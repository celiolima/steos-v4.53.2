<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class VeiculosController extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('veiculos_model');
        $this->load->model('gasolina_model');
    }

    public function index_get($id = '')
    {
        $this->logged_user();
        if (! $this->permission->checkPermission($this->logged_user()->level, 'vVeiculo')) {
            $this->response([
                'status' => false,
                'message' => 'Você não está autorizado a Visualizar Veículos.',
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        if (! $id) {
            $perPage = (int) ($this->get('perPage', true) ?: 20);
            $page = max(0, (int) $this->get('page', true));
            $start = $page * $perPage;

            $veiculos = $this->veiculos_model->get('veiculos', '*', '', $perPage, $start);

            $this->response([
                'status' => true,
                'message' => 'Listando Veículos',
                'result' => $veiculos,
            ], REST_Controller::HTTP_OK);
        }

        if (! is_numeric($id)) {
            $this->response([
                'status' => false,
                'message' => 'ID de veículo inválido.',
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $veiculo = $this->veiculos_model->getById((int) $id);
        if (! $veiculo) {
            $this->response([
                'status' => false,
                'message' => 'Veículo não encontrado.',
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        $veiculo->abastecimentos = $this->gasolina_model->getByIdEx((int) $id);
        $veiculo->logs = $this->db->select('veiculos_logs.*, usuarios.nome as nomeUsuario')
            ->from('veiculos_logs')
            ->join('usuarios', 'usuarios.idUsuarios = veiculos_logs.usuario_id', 'left')
            ->where('veiculo_id', (int) $id)
            ->order_by('data_hora', 'DESC')
            ->limit(25)
            ->get()
            ->result();

        $this->response([
            'status' => true,
            'message' => 'Detalhes do Veículo',
            'result' => $veiculo,
        ], REST_Controller::HTTP_OK);
    }
}
