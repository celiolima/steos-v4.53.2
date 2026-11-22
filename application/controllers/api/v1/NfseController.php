<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class NfseController extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('asaas');
        $this->load->model('os_model');
        $this->load->model('servicos_nfse_model');
    }

    private function canViewNfse($level)
    {
        return $this->permission->checkPermission($level, 'vNfse');
    }

    public function index_get($idOs = '')
    {
        $this->logged_user();
        if (! $this->canViewNfse($this->logged_user()->level)) {
            $this->response([
                'status' => false,
                'message' => 'Você não está autorizado a Visualizar NFS-e.',
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        if ($idOs && is_numeric($idOs)) {
            $os = $this->os_model->getById($idOs);
            if (! $os) {
                $this->response([
                    'status' => false,
                    'message' => 'Ordem de Serviço não encontrada.',
                ], REST_Controller::HTTP_NOT_FOUND);
            }

            unset($os->senha);
            $this->response([
                'status' => true,
                'message' => 'Dados da NFS-e da O.S.',
                'result' => [
                    'idOs' => $os->idOs,
                    'clientes_id' => $os->clientes_id,
                    'nomeCliente' => $os->nomeCliente ?? null,
                    'valorTotal' => $os->valorTotal,
                    'asaas_payment_id' => $os->asaas_payment_id ?? null,
                    'asaas_invoice_id' => $os->asaas_invoice_id ?? null,
                    'asaas_invoice_status' => $os->asaas_invoice_status ?? null,
                    'asaas_invoice_number' => $os->asaas_invoice_number ?? null,
                    'asaas_invoice_pdf' => $os->asaas_invoice_pdf ?? null,
                    'asaas_invoice_xml' => $os->asaas_invoice_xml ?? null,
                    'asaas_invoice_error' => $os->asaas_invoice_error ?? null,
                ],
            ], REST_Controller::HTTP_OK);
        }

        $pesquisa = trim((string) $this->get('search', true));
        $status = trim((string) $this->get('status', true));
        $dataDe = trim((string) $this->get('data_de', true));
        $dataAte = trim((string) $this->get('data_ate', true));

        $perPage = (int) ($this->get('perPage', true) ?: 20);
        $page = max(0, (int) $this->get('page', true));
        $start = $page * $perPage;

        $this->db->select('os.idOs, os.dataInicial, os.dataFinal, os.valorTotal, os.clientes_id, os.asaas_payment_id, os.asaas_invoice_id, os.asaas_invoice_status, os.asaas_invoice_number, os.asaas_invoice_pdf, os.asaas_invoice_xml, os.asaas_invoice_error, clientes.nomeCliente, clientes.documento');
        $this->db->from('os');
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id', 'left');
        $this->db->where('os.asaas_invoice_status IS NOT NULL', null, false);
        $this->db->where('os.asaas_invoice_status !=', '');

        if ($pesquisa !== '') {
            $this->db->group_start();
            $this->db->like('clientes.nomeCliente', $pesquisa);
            $this->db->or_like('clientes.documento', $pesquisa);
            $this->db->or_like('os.asaas_invoice_number', $pesquisa);
            if (is_numeric($pesquisa)) {
                $this->db->or_where('os.idOs', (int) $pesquisa);
            }
            $this->db->group_end();
        }

        if ($status !== '' && $status !== 'Todos') {
            $this->db->where('os.asaas_invoice_status', $status);
        }

        if ($dataDe !== '') {
            $this->db->where('os.dataInicial >=', $dataDe);
        }
        if ($dataAte !== '') {
            $this->db->where('os.dataInicial <=', $dataAte);
        }

        $this->db->order_by('os.idOs', 'DESC');
        $this->db->limit($perPage, $start);
        $results = $this->db->get()->result();

        $this->response([
            'status' => true,
            'message' => 'Listando NFS-e',
            'result' => $results,
        ], REST_Controller::HTTP_OK);
    }

    public function servicos_get()
    {
        $this->logged_user();
        if (! $this->permission->checkPermission($this->logged_user()->level, 'vNfse')) {
            $this->response([
                'status' => false,
                'message' => 'Você não está autorizado a Visualizar Serviços NFS-e.',
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        $servicos = $this->servicos_nfse_model->get('servicos_nfse', '*');

        $this->response([
            'status' => true,
            'message' => 'Serviços NFS-e e tabela oficial de Indicadores de Operação',
            'result' => [
                'servicos_nfse' => $servicos,
                'operation_indicator_codes' => asaas_operation_indicator_codes(),
            ],
        ], REST_Controller::HTTP_OK);
    }

    public function emitir_post($idOs = null)
    {
        $this->logged_user();
        if (! $this->permission->checkPermission($this->logged_user()->level, 'aNfse')) {
            $this->response([
                'status' => false,
                'message' => 'Você não está autorizado a Emitir NFS-e.',
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (! is_array($input)) {
            $input = $_POST;
        }

        $idOs = $idOs ?: ($input['os_id'] ?? null);
        if (! $idOs || ! is_numeric($idOs)) {
            $this->response([
                'status' => false,
                'message' => 'Informe o ID da Ordem de Serviço.',
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $os = $this->os_model->getById($idOs);
        if (! $os) {
            $this->response([
                'status' => false,
                'message' => 'Ordem de Serviço não encontrada.',
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        $servicoPadrao = null;
        if (! empty($input['servico_nfse_id']) && is_numeric($input['servico_nfse_id'])) {
            $servicoPadrao = $this->servicos_nfse_model->getById((int) $input['servico_nfse_id']);
        }

        $municipalServiceCodeRaw = trim((string) ($input['municipal_service_code'] ?? ($servicoPadrao->codigo_servico_municipal ?? '')));
        $municipalServiceNameInput = trim((string) ($input['municipal_service_name'] ?? $municipalServiceCodeRaw));
        $codigoNbsRaw = trim((string) ($input['codigo_nbs'] ?? ($servicoPadrao->codigo_nbs ?? '')));
        $codigoNbs = $codigoNbsRaw !== '' ? trim(explode(' - ', $codigoNbsRaw)[0]) : '';

        $fiscalCodes = asaas_sanitize_fiscal_codes(
            $municipalServiceCodeRaw,
            $input['operation_indicator_code'] ?? ($servicoPadrao->ind_operacao ?? ''),
            $input['federal_service_tax_code'] ?? ($servicoPadrao->codigo_tributacao_nacional ?? '')
        );

        $taxSituationCode = trim((string) ($input['tax_situation_code'] ?? ($servicoPadrao->cst_ibs_cbs ?? '000'))) ?: '000';
        $taxClassificationCode = trim((string) ($input['tax_classification_code'] ?? ($servicoPadrao->class_trib_ibs_cbs ?? '000001'))) ?: '000001';

        $valorFloat = isset($input['value'])
            ? (float) $input['value']
            : (isset($input['valor_nfse']) ? (float) $input['valor_nfse'] : (float) $os->valorTotal);
        if ($valorFloat <= 0) {
            $totais = $this->os_model->valorTotalOS($idOs);
            $valorFloat = (float) ($totais['valor_desconto'] > 0 ? $totais['valor_desconto'] : ($totais['totalProdutos'] + $totais['totalServico']));
        }

        if ($valorFloat <= 0) {
            $this->response([
                'status' => false,
                'message' => 'Valor da NFS-e deve ser maior que zero.',
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $aliquotaIss = max(0.0, round((float) ($input['aliquota_iss'] ?? ($servicoPadrao->aliquota ?? 3.5)), 2));
        if (strpos($municipalServiceCodeRaw, '99.03') !== false && $aliquotaIss <= 0.01) {
            $aliquotaIss = 0.0;
        }

        try {
            $this->load->library('Gateways/Asaas', null, 'asaas');

            $useNationalPortal = true;
            try {
                $fiscalInfo = $this->asaas->obterFiscalInfo();
                if (is_object($fiscalInfo) && property_exists($fiscalInfo, 'useNationalPortal')) {
                    $useNationalPortal = (bool) $fiscalInfo->useNationalPortal;
                }
            } catch (\Exception $e) {
                log_message('info', 'API NfseController fallback Portal Nacional: ' . $e->getMessage());
            }

            $municipalServiceId = null;
            $municipalServiceCode = null;
            if ($useNationalPortal) {
                $municipalServiceName = ! empty($municipalServiceCodeRaw) && strpos($municipalServiceCodeRaw, ' - ') !== false
                    ? $municipalServiceCodeRaw
                    : ($municipalServiceNameInput ?: '952150001 | 14.01 - Reparação e manutenção de equipamentos eletroeletrônicos de uso pessoal e domestico.');
            } else {
                $partsM = explode(' - ', $municipalServiceCodeRaw);
                $searchTerm = trim($partsM[0]);
                $municipalServiceId = $input['asaas_service_id'] ?? ($servicoPadrao->asaas_service_id ?? null);
                if (empty($municipalServiceId) && ! empty($searchTerm)) {
                    $partsM2 = explode(' | ', $searchTerm);
                    $municipalServiceCode = trim(end($partsM2));
                }
                $municipalServiceName = $municipalServiceNameInput ?: $municipalServiceCodeRaw;
            }

            $serviceDescription = trim((string) ($input['service_description'] ?? ("Prestação de Serviços - OS Nº {$idOs}")));
            $observations = trim((string) ($input['observations'] ?? ("Ordem de Serviço Nº {$idOs} - STEOS")));
            $effectiveDate = ! empty($input['effective_date']) ? $input['effective_date'] : date('Y-m-d');
            $retainIss = filter_var($input['retain_iss'] ?? false, FILTER_VALIDATE_BOOLEAN);

            $payload = [
                'serviceDescription' => $serviceDescription,
                'observations' => $observations,
                'value' => $valorFloat,
                'effectiveDate' => $effectiveDate,
                'municipalServiceId' => $municipalServiceId,
                'municipalServiceCode' => $municipalServiceCode,
                'municipalServiceName' => $municipalServiceName,
                'taxes' => [
                    'retainIss' => $retainIss,
                    'iss' => $aliquotaIss,
                    'pisCofinsRetentionType' => 'PIS_COFINS_CSLL_NOT_WITHHELD',
                    'pisCofinsTaxStatus' => 'NONE',
                    'cofins' => 0.0,
                    'csll' => 0.0,
                    'inss' => 0.0,
                    'ir' => 0.0,
                    'pis' => 0.0,
                    'taxSituationCode' => $taxSituationCode,
                    'taxClassificationCode' => $taxClassificationCode,
                    'operationIndicatorCode' => $fiscalCodes['operationIndicatorCode'],
                ],
            ];

            if ($fiscalCodes['federalServiceTaxCode'] !== '') {
                $payload['taxes']['federalServiceTaxCode'] = $fiscalCodes['federalServiceTaxCode'];
            }
            if ($codigoNbs !== '') {
                $payload['taxes']['nbsCode'] = $codigoNbs;
            }
            if (! empty($input['installment_id'])) {
                $payload['installment'] = trim((string) $input['installment_id']);
            } elseif (! empty($input['charge_id'])) {
                $payload['payment'] = trim((string) $input['charge_id']);
            } else {
                $payload['customer'] = $this->asaas->criarOuRetornarClienteAsaasId($os->clientes_id);
            }

            $resp = $this->asaas->emitirNfse($payload);
            if (! empty($resp->id)) {
                $dadosUpdateOs = [
                    'asaas_invoice_id' => $resp->id,
                    'asaas_invoice_status' => $resp->status ?? 'SCHEDULED',
                    'asaas_invoice_number' => $resp->number ?? null,
                    'asaas_invoice_pdf' => $resp->pdfUrl ?? null,
                    'asaas_invoice_xml' => $resp->xmlUrl ?? null,
                    'asaas_invoice_error' => null,
                ];
                if (empty($os->valorTotal) || $os->valorTotal <= 0) {
                    $dadosUpdateOs['valorTotal'] = $valorFloat;
                }
                $this->db->where('idOs', $idOs)->update('os', $dadosUpdateOs);
                $this->log_app("Emitiu NFS-e ({$resp->id}) via API para OS #{$idOs}");

                $this->response([
                    'status' => true,
                    'message' => 'NFS-e agendada com sucesso no Asaas!',
                    'result' => $resp,
                ], REST_Controller::HTTP_CREATED);
            }

            $this->response([
                'status' => false,
                'message' => 'Erro desconhecido ao agendar NFS-e no Asaas.',
            ], REST_Controller::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            $this->response([
                'status' => false,
                'message' => 'Erro ao emitir NFS-e: ' . $e->getMessage(),
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function consultar_get($idOs = null)
    {
        $this->logged_user();
        if (! $this->canViewNfse($this->logged_user()->level)) {
            $this->response([
                'status' => false,
                'message' => 'Você não está autorizado a Consultar NFS-e.',
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        $os = $idOs ? $this->os_model->getById($idOs) : null;
        if (! $os || empty($os->asaas_invoice_id)) {
            $this->response([
                'status' => false,
                'message' => 'O.S. não encontrada ou sem NFS-e vinculada.',
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        try {
            $this->load->library('Gateways/Asaas', null, 'asaas');
            $resp = $this->asaas->consultarNfse($os->asaas_invoice_id);

            if (! empty($resp->id)) {
                $dadosUpdate = [
                    'asaas_invoice_id' => $resp->id,
                    'asaas_invoice_status' => $resp->status ?? $os->asaas_invoice_status,
                    'asaas_invoice_number' => ! empty($resp->number) ? $resp->number : (! empty($resp->rpsNumber) ? $resp->rpsNumber : ($os->asaas_invoice_number ?? null)),
                    'asaas_invoice_pdf' => $resp->pdfUrl ?? ($os->asaas_invoice_pdf ?? null),
                    'asaas_invoice_xml' => $resp->xmlUrl ?? ($os->asaas_invoice_xml ?? null),
                    'asaas_invoice_error' => ($resp->status ?? '') === 'AUTHORIZED' ? null : ($resp->statusDescription ?? ($os->asaas_invoice_error ?? null)),
                ];
                $this->db->where('idOs', $idOs)->update('os', $dadosUpdate);

                $this->response([
                    'status' => true,
                    'message' => 'Status da NFS-e atualizado.',
                    'result' => $dadosUpdate,
                ], REST_Controller::HTTP_OK);
            }

            $this->response([
                'status' => false,
                'message' => 'Não foi possível consultar a NFS-e no Asaas.',
            ], REST_Controller::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            $this->response([
                'status' => false,
                'message' => 'Erro na consulta: ' . $e->getMessage(),
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function cancelar_post($idOs = null)
    {
        $this->logged_user();
        if (! $this->permission->checkPermission($this->logged_user()->level, 'dNfse')) {
            $this->response([
                'status' => false,
                'message' => 'Você não está autorizado a Cancelar NFS-e.',
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        $os = $idOs ? $this->os_model->getById($idOs) : null;
        if (! $os || empty($os->asaas_invoice_id)) {
            $this->response([
                'status' => false,
                'message' => 'O.S. não encontrada ou sem NFS-e vinculada.',
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $cancelOnlyOnAsaas = is_array($input) && ! empty($input['cancelOnlyOnAsaas']);

        try {
            $this->load->library('Gateways/Asaas', null, 'asaas');
            try {
                $resp = $this->asaas->cancelarNfse($os->asaas_invoice_id, $cancelOnlyOnAsaas);
                $statusRetorno = strtoupper((string) ($resp->status ?? 'CANCELED'));
            } catch (\Exception $cancelEx) {
                $consulta = $this->asaas->consultarNfse($os->asaas_invoice_id);
                $statusConsulta = strtoupper((string) ($consulta->status ?? ''));
                if (in_array($statusConsulta, ['CANCELED', 'CANCELLED', 'ERROR'], true)) {
                    $statusRetorno = 'CANCELED';
                } elseif ($statusConsulta === 'PROCESSING_CANCELLATION') {
                    $statusRetorno = 'PROCESSING_CANCELLATION';
                } elseif ($statusConsulta === 'CANCELLATION_DENIED') {
                    $msgNegado = $consulta->statusDescription ?? $cancelEx->getMessage();
                    $this->db->where('idOs', $idOs)->update('os', [
                        'asaas_invoice_status' => 'CANCELLATION_DENIED',
                        'asaas_invoice_error' => $msgNegado,
                    ]);
                    $this->response([
                        'status' => false,
                        'message' => 'Cancelamento negado pela prefeitura: ' . $msgNegado,
                    ], REST_Controller::HTTP_BAD_REQUEST);
                } else {
                    throw $cancelEx;
                }
            }

            $novoStatus = ($statusRetorno === 'PROCESSING_CANCELLATION') ? 'PROCESSING_CANCELLATION' : 'CANCELED';
            $this->db->where('idOs', $idOs)->update('os', [
                'asaas_invoice_status' => $novoStatus,
                'asaas_invoice_error' => null,
            ]);
            $this->log_app("Solicitou cancelamento NFS-e ({$os->asaas_invoice_id}) via API para OS #{$idOs}. Status: {$novoStatus}");

            $this->response([
                'status' => true,
                'message' => $novoStatus === 'PROCESSING_CANCELLATION'
                    ? 'Cancelamento em processamento na prefeitura.'
                    : 'NFS-e cancelada com sucesso no Asaas.',
                'result' => ['asaas_invoice_status' => $novoStatus],
            ], REST_Controller::HTTP_OK);
        } catch (\Exception $e) {
            $this->response([
                'status' => false,
                'message' => 'Erro ao cancelar: ' . $e->getMessage(),
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}
