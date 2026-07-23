<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Servicos_nfse extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('form');
        $this->load->model('servicos_nfse_model');
        $this->data['menuServicosNfse'] = 'Serviços NFS-e';
    }

    public function index()
    {
        $this->gerenciar();
    }

    public function gerenciar()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vServico')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar serviços.');
            redirect(base_url());
        }

        $pesquisa = $this->input->get('pesquisa');

        $this->load->library('pagination');

        $this->data['configuration']['base_url'] = site_url('servicos_nfse/gerenciar/');
        $this->data['configuration']['total_rows'] = $this->servicos_nfse_model->count('servicos_nfse');
        if ($pesquisa) {
            $this->data['configuration']['suffix'] = "?pesquisa={$pesquisa}";
            $this->data['configuration']['first_url'] = base_url("index.php/servicos_nfse")."\?pesquisa={$pesquisa}";
        }

        $this->pagination->initialize($this->data['configuration']);

        $this->data['results'] = $this->servicos_nfse_model->get('servicos_nfse', '*', $pesquisa, $this->data['configuration']['per_page'], $this->uri->segment(3));

        $this->data['view'] = 'servicos_nfse/servicos_nfse';

        return $this->layout();
    }

    public function adicionar()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'aServico')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar serviços.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->input->post('nome_servico') == null && $this->input->post('codigo_servico_municipal') == null) {
            // Apenas renderiza a tela
        } else {
            $this->form_validation->set_rules('nome_servico', 'Nome do Serviço', 'required|trim');
            $this->form_validation->set_rules('codigo_servico_municipal', 'Código de Serviço Municipal', 'required|trim');
            $this->form_validation->set_rules('aliquota', 'Alíquota', 'required|trim');

            if ($this->form_validation->run() == false) {
                $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
            } else {
                $aliquota = $this->input->post('aliquota');
                $aliquota = str_replace('%', '', $aliquota);
                $aliquota = str_replace('.', '', $aliquota);
                $aliquota = str_replace(',', '.', $aliquota);
                $aliquota = trim($aliquota);

                $data = [
                    'nome_servico' => $this->input->post('nome_servico'),
                    'codigo_servico_municipal' => $this->input->post('codigo_servico_municipal'),
                    'codigo_nbs' => $this->input->post('codigo_nbs'),
                    'aliquota' => $aliquota,
                ];

                if ($this->servicos_nfse_model->add('servicos_nfse', $data) == true) {
                    $this->session->set_flashdata('success', 'Serviço NFS-e adicionado com sucesso!');
                    log_info('Adicionou um serviço NFS-e');
                    redirect(site_url('servicos_nfse/adicionar/'));
                } else {
                    $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
                }
            }
        }
        $this->data['view'] = 'servicos_nfse/adicionarServicoNfse';

        return $this->layout();
    }

    public function editar()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3)) || ! $this->servicos_nfse_model->getById($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Serviço não encontrado ou parâmetro inválido.');
            redirect('servicos_nfse/gerenciar');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'eServico')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar serviços.');
            redirect(base_url());
        }
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->input->post('idServicosNfse') != null) {
            $this->form_validation->set_rules('nome_servico', 'Nome do Serviço', 'required|trim');
            $this->form_validation->set_rules('codigo_servico_municipal', 'Código de Serviço Municipal', 'required|trim');
            $this->form_validation->set_rules('aliquota', 'Alíquota', 'required|trim');

            if ($this->form_validation->run() == false) {
                $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
            } else {
                $aliquota = $this->input->post('aliquota');
                $aliquota = str_replace('%', '', $aliquota);
                $aliquota = str_replace('.', '', $aliquota);
                $aliquota = str_replace(',', '.', $aliquota);
                $aliquota = trim($aliquota);

                $data = [
                    'nome_servico' => $this->input->post('nome_servico'),
                    'codigo_servico_municipal' => $this->input->post('codigo_servico_municipal'),
                    'codigo_nbs' => $this->input->post('codigo_nbs'),
                    'aliquota' => $aliquota,
                ];

                if ($this->servicos_nfse_model->edit('servicos_nfse', $data, 'idServicosNfse', $this->input->post('idServicosNfse')) == true) {
                    $this->session->set_flashdata('success', 'Serviço NFS-e editado com sucesso!');
                    log_info('Alterou um serviço NFS-e. ID: ' . $this->input->post('idServicosNfse'));
                    redirect(site_url('servicos_nfse/editar/') . $this->input->post('idServicosNfse'));
                } else {
                    $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
                }
            }
        }

        $this->data['result'] = $this->servicos_nfse_model->getById($this->uri->segment(3));

        $this->data['view'] = 'servicos_nfse/editarServicoNfse';

        return $this->layout();
    }

    public function excluir()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'dServico')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir serviços.');
            redirect(base_url());
        }

        $id = $this->input->post('id');
        if ($id == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir serviço.');
            redirect(site_url('servicos_nfse/gerenciar/'));
        }

        $this->servicos_nfse_model->delete('servicos_nfse', 'idServicosNfse', $id);

        log_info('Removeu um serviço NFS-e. ID: ' . $id);

        $this->session->set_flashdata('success', 'Serviço NFS-e excluído com sucesso!');
        redirect(site_url('servicos_nfse/gerenciar/'));
    }

    public function getAllJson()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vServico')) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([]));
        }

        $results = $this->servicos_nfse_model->get('servicos_nfse', '*');
        return $this->output->set_content_type('application/json')->set_output(json_encode($results));
    }
}
