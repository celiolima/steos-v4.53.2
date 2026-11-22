<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class ContratosController extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('contratos_model');
    }

    public function index_get($id = '')
    {
        $this->logged_user();

        if (! $id) {
            $where = [];
            $pesquisa = trim((string) $this->get('search', true));
            $contrato = trim((string) $this->get('contrato', true));
            $nome = trim((string) $this->get('nome', true));
            $status = trim((string) $this->get('status', true));
            $clientesId = trim((string) $this->get('clientes_id', true));

            if ($pesquisa !== '') {
                $where['pesquisa'] = $pesquisa;
            }
            if ($contrato !== '') {
                $where['contrato'] = $contrato;
            }
            if ($nome !== '') {
                $where['nome'] = $nome;
            }
            if ($status !== '') {
                $where['status'] = $status;
            }
            if ($clientesId !== '' && is_numeric($clientesId)) {
                $where['clientes_id'] = (int) $clientesId;
            }

            $perPage = (int) ($this->get('perPage', true) ?: 20);
            $page = max(0, (int) $this->get('page', true));
            $start = $page * $perPage;

            $contratos = $this->contratos_model->getContratos('contratos', 'contratos.*', $where, $perPage, $start);

            $this->response([
                'status' => true,
                'message' => 'Listando Contratos',
                'result' => $contratos,
            ], REST_Controller::HTTP_OK);
        }

        if (! is_numeric($id)) {
            $this->response([
                'status' => false,
                'message' => 'ID de contrato inválido.',
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $contrato = $this->contratos_model->getById($id);
        if (! $contrato) {
            $this->response([
                'status' => false,
                'message' => 'Contrato não encontrado.',
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        $sistemas = $this->contratos_model->getSistemasByContrato($id);
        foreach ($sistemas as $sistema) {
            $sistema->checks = $this->contratos_model->getChecksBySistemaContrato($sistema->idSistemas_contratos);
        }

        $contrato->sistemas = $sistemas;
        $contrato->os = $this->contratos_model->getOsByContrato($id);
        $contrato->vendas = $this->contratos_model->getVendasByContrato($id);
        $contrato->cobrancas = $this->contratos_model->getCobrancasByContrato($id);
        $contrato->anexos = $this->contratos_model->getAnexos($id);

        $this->response([
            'status' => true,
            'message' => 'Detalhes do Contrato',
            'result' => $contrato,
        ], REST_Controller::HTTP_OK);
    }
}
