<?php

/*
    Ivan Sarkozin
    https://github.com/sarkozin
*/

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class ClientCobrancasController extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Conecte_model');
        $this->load->library('Authorization_Token');
    }

    public function index_get()
    {
        $clientLogged = $this->logged_client();
        $clienteId = (int) $clientLogged->usuario->idClientes;
        $perPage = (int) ($this->get('perPage', true) ?: 20);
        $page = max(0, (int) $this->get('page', true));
        $start = $page * $perPage;

        $cobrancas = $this->Conecte_model->getCobrancas('cobrancas', '*', '', $perPage, $start, false, '', $clienteId);

        if (empty($cobrancas)) {
            $this->response([
                'status' => true,
                'message' => 'Nenhuma cobrança encontrada',
                'result' => [],
            ], REST_Controller::HTTP_OK);
        }

        foreach ($cobrancas as $cobranca) {
            unset($cobranca->senha);
        }

        $this->response([
            'status' => true,
            'message' => 'Listando resultados',
            'result' => $cobrancas
        ], REST_Controller::HTTP_OK);
    }
}
