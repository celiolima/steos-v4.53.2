<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class ClientContratosController extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('contratos_model');
    }

    public function index_get($id = null)
    {
        $clientLogged = $this->logged_client();
        $clienteId = (int) $clientLogged->usuario->idClientes;

        if (! $id) {
            $perPage = (int) ($this->get('perPage', true) ?: 20);
            $page = max(0, (int) $this->get('page', true));
            $start = $page * $perPage;

            $contratos = $this->contratos_model->getContratos(
                'contratos',
                'contratos.*',
                ['clientes_id' => $clienteId],
                $perPage,
                $start
            );

            $this->response([
                'status' => true,
                'message' => 'Listando Contratos do Cliente',
                'result' => $contratos,
            ], REST_Controller::HTTP_OK);
        }

        if (! is_numeric($id)) {
            $this->response([
                'status' => false,
                'message' => 'ID de contrato inválido.',
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $contrato = $this->contratos_model->getById((int) $id);
        if (! $contrato) {
            $this->response([
                'status' => false,
                'message' => 'Contrato não encontrado.',
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        if ((int) $contrato->clientes_id !== $clienteId) {
            $this->response([
                'status' => false,
                'message' => 'Você não possui permissão para visualizar este contrato.',
            ], REST_Controller::HTTP_FORBIDDEN);
        }

        $sistemas = $this->contratos_model->getSistemasByContrato((int) $id);
        foreach ($sistemas as $sistema) {
            $sistema->checks = $this->contratos_model->getChecksBySistemaContrato($sistema->idSistemas_contratos);
        }

        $anexos = $this->contratos_model->getAnexos((int) $id);
        foreach ($anexos as $anexo) {
            unset($anexo->path);
        }

        $contrato->sistemas = $sistemas;
        $contrato->os = $this->contratos_model->getOsByContrato((int) $id);
        $contrato->vendas = $this->contratos_model->getVendasByContrato((int) $id);
        $contrato->cobrancas = $this->contratos_model->getCobrancasByContrato((int) $id);
        $contrato->anexos = $anexos;

        $this->response([
            'status' => true,
            'message' => 'Detalhes do Contrato',
            'result' => $contrato,
        ], REST_Controller::HTTP_OK);
    }
}
