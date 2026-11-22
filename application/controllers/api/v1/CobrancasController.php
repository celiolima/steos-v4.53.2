<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class CobrancasController extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('cobrancas_model');
    }

    public function index_get($id = '')
    {
        $this->logged_user();
        if (! $this->permission->checkPermission($this->logged_user()->level, 'vCobranca')) {
            $this->response([
                'status' => false,
                'message' => 'Você não está autorizado a Visualizar Cobranças.',
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        if (! $id) {
            $where = [];
            foreach (['pesquisa', 'os_id', 'vendas_id', 'tipo', 'status', 'data_de', 'data_ate'] as $field) {
                $val = trim((string) $this->get($field, true));
                if ($val !== '') {
                    $where[$field] = $val;
                }
            }
            if (! isset($where['pesquisa'])) {
                $search = trim((string) $this->get('search', true));
                if ($search !== '') {
                    $where['pesquisa'] = $search;
                }
            }

            $perPage = (int) ($this->get('perPage', true) ?: 20);
            $page = max(0, (int) $this->get('page', true));
            $start = $page * $perPage;

            $cobrancas = $this->cobrancas_model->get('cobrancas', 'cobrancas.*', $where, $perPage, $start);

            $this->response([
                'status' => true,
                'message' => 'Listando Cobranças',
                'result' => $cobrancas,
            ], REST_Controller::HTTP_OK);
        }

        $cobranca = $this->cobrancas_model->getById($id);
        if (! $cobranca) {
            $this->response([
                'status' => false,
                'message' => 'Cobrança não encontrada.',
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        unset($cobranca->senha);

        $this->response([
            'status' => true,
            'message' => 'Detalhes da Cobrança',
            'result' => $cobranca,
        ], REST_Controller::HTTP_OK);
    }

    public function pix_get($id)
    {
        $this->logged_user();
        if (! $this->permission->checkPermission($this->logged_user()->level, 'vCobranca')) {
            $this->response([
                'status' => false,
                'message' => 'Você não está autorizado a Visualizar Cobranças.',
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        $cobranca = $this->cobrancas_model->getById($id);
        if (! $cobranca) {
            $this->response([
                'status' => false,
                'message' => 'Cobrança não encontrada.',
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        if ($cobranca->payment_gateway !== 'Asaas' || empty($cobranca->charge_id)) {
            $this->response([
                'status' => false,
                'message' => 'QR Code Pix disponível apenas para cobranças Asaas com charge_id válido.',
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $this->load->library('Gateways/Asaas', null, 'asaas');
        $pix = $this->asaas->getPixQrCode($cobranca->charge_id);

        if (empty($pix['success'])) {
            $this->response([
                'status' => false,
                'message' => $pix['message'] ?? 'Não foi possível obter o QR Code Pix.',
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $this->response([
            'status' => true,
            'message' => 'QR Code Pix obtido com sucesso.',
            'result' => $pix,
        ], REST_Controller::HTTP_OK);
    }
}
