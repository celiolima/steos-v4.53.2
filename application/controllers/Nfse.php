<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Nfse extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('pagination');
        $this->data['menuNfse'] = true;
    }

    public function index()
    {
        $this->listar();
    }

    public function listar()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar as NFS-e.');
            redirect(base_url());
        }

        $pesquisa = $this->input->get('pesquisa');
        $status = $this->input->get('status');
        $data_de = $this->input->get('data_de');
        $data_ate = $this->input->get('data_ate');

        $this->db->start_cache();
        $this->db->from('os');
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id', 'left');
        $this->db->where('os.asaas_invoice_status IS NOT NULL');
        $this->db->where('os.asaas_invoice_status !=', '');

        if ($pesquisa) {
            $this->db->group_start();
            $this->db->like('clientes.nomeCliente', $pesquisa);
            $this->db->or_like('clientes.documento', $pesquisa);
            $this->db->or_like('os.asaas_invoice_number', $pesquisa);
            if (is_numeric($pesquisa)) {
                $this->db->or_where('os.idOs', $pesquisa);
            }
            $this->db->group_end();
        }

        if ($status && $status !== 'Todos') {
            $this->db->where('os.asaas_invoice_status', $status);
        }

        if ($data_de) {
            $deArr = explode('/', $data_de);
            if (count($deArr) == 3) {
                $this->db->where('os.dataInicial >=', $deArr[2] . '-' . $deArr[1] . '-' . $deArr[0]);
            } else {
                $this->db->where('os.dataInicial >=', $data_de);
            }
        }
        if ($data_ate) {
            $ateArr = explode('/', $data_ate);
            if (count($ateArr) == 3) {
                $this->db->where('os.dataInicial <=', $ateArr[2] . '-' . $ateArr[1] . '-' . $ateArr[0]);
            } else {
                $this->db->where('os.dataInicial <=', $data_ate);
            }
        }
        $this->db->stop_cache();

        $total_rows = $this->db->count_all_results();

        $per_page = $this->data['configuration']['per_page'] ?? 10;
        $offset = $this->uri->segment(3) ? intval($this->uri->segment(3)) : 0;

        $this->db->select('os.*, clientes.nomeCliente, clientes.documento,
            COALESCE((SELECT SUM(produtos_os.preco * produtos_os.quantidade) FROM produtos_os WHERE produtos_os.os_id = os.idOs), 0) AS totalProdutos,
            COALESCE((SELECT SUM(servicos_os.preco * servicos_os.quantidade) FROM servicos_os WHERE servicos_os.os_id = os.idOs), 0) AS totalServicos');
        $this->db->order_by('os.idOs', 'DESC');
        $this->db->limit($per_page, $offset);
        $results = $this->db->get()->result();

        $this->db->flush_cache();

        $query_params = [];
        if ($pesquisa) { $query_params['pesquisa'] = $pesquisa; }
        if ($status && $status !== 'Todos') { $query_params['status'] = $status; }
        if ($data_de) { $query_params['data_de'] = $data_de; }
        if ($data_ate) { $query_params['data_ate'] = $data_ate; }

        $this->data['configuration']['base_url'] = site_url('nfse/listar/');
        $this->data['configuration']['total_rows'] = $total_rows;
        if (count($query_params) > 0) {
            $suffix = '?' . http_build_query($query_params);
            $this->data['configuration']['suffix'] = $suffix;
            $this->data['configuration']['first_url'] = site_url('nfse/listar/') . $suffix;
        }

        $this->pagination->initialize($this->data['configuration']);

        $this->data['results'] = $results;
        $this->data['view'] = 'nfse/nfse';

        return $this->layout();
    }
}
