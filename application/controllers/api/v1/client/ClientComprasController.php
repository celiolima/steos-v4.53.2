<?php

/*
    Ivan Sarkozin
    https://github.com/sarkozin
*/

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class ClientComprasController extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Conecte_model');
        $this->load->library('Authorization_Token');
        $this->load->library('pagination');
        $this->load->library('format');
        $this->load->model('steos_model');
        $this->load->model('os_model');
    }

    public function index_get(?int $idVendas = null)
    {
        $clientLogged = $this->logged_client();
        $clienteId = (int) $clientLogged->usuario->idClientes;

        if ($idVendas == '') {
            $perPage = (int) ($this->get('perPage', true) ?: $this->CI->db->get_where('configuracoes', ['config' => 'per_page'])->row_object()->valor ?: 20);
            $page = max(0, (int) $this->get('page', true));
            $start = $page * $perPage;

            $compras = $this->Conecte_model->getCompras('vendas', '*', '', $perPage, $start, false, '', $clienteId);
            if (empty($compras)) {
                $this->response([
                    'status' => true,
                    'message' => 'Nenhuma compra encontrada',
                    'result' => ['Compras' => []],
                ], REST_Controller::HTTP_OK);
            }

            foreach ($compras as $compra) {
                unset($compra->senha);
            }

            $this->response([
                'status' => true,
                'message' => 'Listando resultados',
                'result' => [
                    'Compras' => $compras,
                ],
            ], REST_Controller::HTTP_OK);
        }

        $this->load->model('vendas_model');
        $result = $this->vendas_model->getById($idVendas);
        if (empty($result)) {
            $this->response([
                'status' => false,
                'message' => 'Compra não encontrada'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        if ((int) $result->clientes_id !== $clienteId) {
            $this->response([
                'status' => false,
                'message' => 'Você não possui permissão para visualizar esta compra.'
            ], REST_Controller::HTTP_FORBIDDEN);
        }

        $pixKey = $this->CI->db->get_where('configuracoes', ['config' => 'pix_key'])->row_object()->valor;
        $emitente = $this->steos_model->getEmitente();
        $qrCode = $this->vendas_model->getQrCode(
            $idVendas,
            $pixKey,
            $emitente
        );
        $chaveFormatada = $this->format->formatarChave($pixKey);
        $produtos = $this->vendas_model->getProdutos($idVendas);
        unset(
            $result->senha,
            $result->email,
            $result->telefone,
            $result->celular,
            $result->dataCadastro,
            $result->usuarios_id
        );
        $this->response([
            'status' => true,
            'message' => 'Listando resultados',
            'result' => [
                'Compras' => $result,
                'Produtos' => $produtos,
                'QrCode' => $qrCode,
                'ChaveFormatada' => $chaveFormatada,
            ],
        ], REST_Controller::HTTP_OK);
    }
}
