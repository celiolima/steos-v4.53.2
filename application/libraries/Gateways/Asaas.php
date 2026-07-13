<?php

use CodePhix\Asaas\Asaas as AsaasSdk;
use Libraries\Gateways\BasePaymentGateway;
use Libraries\Gateways\Contracts\PaymentGateway;

class Asaas extends BasePaymentGateway
{
    /** @var AsaasSdk */
    private $asaasApi;

    private $asaasConfig;

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->config('payment_gateways');
        $this->ci->load->model('Os_model');
        $this->ci->load->model('vendas_model');
        $this->ci->load->model('cobrancas_model');
        $this->ci->load->model('steos_model');
        $this->ci->load->model('email_model');
        $this->ci->load->model('clientes_model');

        $asaasConfig = $this->ci->config->item('payment_gateways')['Asaas'];
        $this->asaasConfig = $asaasConfig;
        $this->ci->load->helper('asaas');
        $this->asaasApi = new AsaasSdk(
            $asaasConfig['credentials']['api_key'],
            $asaasConfig['production'] === true ? 'producao' : 'homologacao'
        );
    }

    public function isConfigured()
    {
        return !empty($this->asaasConfig['credentials']['api_key']);
    }

    public function cancelar($id)
    {
        $cobranca = $this->ci->cobrancas_model->getById($id);
        if (! $cobranca) {
            throw new \Exception('Cobrança não existe!');
        }

        if ($cobranca->payment_method == 'boleto') {
            $this->asaasApi->Cobranca()->delete($cobranca->charge_id);
        } else {
            $this->asaasApi->LinkPagamento()->delete($cobranca->charge_id);
        }

        return $this->atualizarDados($id);
    }

    public function enviarPorEmail($id)
    {
        $cobranca = $this->ci->cobrancas_model->getById($id);
        if (! $cobranca) {
            throw new \Exception('Cobrança não existe!');
        }

        $emitente = $this->ci->steos_model->getEmitente();
        if (! $emitente) {
            throw new \Exception('Emitente não configurado!');
        }

        $html = $this->ci->load->view(
            'cobrancas/emails/cobranca',
            [
                'cobranca' => $cobranca,
                'emitente' => $emitente,
                'paymentGatewaysConfig' => $this->ci->config->item('payment_gateways'),
            ],
            true
        );

        $assunto = 'Cobrança - ' . $emitente->nome;
        if ($cobranca->os_id) {
            $assunto .= ' - OS #' . $cobranca->os_id;
        } else {
            $assunto .= ' - Venda #' . $cobranca->vendas_id;
        }

        $remetentes = [$cobranca->email];
        foreach ($remetentes as $remetente) {
            $headers = [
                'From' => $emitente->email,
                'Subject' => $assunto,
                'Return-Path' => '',
            ];
            $email = [
                'to' => $remetente,
                'message' => $html,
                'status' => 'pending',
                'date' => date('Y-m-d H:i:s'),
                'headers' => json_encode($headers),
            ];
            $this->ci->email_model->add('email_queue', $email);
        }
    }

    public function atualizarDados($id)
    {
        $cobranca = $this->ci->cobrancas_model->getById($id);
        if (! $cobranca) {
            throw new \Exception('Cobrança não existe!');
        }

        // Requisição v3 nativa, suporta Boletos, Pix e Cartão (sem limitação legada do SDK)
        $this->ci->load->helper('asaas');
        $result = asaas_api_request('/v3/payments/' . $cobranca->charge_id, 'GET');

        // Verifica se a cobrança já foi deletada
        if (!empty($result->deleted) && $result->deleted == true) {
            $result->status = 'DELETED';
        }

        // Importa e executa o motor sênior de baixa e conciliação (atualiza cobrancas, os, vendas e lancamentos)
        if (file_exists(APPPATH . 'controllers/Asaas_webhook.php')) {
            require_once APPPATH . 'controllers/Asaas_webhook.php';
            if (class_exists('Asaas_webhook') && method_exists('Asaas_webhook', 'executarBaixaEConciliacao')) {
                $valorReais = round($cobranca->total / 100, 2);
                if ($valorReais <= 0 && isset($result->value)) {
                    $valorReais = floatval($result->value);
                }
                $dataPagamento = !empty($result->paymentDate) ? date('Y-m-d', strtotime($result->paymentDate)) : (!empty($result->clientPaymentDate) ? date('Y-m-d', strtotime($result->clientPaymentDate)) : date('Y-m-d'));
                
                \Asaas_webhook::executarBaixaEConciliacao($cobranca, $result->status, $valorReais, $dataPagamento);
            } else {
                $this->ci->cobrancas_model->edit('cobrancas', ['status' => $result->status], 'idCobranca', $id);
            }
        } else {
            $this->ci->cobrancas_model->edit('cobrancas', ['status' => $result->status], 'idCobranca', $id);
        }

        $this->ci->session->set_flashdata('success', 'Cobrança atualizada com sucesso!');
        log_info('Alterou um status e conciliou cobrança. ID' . $id);
        return true;
    }

    public function confirmarPagamento($id)
    {
        $cobranca = $this->ci->cobrancas_model->getById($id);
        if (! $cobranca) {
            throw new \Exception('Cobrança não existe!');
        }

        $this->ci->load->helper('asaas');
        $payload = [
            'paymentDate' => (new DateTime())->format('Y-m-d'),
            'value' => round($cobranca->total / 100, 2),
        ];

        asaas_api_request('/v3/payments/' . $cobranca->charge_id . '/receiveInCash', 'POST', $payload);

        return $this->atualizarDados($id);
    }

    protected function gerarCobrancaBoleto($id, $tipo, $data = [])
    {
        $entity = $this->findEntity($id, $tipo);
        $produtos = $tipo === PaymentGateway::PAYMENT_TYPE_OS
            ? $this->ci->Os_model->getProdutos($id)
            : $this->ci->vendas_model->getProdutos($id);
        $servicos = $tipo === PaymentGateway::PAYMENT_TYPE_OS
            ? $this->ci->Os_model->getServicos($id)
            : [];

        $desconto = [$tipo === PaymentGateway::PAYMENT_TYPE_OS
            ? $this->ci->Os_model->getById($id)
            : $this->ci->vendas_model->getById($id)];

        $tipo_desconto = [$tipo === PaymentGateway::PAYMENT_TYPE_OS
            ? $this->ci->Os_model->getById($id)
            : $this->ci->vendas_model->getById($id)];

        $totalProdutos = array_reduce(
            $produtos,
            function ($total, $item) {
                return $total + (floatval($item->preco) * intval($item->quantidade));
            },
            0
        );
        $totalServicos = array_reduce(
            $servicos,
            function ($total, $item) {
                return $total + (floatval($item->preco) * intval($item->quantidade));
            },
            0
        );
        $tipoDesconto = array_reduce(
            $tipo_desconto,
            function ($total, $item) {
                return $item->tipo_desconto;
            },
            0
        );
        $totalDesconto = array_reduce(
            $desconto,
            function ($total, $item) {
                return $item->desconto;
            },
            0
        );

        if (empty($entity)) {
            throw new \Exception('OS ou venda não existe!');
        }

        if (($totalProdutos + $totalServicos) <= 0 && empty($data['valor'])) {
            throw new \Exception('OS ou venda com valor negativo ou zero!');
        }

        if ($err = $this->errosCadastro($entity)) {
            throw new \Exception($err);
        }

        if (!empty($data['vencimento'])) {
            $expirationDate = $data['vencimento'];
        } else {
            $expirationDate = (new DateTime())->add(new DateInterval($this->asaasConfig['boleto_expiration']));
            $expirationDate = ($expirationDate->format('Y-m-d'));
        }

        if (!empty($data['valor']) && floatval($data['valor']) > 0) {
            $valorCobrar = floatval($data['valor']);
        } else {
            $valorCobrar = $this->valorTotal($totalProdutos, $totalServicos, $totalDesconto, $tipoDesconto);
        }

        $billingType = 'BOLETO';
        if (!empty($data['forma_pagamento']) && (stripos($data['forma_pagamento'], 'pix') !== false || $data['forma_pagamento'] === 'PIX')) {
            $billingType = 'PIX';
        }

        $description = !empty($data['descricao']) ? $data['descricao'] : ($tipo === PaymentGateway::PAYMENT_TYPE_OS ? "OS #$id" : "Venda #$id");

        $body = [
            'customer' => $this->criarOuRetornarClienteAsaasId($entity->clientes_id),
            'billingType' => $billingType,
            'dueDate' => $expirationDate,
            'value' => $valorCobrar,
            'description' => $description,
            'externalReference' => $id,
            'postalService' => false,
        ];

        if (!empty($data['installmentCount']) && intval($data['installmentCount']) > 1) {
            $body['installmentCount'] = intval($data['installmentCount']);
            $body['installmentValue'] = !empty($data['installmentValue']) ? floatval($data['installmentValue']) : ($valorCobrar / intval($data['installmentCount']));
        }

        $result = $this->asaasApi->Cobranca()->create($body);

        if ($result && ! empty($result->errors)) {
            // A resposta da API inclui erros
            foreach ($result->errors as $error) {
                throw new \Exception('Erro na criação da cobrança: ' . $error->description);
            }
        } elseif (! $result) {
            // A chamada para a API falhou de alguma forma
            throw new \Exception('Falha na chamada para a API Asaas');
        }

        $title = $description;
        $dataCobranca = [
            'barcode' => '',
            'link' => $result->invoiceUrl,
            'payment_url' => $result->invoiceUrl,
            'pdf' => $result->bankSlipUrl,
            'expire_at' => $result->dueDate,
            'charge_id' => $result->id,
            'status' => $result->status,
            'total' => getMoneyAsCents($valorCobrar),
            'payment' => $result->billingType,
            'clientes_id' => $entity->idClientes,
            'payment_method' => strtolower($billingType),
            'payment_gateway' => 'Asaas',
            'message' => 'Pagamento referente a ' . $title,
        ];

        if (!empty($data['lancamentos_id'])) {
            $dataCobranca['message'] = $description;
        }

        if ($tipo === PaymentGateway::PAYMENT_TYPE_OS) {
            $dataCobranca['os_id'] = $id;
        } else {
            $dataCobranca['vendas_id'] = $id;
        }

        if ($idCobranca = $this->ci->cobrancas_model->add('cobrancas', $dataCobranca, true)) {
            $dataCobranca['idCobranca'] = $idCobranca;
            log_info('Cobrança criada com successo. ID: ' . $result->id);
        } else {
            throw new \Exception('Erro ao salvar cobrança!');
        }

        return $dataCobranca;
    }

    protected function gerarCobrancaLink($id, $tipo, $data = [])
    {
        $entity = $this->findEntity($id, $tipo);
        $produtos = $tipo === PaymentGateway::PAYMENT_TYPE_OS
            ? $this->ci->Os_model->getProdutos($id)
            : $this->ci->vendas_model->getProdutos($id);
        $servicos = $tipo === PaymentGateway::PAYMENT_TYPE_OS
            ? $this->ci->Os_model->getServicos($id)
            : [];
        $tipo_desconto = [$tipo === PaymentGateway::PAYMENT_TYPE_OS
            ? $this->ci->Os_model->getById($id)
            : $this->ci->vendas_model->getById($id)];

        $desconto = [$tipo === PaymentGateway::PAYMENT_TYPE_OS
            ? $this->ci->Os_model->getById($id)
            : $this->ci->vendas_model->getById($id)];

        $totalProdutos = array_reduce(
            $produtos,
            function ($total, $item) {
                return $total + (floatval($item->preco) * intval($item->quantidade));
            },
            0
        );
        $totalServicos = array_reduce(
            $servicos,
            function ($total, $item) {
                return $total + (floatval($item->preco) * intval($item->quantidade));
            },
            0
        );
        $tipoDesconto = array_reduce(
            $tipo_desconto,
            function ($total, $item) {
                return $item->tipo_desconto;
            },
            0
        );
        $totalDesconto = array_reduce(
            $desconto,
            function ($total, $item) {
                return $item->desconto;
            },
            0
        );

        if (empty($entity)) {
            throw new \Exception('OS ou venda não existe!');
        }

        if (($totalProdutos + $totalServicos) <= 0) {
            throw new \Exception('OS ou venda com valor negativo ou zero!');
        }

        if ($err = $this->errosCadastro($entity)) {
            throw new \Exception($err);
        }

        $expirationDate = (new DateTime())->add(new DateInterval($this->asaasConfig['boleto_expiration']));
        $expirationDate = ($expirationDate->format('Y-m-d'));
        $body = [
            'name' => $tipo === PaymentGateway::PAYMENT_TYPE_OS ? "OS #$id" : "Venda #$id",
            'description' => $tipo === PaymentGateway::PAYMENT_TYPE_OS ? "OS #$id" : "Venda #$id",
            'endDate' => $expirationDate,
            'value' => $this->valorTotal($totalProdutos, $totalServicos, $totalDesconto, $tipoDesconto),
            'billingType' => 'UNDEFINED',
            'chargeType' => 'DETACHED',
            'dueDateLimitDays' => preg_replace('/[^0-9]/', '', $this->asaasConfig['boleto_expiration']),
            'subscriptionCycle' => null,
            'maxInstallmentCount' => 1,
        ];

        $result = $this->asaasApi->LinkPagamento()->create($body);
        if ($result && ! empty($result->errors)) {
            // A resposta da API inclui erros
            foreach ($result->errors as $error) {
                throw new \Exception('Erro na criação da cobrança: ' . $error->description);
            }
        } elseif (! $result) {
            // A chamada para a API falhou de alguma forma
            throw new \Exception('Falha na chamada para a API Asaas');
        }

        $title = $tipo === PaymentGateway::PAYMENT_TYPE_OS ? "OS #$id" : "Venda #$id";
        $data = [
            'expire_at' => $result->endDate,
            'charge_id' => $result->id,
            'status' => 'PENDING',
            'total' => getMoneyAsCents($this->valorTotal($totalProdutos, $totalServicos, $totalDesconto, $tipoDesconto)),
            'clientes_id' => $entity->idClientes,
            'payment_method' => 'link',
            'payment_gateway' => 'Asaas',
            'payment_url' => $result->url,
            'link' => $result->url,
            'message' => $result->description,
            'message' => 'Pagamento referente a ' . $title,
        ];

        if ($tipo === PaymentGateway::PAYMENT_TYPE_OS) {
            $data['os_id'] = $id;
        } else {
            $data['vendas_id'] = $id;
        }

        if ($id = $this->ci->cobrancas_model->add('cobrancas', $data, true)) {
            $data['idCobranca'] = $id;
            log_info('Cobrança criada com successo. ID: ' . $result->id);
        } else {
            throw new \Exception('Erro ao salvar cobrança!');
        }

        return $data;
    }

    private function valorTotal($produtosValor, $servicosValor, $desconto, $tipo_desconto)
    {
        if ($tipo_desconto == 'porcento') {
            $def_desconto = $desconto * ($produtosValor + $servicosValor) / 100;
        } elseif ($tipo_desconto == 'real') {
            $def_desconto = $desconto;
        } else {
            $def_desconto = 0;
        }

        return ($produtosValor + $servicosValor) - $def_desconto;
    }

    private function criarOuRetornarClienteAsaasId($clienteId)
    {
        $cliente = (array) $this->ci->clientes_model->getById($clienteId);
        if (! $cliente) {
            throw new Exception('Cliente não encontrado: ' . $clienteId);
        }

        if (!empty($cliente['asaas_id'])) {
            return $cliente['asaas_id'];
        }

        $this->ci->load->helper('asaas');
        $docLimpo = preg_replace('/[^0-9]/', '', $cliente['documento'] ?? '');

        // Fase 1.2: Consulta inteligente anti-duplicidade antes do POST
        if (!empty($docLimpo)) {
            try {
                $busca = asaas_api_request('/v3/customers?cpfCnpj=' . $docLimpo, 'GET');
                if (!empty($busca->data) && count($busca->data) > 0) {
                    $asaasIdExistente = $busca->data[0]->id;
                    $this->ci->clientes_model->edit(
                        'clientes',
                        ['asaas_id' => $asaasIdExistente],
                        'idClientes',
                        $clienteId
                    );
                    return $asaasIdExistente;
                }
            } catch (\Exception $e) {
                log_message('error', 'Falha ao buscar cliente por CPF/CNPJ no Asaas: ' . $e->getMessage());
            }
        }

        $body = [
            'name' => $cliente['nomeCliente'],
            'email' => $cliente['email'],
            'phone' => preg_replace('/[^0-9]/', '', $cliente['telefone'] ?? ''),
            'mobilePhone' => preg_replace('/[^0-9]/', '', $cliente['celular'] ?? ''),
            'cpfCnpj' => $docLimpo,
            'postalCode' => $cliente['cep'] ?? '',
            'address' => $cliente['rua'] ?? '',
            'addressNumber' => $cliente['numero'] ?? '',
            'complement' => $cliente['complemento'] ?? '',
            'province' => $cliente['bairro'] ?? '',
            'city' => $cliente['cidade'] ?? '',
            'state' => $cliente['estado'] ?? '',
            'country' => 'Brasil',
            'externalReference' => (string) $clienteId,
            'notificationDisabled' => $this->asaasConfig['notify'] === false,
            'observations' => '',
            'groupName' => 'steos',
        ];

        $result = asaas_api_request('/v3/customers', 'POST', $body);

        if (!empty($result->id)) {
            $this->ci->clientes_model->edit(
                'clientes',
                ['asaas_id' => $result->id],
                'idClientes',
                $clienteId
            );
            return $result->id;
        }

        throw new Exception('Erro ao criar ou vincular ID do cliente no Asaas!');
    }

    /**
     * Fase 1.3: QR Code Pix Instantâneo e Copia-e-Cola
     */
    public function getPixQrCode($chargeId)
    {
        try {
            $this->ci->load->helper('asaas');
            $response = asaas_api_request('/v3/payments/' . $chargeId . '/pixQrCode', 'GET');
            return [
                'success' => true,
                'encodedImage' => $response->encodedImage ?? null,
                'payload' => $response->payload ?? null,
                'expirationDate' => $response->expirationDate ?? null,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
