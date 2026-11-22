<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class VendasController extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('vendas_model');
    }

    public function index_get($id = '')
    {
        $this->logged_user();
        if (! $this->permission->checkPermission($this->logged_user()->level, 'vVenda')) {
            $this->response([
                'status' => false,
                'message' => 'Você não está autorizado a Visualizar Vendas.',
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        if (! $id) {
            $where = [];
            $pesquisa = trim((string) $this->get('search', true));
            $status = trim((string) $this->get('status', true));
            $de = trim((string) $this->get('from', true));
            $ate = trim((string) $this->get('to', true));

            if ($pesquisa !== '') {
                $where['pesquisa'] = $pesquisa;
            }
            if ($status !== '') {
                $where['status'] = $status;
            }
            if ($de !== '') {
                $where['de'] = $de;
            }
            if ($ate !== '') {
                $where['ate'] = $ate;
            }

            $perPage = (int) ($this->get('perPage', true) ?: 20);
            $page = max(0, (int) $this->get('page', true));
            $start = $page * $perPage;

            $vendas = $this->vendas_model->get('vendas', '*', $where, $perPage, $start);
            foreach ($vendas as $venda) {
                $venda->totalProdutos = $this->vendas_model->getTotalVendas($venda->idVendas);
            }

            $this->response([
                'status' => true,
                'message' => 'Listando Vendas',
                'result' => $vendas,
            ], REST_Controller::HTTP_OK);
        }

        if (! is_numeric($id)) {
            $this->response([
                'status' => false,
                'message' => 'ID de venda inválido.',
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $venda = $this->vendas_model->getById((int) $id);
        if (! $venda) {
            $this->response([
                'status' => false,
                'message' => 'Venda não encontrada.',
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        unset($venda->senha);
        $venda->produtos = $this->vendas_model->getProdutos((int) $id);
        $venda->cobrancas = $this->vendas_model->getCobrancas((int) $id);
        $venda->totalProdutos = $this->vendas_model->getTotalVendas((int) $id);

        $this->response([
            'status' => true,
            'message' => 'Detalhes da Venda',
            'result' => $venda,
        ], REST_Controller::HTTP_OK);
    }
}
