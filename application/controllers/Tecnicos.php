<?php
// CI3
// MIGRAÇÃO: Port direto do steos — Fase 3
// Controller: Tecnicos.php
// Responsabilidade: CRUD de técnicos com controle de comissão, validade e status

defined('BASEPATH') OR exit('No direct script access allowed');

class Tecnicos extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cUsuario')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para configurar os Tecnicos.');
            redirect(base_url());
        }

        $this->load->helper('form');
        $this->load->model('tecnicos_model');
        $this->data['menuTecnicos'] = 'Tecnicos';
        $this->data['menuConfiguracoes'] = 'Configurações';
    }


    public function index()
    {
        $this->gerenciar();
    }

    public function gerenciar()
    {
        $this->data['configuration']['base_url'] = base_url() . 'index.php/tecnicos/gerenciar/';
        $this->data['configuration']['total_rows'] = $this->tecnicos_model->count('tecnicos');

        $resultado = $this->data['results'] = $this->tecnicos_model->get($this->data['configuration']['per_page'], $this->uri->segment(3));

        $this->load->library('pagination');

        $this->data['view'] = 'tecnicos/tecnicos';
        return $this->layout();
    }

    public function adicionar()
    {
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->form_validation->run('tecnicos') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $data = [
                'nome'              => set_value('nome'),
                'comissao_servico'  => set_value('comisServTecnico'),
                'comissao_produto'  => set_value('comisVendTecnico'),
                'dataExpiracao'     => set_value('dataExpiracao'),
                'ativo'             => set_value('situacao'),
                'dataCadastro'      => date('Y-m-d'),
            ];

            if ($this->tecnicos_model->add('tecnicos', $data) == true) {
                $this->session->set_flashdata('success', 'Tecnico cadastrado com sucesso!');
                log_info('Adicionou um tecnico.');
                redirect(site_url('tecnicos/adicionar/'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
            }
        }

        $this->load->model('permissoes_model');
        $this->data['permissoes'] = $this->permissoes_model->getActive('permissoes', 'permissoes.idPermissao,permissoes.nome');
        $this->data['view'] = 'tecnicos/adicionarTecnico';
        return $this->layout();
    }

    public function editar()
    {
        if (!$this->uri->segment(3) || !is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('steos');
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';
        $this->form_validation->set_rules('nome', 'Nome', 'trim|required');
        $this->form_validation->set_rules('comissao_servico', 'Comissao Servico', 'trim|required');
        $this->form_validation->set_rules('comissao_produto', 'Comissao Produto', 'trim|required');
        $this->form_validation->set_rules('situacao', 'Situação', 'trim|required');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            if ($this->input->post('idTecnico') == 1 && $this->input->post('situacao') == 0) {
                $this->session->set_flashdata('error', 'O usuário super admin não pode ser desativado!');
                redirect(base_url() . 'index.php/tecnicos/editar/' . $this->input->post('idTecnico'));
            }

            $data = [
                'nome'              => $this->input->post('nome'),
                'comissao_servico'  => $this->input->post('comissao_servico'),
                'comissao_produto'  => $this->input->post('comissao_produto'),
                'dataExpiracao'     => set_value('dataExpiracao'),
                'ativo'             => $this->input->post('situacao'),
            ];

            if ($this->tecnicos_model->edit('tecnicos', $data, 'idTecnicos', $this->input->post('idTecnico')) == true) {
                $this->session->set_flashdata('success', 'Tecnico editado com sucesso!');
                log_info('Alterou um tecnico. ID: ' . $this->input->post('idTecnico'));
                redirect(site_url('tecnicos/editar/') . $this->input->post('idTecnico'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro</p></div>';
            }
        }

        $this->data['result'] = $this->tecnicos_model->getById($this->uri->segment(3));
        $this->load->model('permissoes_model');
        $this->data['permissoes'] = $this->permissoes_model->getActive('permissoes', 'permissoes.idPermissao,permissoes.nome');

        $this->data['view'] = 'tecnicos/editarTecnico';
        return $this->layout();
    }
}
