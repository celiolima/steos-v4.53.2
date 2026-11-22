<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
// CI3
// MIGRAÇÃO: Port direto do steos — Fase 3
// Controller: Equipamentos.php
// Responsabilidade: Cadastro de equipamentos de clientes

class Equipamentos extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('form');
        $this->load->model('equipamentos_model');
        $this->load->model('marcas_model');
        $this->data['menuEquipamentos'] = 'Equipamentos';
    }

    public function index()
    {
        $this->gerenciar();
    }

    public function gerenciar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vEquipamento')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar equipamentos.');
            redirect(base_url());
        }

        $pesquisa = $this->input->get('pesquisa');

        $this->load->library('pagination');

        $this->data['configuration']['base_url'] = site_url('equipamentos/gerenciar/');
        $this->data['configuration']['total_rows'] = $this->equipamentos_model->count('equipamentos', $pesquisa);
        if ($pesquisa) {
            $this->data['configuration']['suffix'] = "?pesquisa={$pesquisa}";
            $this->data['configuration']['first_url'] = base_url("index.php/equipamentos")."\?pesquisa={$pesquisa}";
        }

        $this->pagination->initialize($this->data['configuration']);

        $this->data['results'] = $this->equipamentos_model->get('equipamentos', '*', $pesquisa, $this->data['configuration']['per_page'], $this->uri->segment(3));
        $this->data['view'] = 'equipamentos/equipamentos';
        return $this->layout();
    }

    public function adicionar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aEquipamento')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar equipamentos.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->input->post() == false) {
            $this->data['marcas'] = $this->marcas_model->getALL('marcas');
        } else {
            $data = [
                'equipamento'   => set_value('tipoEquipamento'),
                'descricao'     => set_value('descricao'),
                'modelo'        => set_value('modelo'),
                'marcas'        => set_value('marcas'),
                'num_serie'     => set_value('serial'),
                'cor'           => set_value('cor'),
                'voltagem'      => set_value('voltagem'),
                'potencia'      => set_value('potencia'),
            ];

            if ($this->equipamentos_model->add('equipamentos', $data) == true) {
                $this->session->set_flashdata('success', 'Equipamento adicionado com sucesso!');
                log_info('Adicionou um equipamento');
                $this->data['marcas'] = $this->marcas_model->getALL('marcas');
                redirect(site_url('equipamentos/'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>An Error Occured.</p></div>';
            }
        }

        $this->data['view'] = 'equipamentos/adicionarEquipamentos';
        return $this->layout();
    }

    public function editar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eEquipamento')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar equipamentos.');
            redirect(base_url());
        }

        if (!$this->uri->segment(3) || !is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('steos');
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->input->post() == false) {
            $this->data['marcas'] = $this->marcas_model->getALL('marcas');
        } else {
            $data = [
                'equipamento'   => set_value('tipoEquipamento'),
                'descricao'     => set_value('descricao'),
                'modelo'        => set_value('modelo'),
                'marcas'        => set_value('marcas'),
                'num_serie'     => set_value('serial'),
                'cor'           => set_value('cor'),
                'voltagem'      => set_value('voltagem'),
                'potencia'      => set_value('potencia'),
            ];

            if ($this->equipamentos_model->edit('equipamentos', $data, 'idEquipamentos', $this->input->post('idEquipamentos')) == true) {
                $this->session->set_flashdata('success', 'Equipamento editado com sucesso!');
                log_info('Alterou um equipamento. ID: ' . $this->input->post('idEquipamentos'));
                redirect(site_url('equipamentos/editar/') . $this->input->post('idEquipamentos'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>An Error Occured</p></div>';
            }
        }

        $this->data['result'] = $this->equipamentos_model->getById($this->uri->segment(3));

        $this->data['view'] = 'equipamentos/editarEquipamentos';
        return $this->layout();
    }

    public function excluir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dEquipamento')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir equipamentos.');
            redirect(base_url());
        }

        $id = $this->input->post('id');
        if ($id == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir equipamento.');
            redirect(site_url('equipamentos/gerenciar/'));
        }

        if ($this->equipamentos_model->delete('equipamentos', 'idEquipamentos', $id) == true) {
            log_info('Removeu um equipamento. ID: ' . $id);
            $this->session->set_flashdata('success', 'Equipamento excluído com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir equipamento.');
        }

        redirect(site_url('equipamentos/gerenciar/'));
    }
}
