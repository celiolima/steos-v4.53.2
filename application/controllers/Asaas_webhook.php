<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Controller Sênior para recepção e processamento de Webhooks do Asaas (API v3).
 * Responsável por validação criptográfica (hash_equals), baixa automática financeira e conciliação de O.S. e Contratos.
 */
class Asaas_webhook extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->config('payment_gateways');
        $this->load->helper('general');
    }

    public function receber()
    {
        $rawPayload = file_get_contents('php://input');
        $payload = json_decode($rawPayload);

        // 1. Validação de Segurança Sênior (hash_equals com Token > 32 caracteres)
        $receivedToken = $this->input->get_request_header('asaas-access-token', true);
        if (empty($receivedToken) && isset($_SERVER['HTTP_ASAAS_ACCESS_TOKEN'])) {
            $receivedToken = $_SERVER['HTTP_ASAAS_ACCESS_TOKEN'];
        }

        $asaasConfig = $this->config->item('payment_gateways')['Asaas'] ?? [];
        $webhookToken = $_ENV['PAYMENT_GATEWAYS_ASAAS_WEBHOOK_TOKEN'] ?? ($asaasConfig['webhook_token'] ?? '');

        // Se o token estiver configurado no servidor, exige validação exata em tempo constante
        if (!empty($webhookToken)) {
            if (empty($receivedToken) || !hash_equals((string) $webhookToken, (string) $receivedToken)) {
                log_message('error', '[Asaas_webhook] Rejeitado: Token de segurança inválido ou ausente.');
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(401)
                    ->set_output(json_encode(['error' => 'Unauthorized token']));
            }
        }

        if (empty($payload) || !isset($payload->event)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['error' => 'Payload inválido ou incompleto']));
        }

        $event = $payload->event;

        // Tratamento de Webhook para NFS-e (Fase 4)
        if (strpos($event, 'INVOICE_') === 0 && isset($payload->invoice)) {
            $invoice = $payload->invoice;
            $invoiceId = $invoice->id ?? null;
            $paymentId = $invoice->payment ?? null; // ID da cobrança que gerou a nota

            if (empty($invoiceId)) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(200)
                    ->set_output(json_encode(['status' => 'Ignored - sem invoice ID']));
            }

            // 1. Tentar encontrar a OS pelo asaas_invoice_id (se ela já tiver sido vinculada antes)
            $os_alvo = $this->db->where('asaas_invoice_id', $invoiceId)->get('os')->row();

            // 2. Se não encontrou pelo invoice ID, tenta encontrar pela cobrança que gerou a nota
            if (!$os_alvo && !empty($paymentId)) {
                $os_alvo = $this->db->where('asaas_payment_id', $paymentId)->get('os')->row();

                if (!$os_alvo) {
                    // Tentar buscar através da tabela de cobrancas caso a O.S não tenha asaas_payment_id direto
                    $cobranca = $this->db->where('charge_id', $paymentId)->get('cobrancas')->row();
                    if ($cobranca && !empty($cobranca->os_id)) {
                        $os_alvo = $this->db->where('idOs', $cobranca->os_id)->get('os')->row();
                    }
                }
            }

            if ($os_alvo) {
                $dadosNfse = [
                    'asaas_invoice_id' => $invoiceId,
                    'asaas_invoice_status' => $invoice->status ?? null,
                    'asaas_invoice_number' => !empty($invoice->number) ? $invoice->number : (!empty($invoice->rpsNumber) ? $invoice->rpsNumber : (!empty($invoice->invoiceNumber) ? $invoice->invoiceNumber : null)),
                    'asaas_invoice_pdf' => $invoice->pdfUrl ?? null,
                    'asaas_invoice_xml' => $invoice->xmlUrl ?? null,
                    'asaas_invoice_error' => $invoice->statusDescription ?? null,
                ];

                $this->db->where('idOs', $os_alvo->idOs)->update('os', $dadosNfse);

                $this->_logWebhookEvent($event, $invoiceId, 'SUCESSO_NFSE', "NFS-e atualizada para a OS {$os_alvo->idOs}. Status: {$dadosNfse['asaas_invoice_status']}");
                
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(200)
                    ->set_output(json_encode(['status' => 'OK', 'message' => "Invoice vinculada à OS {$os_alvo->idOs}"]));
            } else {
                $this->_logWebhookEvent($event, $invoiceId, 'IGNORADO_NFSE', "OS não encontrada para o invoice {$invoiceId} / pagamento {$paymentId}");
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(200)
                    ->set_output(json_encode(['status' => 'OK', 'message' => 'OS não encontrada para invoice']));
            }
        }

        if (!isset($payload->payment)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['error' => 'Payload inválido: sem payment ou invoice']));
        }

        $payment = $payload->payment;
        $chargeId = $payment->id ?? null;

        if (empty($chargeId)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode(['status' => 'Ignored - sem ID de pagamento']));
        }

        // 1.5 Tratamento de PAYMENT_CREATED para Assinaturas (O.S. Preventivas)
        if ($event === 'PAYMENT_CREATED' && !empty($payment->subscription)) {
            $subscriptionId = $payment->subscription;
            $contrato = $this->db->where('asaas_subscription_id', $subscriptionId)->get('contratos')->row();
            
            if ($contrato) {
                // Procurar a próxima O.S. preventiva livre
                $this->db->where('contratos_id', $contrato->idContratos);
                $this->db->where('manutPreventiva', 1);
                $this->db->group_start();
                $this->db->where('asaas_payment_id IS NULL', null, false);
                $this->db->or_where('asaas_payment_id', '');
                $this->db->group_end();
                if (!empty($contrato->data_ativacao_assinatura)) {
                    $this->db->where('dataInicial >=', $contrato->data_ativacao_assinatura . ' 00:00:00');
                }
                $osPreventiva = $this->db->order_by('dataInicial', 'ASC')->get('os', 1)->row();

                if ($osPreventiva) {
                    // Prepara os dados para inserção no Contas a Receber (lancamentos)
                    $this->load->model('Os_model');
                    // Obter o nome do cliente
                    $clienteObj = $this->db->where('idClientes', $osPreventiva->clientes_id)->get('clientes')->row();
                    $nomeCliente = $clienteObj ? $clienteObj->nomeCliente : '(Assinatura Asaas)';

                    $valorTotal = floatval($payment->value);
                    $dataLancamento = [
                        'descricao' => "Fatura de OS - #{$osPreventiva->idOs}",
                        'valor' => $valorTotal,
                        'tipo_desconto' => 'real',
                        'desconto' => 0,
                        'valor_desconto' => $valorTotal,
                        'clientes_id' => $osPreventiva->clientes_id,
                        'vendas_id' => $osPreventiva->idOs,
                        'data_vencimento' => date('Y-m-d', strtotime($payment->dueDate)),
                        'data_pagamento' => null,
                        'baixado' => 0,
                        'cliente_fornecedor' => $nomeCliente,
                        'forma_pgto' => ($payment->billingType === 'PIX') ? 'Pix (Asaas)' : 'Boleto (Asaas)',
                        'tipo' => 'receita',
                        'observacoes' => 'Faturamento automático Asaas via Webhook (Assinaturas).',
                        'usuarios_id' => 1
                    ];

                    $dadosOs = [
                        'asaas_payment_id' => $chargeId,
                        'faturado' => 1,
                        'valorTotal' => $valorTotal,
                        'status' => 'Faturado',
                        'desconto' => 0,
                        'valor_desconto' => $valorTotal
                    ];

                    $this->db->trans_start();

                    // Faturar a O.S (Isso atualiza a O.S e gera o registro no lancamentos)
                    $idLancamento = $this->Os_model->faturarOs($osPreventiva->idOs, $dataLancamento, $dadosOs);
                    
                    // Adiciona a cobrança correspondente localmente
                    // Não chamamos a API pois a cobrança já nasceu lá!
                    $this->load->helper('asaas');
                    $this->db->insert('cobrancas', [
                        'charge_id' => $chargeId,
                        'os_id' => $osPreventiva->idOs,
                        'clientes_id' => $osPreventiva->clientes_id,
                        'vendas_id' => null,
                        'payment_url' => $payment->invoiceUrl ?? '',
                        'link' => $payment->invoiceUrl ?? '',
                        'pdf' => $payment->bankSlipUrl ?? '',
                        'expire_at' => date('Y-m-d', strtotime($payment->dueDate)),
                        'status' => 'PENDING',
                        'total' => getMoneyAsCents($valorTotal),
                        'payment' => ($payment->billingType === 'PIX') ? 'PIX' : 'BOLETO',
                        'payment_method' => ($payment->billingType === 'PIX') ? 'pix' : 'boleto',
                        'payment_gateway' => 'Asaas',
                        'barcode' => '',
                        'message' => "OS #{$osPreventiva->idOs} - Assinatura"
                    ]);

                    $this->db->trans_complete();

                    if ($this->db->trans_status() === FALSE) {
                        $this->_logWebhookEvent($event, $chargeId, 'ERRO', 'Falha no banco de dados ao faturar a OS.');
                        return $this->output->set_status_header(500)->set_output(json_encode(['error' => 'Database error']));
                    }

                    $this->_logWebhookEvent($event, $chargeId, 'SUCESSO', "Cobrança vinculada à OS {$osPreventiva->idOs} e faturada.");
                    log_message('info', "[Asaas_webhook] Assinatura {$subscriptionId}: Cobrança {$chargeId} vinculada à OS {$osPreventiva->idOs} e faturada.");
                    
                    return $this->output
                        ->set_content_type('application/json')
                        ->set_status_header(200)
                        ->set_output(json_encode(['status' => 'OK', 'message' => 'Matched to OS']));
                } else {
                    $this->_logWebhookEvent($event, $chargeId, 'ERRO', "Nenhuma OS Preventiva Livre encontrada para o Contrato #{$contrato->idContratos}");
                    // Força 500 para o Asaas tentar de novo
                    return $this->output->set_status_header(500)->set_output(json_encode(['error' => 'No free preventive OS found for subscription']));
                }
            } else {
                $this->_logWebhookEvent($event, $chargeId, 'IGNORADO', "Contrato da assinatura {$subscriptionId} não encontrado no sistema.");
            }
            
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode(['status' => 'OK']));
        }

        // 2. Busca a cobrança no banco do STEOS
        $cobranca = $this->db->where('charge_id', $chargeId)->get('cobrancas')->row();

        // 3. Processamento de Baixa Automática (PAYMENT_RECEIVED / PAYMENT_CONFIRMED / PAYMENT_RECEIVED_IN_CASH)
        if (in_array($event, ['PAYMENT_RECEIVED', 'PAYMENT_CONFIRMED', 'PAYMENT_DUNNING_RECEIVED', 'PAYMENT_RECEIVED_IN_CASH'])) {
            if ($cobranca) {
                $statusCobranca = !empty($payment->status) ? $payment->status : ($event === 'PAYMENT_CONFIRMED' ? 'CONFIRMED' : 'RECEIVED');
                $valorReais = round($cobranca->total / 100, 2);
                if ($valorReais <= 0 && isset($payment->value)) {
                    $valorReais = floatval($payment->value);
                }
                $dataPagamento = !empty($payment->paymentDate) ? date('Y-m-d', strtotime($payment->paymentDate)) : (!empty($payment->clientPaymentDate) ? date('Y-m-d', strtotime($payment->clientPaymentDate)) : date('Y-m-d'));

                self::executarBaixaEConciliacao($cobranca, $statusCobranca, $valorReais, $dataPagamento);
            }
        } elseif (in_array($event, ['PAYMENT_OVERDUE'])) {
            if ($cobranca) {
                self::executarBaixaEConciliacao($cobranca, 'OVERDUE', 0, null);
            }
        } elseif (in_array($event, ['PAYMENT_DELETED', 'PAYMENT_REFUNDED', 'PAYMENT_RESTORED'])) {
            if ($cobranca) {
                $statusDestino = ($event === 'PAYMENT_REFUNDED') ? 'REFUNDED' : (($event === 'PAYMENT_RESTORED') ? 'PENDING' : 'DELETED');
                self::executarBaixaEConciliacao($cobranca, $statusDestino, 0, null);
            }
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode(['status' => 'OK', 'processed_event' => $event]));
    }

    /**
     * Motor Sênior de Baixa e Conciliação Financeira para Cobranças Asaas.
     * Pode ser chamado tanto via Webhook (quando o Asaas envia notificação automática)
     * quanto via Sincronização Manual / API (quando o usuário clica em Atualizar no STEOS).
     */
    public static function executarBaixaEConciliacao($cobranca, $statusCobranca, $valorReais, $dataPagamento)
    {
        $ci = &get_instance();
        
        // 1. Atualiza status na tabela cobrancas
        $ci->db->where('idCobranca', $cobranca->idCobranca)->update('cobrancas', [
            'status' => $statusCobranca
        ]);

        // 2. Se for status de pagamento confirmado/recebido, executa a baixa
        $statusPagos = ['CONFIRMED', 'RECEIVED', 'RECEIVED_IN_CASH', 'DUNNING_RECEIVED'];
        if (in_array(strtoupper($statusCobranca), $statusPagos)) {
            
            // Baixa da Ordem de Serviço (caso atrelada)
            if (!empty($cobranca->os_id)) {
                $ci->db->where('idOs', $cobranca->os_id)->update('os', [
                    'status' => 'Faturado',
                    'afaturar' => 0,
                    'faturado' => 1
                ]);
            }

            if (!empty($cobranca->vendas_id)) {
                $ci->db->where('idVendas', $cobranca->vendas_id)->update('vendas', [
                    'status' => 'Faturado',
                    'faturado' => 1
                ]);
            }

            $lancamentoExistente = null;
            $chargeId = $cobranca->charge_id ?? null;

            // Tentativa A: Pelo charge_id do Asaas ou ID da Cobrança exato nas descrições/observações
            if (!empty($chargeId)) {
                $ci->db->where('baixado', 0)
                       ->group_start()
                       ->like('descricao', $chargeId)
                       ->or_like('observacoes', $chargeId)
                       ->group_end();
                $lancamentoExistente = $ci->db->order_by('idLancamentos', 'DESC')->get('lancamentos')->row();
            }

            // Tentativa B: Se tem OS atrelada, busca pelas variações completas de descrição e valor
            if (!$lancamentoExistente && !empty($cobranca->os_id)) {
                $osId = (int) $cobranca->os_id;
                
                // Primeiro tenta casar OS ID + valor aproximado (+- 0.05 para compensar arredondamentos de juros/descontos)
                $ci->db->where('baixado', 0)
                       ->group_start()
                       ->like('descricao', "OS Nº: {$osId}")
                       ->or_like('descricao', "OS - #{$osId}")
                       ->or_like('descricao', "OS #{$osId}")
                       ->or_like('descricao', "Fatura de OS - #{$osId}")
                       ->or_like('descricao', "Fatura de OS Nº: {$osId}")
                       ->or_like('descricao', "Venda/OS #{$osId}")
                       ->or_like('observacoes', "OS Nº: {$osId}")
                       ->or_like('observacoes', "OS #{$osId}")
                       ->or_like('observacoes', "OS - #{$osId}")
                       ->group_end()
                       ->where('valor >=', $valorReais - 0.05)
                       ->where('valor <=', $valorReais + 0.05);
                $lancamentoExistente = $ci->db->order_by('data_vencimento', 'ASC')->order_by('idLancamentos', 'ASC')->get('lancamentos')->row();

                // Se não casou por valor exato, pega a parcela mais antiga em aberto para essa OS
                if (!$lancamentoExistente) {
                    $ci->db->where('baixado', 0)
                           ->group_start()
                           ->like('descricao', "OS Nº: {$osId}")
                           ->or_like('descricao', "OS - #{$osId}")
                           ->or_like('descricao', "OS #{$osId}")
                           ->or_like('descricao', "Fatura de OS - #{$osId}")
                           ->or_like('descricao', "Fatura de OS Nº: {$osId}")
                           ->or_like('descricao', "Venda/OS #{$osId}")
                           ->or_like('observacoes', "OS Nº: {$osId}")
                           ->or_like('observacoes', "OS #{$osId}")
                           ->or_like('observacoes', "OS - #{$osId}")
                           ->group_end();
                    $lancamentoExistente = $ci->db->order_by('data_vencimento', 'ASC')->order_by('idLancamentos', 'ASC')->get('lancamentos')->row();
                }
            }

            // Tentativa C: Se tem Venda atrelada, busca pelas variações completas de descrição e valor
            if (!$lancamentoExistente && !empty($cobranca->vendas_id)) {
                $vendaId = (int) $cobranca->vendas_id;
                
                $ci->db->where('baixado', 0)
                       ->group_start()
                       ->like('descricao', "Venda Nº: {$vendaId}")
                       ->or_like('descricao', "Venda - #{$vendaId}")
                       ->or_like('descricao', "Venda #{$vendaId}")
                       ->or_like('descricao', "Fatura de Venda - #{$vendaId}")
                       ->or_like('descricao', "Fatura de Venda Nº: {$vendaId}")
                       ->or_like('descricao', "Venda/OS #{$vendaId}")
                       ->or_like('observacoes', "Venda Nº: {$vendaId}")
                       ->or_like('observacoes', "Venda #{$vendaId}")
                       ->group_end()
                       ->where('valor >=', $valorReais - 0.05)
                       ->where('valor <=', $valorReais + 0.05);
                $lancamentoExistente = $ci->db->order_by('data_vencimento', 'ASC')->order_by('idLancamentos', 'ASC')->get('lancamentos')->row();

                if (!$lancamentoExistente) {
                    $ci->db->where('baixado', 0)
                           ->group_start()
                           ->like('descricao', "Venda Nº: {$vendaId}")
                           ->or_like('descricao', "Venda - #{$vendaId}")
                           ->or_like('descricao', "Venda #{$vendaId}")
                           ->or_like('descricao', "Fatura de Venda - #{$vendaId}")
                           ->or_like('descricao', "Fatura de Venda Nº: {$vendaId}")
                           ->or_like('descricao', "Venda/OS #{$vendaId}")
                           ->or_like('observacoes', "Venda Nº: {$vendaId}")
                           ->or_like('observacoes', "Venda #{$vendaId}")
                           ->group_end();
                    $lancamentoExistente = $ci->db->order_by('data_vencimento', 'ASC')->order_by('idLancamentos', 'ASC')->get('lancamentos')->row();
                }
            }

            // Tentativa D: Se não achou por OS nem Venda, busca por Cliente ID + Valor na forma de pagamento Asaas em aberto
            if (!$lancamentoExistente && !empty($cobranca->clientes_id)) {
                $ci->db->where('clientes_id', $cobranca->clientes_id)
                       ->where('baixado', 0)
                       ->where('valor >=', $valorReais - 0.05)
                       ->where('valor <=', $valorReais + 0.05)
                       ->group_start()
                       ->like('forma_pgto', 'Asaas')
                       ->or_like('forma_pgto', 'Boleto')
                       ->or_like('forma_pgto', 'Pix')
                       ->group_end();
                $lancamentoExistente = $ci->db->order_by('data_vencimento', 'ASC')->order_by('idLancamentos', 'ASC')->get('lancamentos')->row();
            }

            // Se encontrou o lançamento aberto correspondente, executa a baixa nele!
            if ($lancamentoExistente) {
                $ci->db->where('idLancamentos', $lancamentoExistente->idLancamentos)->update('lancamentos', [
                    'baixado' => 1,
                    'data_pagamento' => $dataPagamento
                ]);
                if (function_exists('log_info')) {
                    log_info("Conciliação Asaas executada - Lançamento #{$lancamentoExistente->idLancamentos} baixado (Cobrança #{$cobranca->idCobranca})");
                }
            } else {
                // Caso raríssimo em que o lançamento não existe no financeiro, cria-se um já baixado
                $cliente = $ci->db->where('idClientes', $cobranca->clientes_id)->get('clientes')->row();
                $ci->db->insert('lancamentos', [
                    'descricao' => 'Fatura Asaas (' . (!empty($cobranca->message) ? $cobranca->message : $chargeId) . ')',
                    'valor' => $valorReais,
                    'data_vencimento' => $cobranca->expire_at ?: date('Y-m-d'),
                    'data_pagamento' => $dataPagamento,
                    'baixado' => 1,
                    'cliente_fornecedor' => $cliente ? $cliente->nomeCliente : 'Cliente Asaas',
                    'clientes_id' => $cobranca->clientes_id,
                    'forma_pgto' => 'Boleto/Pix (Asaas)',
                    'tipo' => 'receita',
                    'observacoes' => 'Baixa automática/Sincronização Asaas (' . $chargeId . ')',
                    'usuarios_id' => 1
                ]);
                if (function_exists('log_info')) {
                    log_info("Conciliação Asaas - Lançamento criado e baixado (Cobrança #{$cobranca->idCobranca})");
                }
            }
        } elseif (in_array(strtoupper($statusCobranca), ['OVERDUE'])) {
            $ci->db->where('idCobranca', $cobranca->idCobranca)->update('cobrancas', ['status' => 'OVERDUE']);
        } elseif (in_array(strtoupper($statusCobranca), ['DELETED', 'REFUNDED', 'RESTORED'])) {
            $statusDestino = (strtoupper($statusCobranca) === 'REFUNDED') ? 'REFUNDED' : ((strtoupper($statusCobranca) === 'RESTORED') ? 'PENDING' : 'DELETED');
            $ci->db->where('idCobranca', $cobranca->idCobranca)->update('cobrancas', ['status' => $statusDestino]);
            // Se foi estornado ou cancelado, podemos reabrir o lançamento financeiro caso esteja baixado
            if (in_array($statusDestino, ['REFUNDED', 'DELETED']) && !empty($cobranca->charge_id)) {
                $ci->db->where('baixado', 1)
                       ->group_start()
                       ->like('descricao', $cobranca->charge_id)
                       ->or_like('observacoes', $cobranca->charge_id)
                       ->group_end()
                       ->update('lancamentos', ['baixado' => 0, 'data_pagamento' => null]);
            }
        }
    }

    private function _logWebhookEvent($event, $paymentId, $status, $errorMessage = null) {
        $this->db->insert('asaas_webhooks_logs', [
            'event' => $event,
            'asaas_payment_id' => $paymentId,
            'status' => $status,
            'mensagem_erro' => $errorMessage,
            'data_recebimento' => date('Y-m-d H:i:s')
        ]);
        return $this->db->insert_id();
    }
}

