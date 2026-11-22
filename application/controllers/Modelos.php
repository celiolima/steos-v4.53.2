<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
// CI3
// MIGRAÇÃO: Port direto do steos — Fase 3
// Controller: Modelos.php
// Responsabilidade: Templates reutilizáveis de documentos com impressão direta

class Modelos extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('form');
        $this->load->model('modelos_model');
        $this->data['menuModelo'] = 'Modelos';
    }

    public function index()
    {
        $this->gerenciar();
    }

    public function gerenciar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vModelo')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar modelos.');
            redirect(base_url());
        }

        $this->load->library('pagination');

        $this->data['configuration']['base_url'] = site_url('modelos/gerenciar/');
        $this->data['configuration']['total_rows'] = $this->modelos_model->count('modelos');

        $this->pagination->initialize($this->data['configuration']);

        $this->data['results'] = $this->modelos_model->get('modelos', '*', '', $this->data['configuration']['per_page'], $this->uri->segment(3));

        $this->data['view'] = 'modelos/modelos';
        return $this->layout();
    }

    public function adicionar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aModelo')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar modelos.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->form_validation->run('modelos') == false) {
            $this->data['custom_error'] = (validation_errors() ? true : false);
        } else {
            $data = [
                'dataModelo'    => date('Y/m/d'),
                'refModelo'     => $this->input->post('refModelo'),
                'textoModelo'   => $this->input->post('textoModelo'),
                'usuarios_id'   => $this->session->userdata('id_admin'),
            ];

            if (is_numeric($id = $this->modelos_model->add('modelos', $data, true))) {
                log_info('Adicionou uma modelo');
                $this->session->set_flashdata('success', 'Termo de Modelo adicionado com sucesso.');
                redirect(site_url('modelos/editar/') . $id);
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
            }
        }

        $this->data['view'] = 'modelos/adicionarModelo';
        return $this->layout();
    }

    public function editar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eModelo')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar modelos.');
            redirect(base_url());
        }

        if (!$this->uri->segment(3) || !is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('steos');
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->form_validation->run('modelos') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $data = [
                'textoModelo'   => $this->input->post('textoModelo'),
                'refModelo'     => $this->input->post('refModelo'),
            ];

            if ($this->modelos_model->edit('modelos', $data, 'idModelos', $this->input->post('idModelos')) == true) {
                $this->session->set_flashdata('success', 'Modelo editada com sucesso!');
                log_info('Alterou uma modelo. ID: ' . $this->input->post('idModelos'));
                redirect(site_url('modelos/editar/') . $this->input->post('idModelos'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro</p></div>';
            }
        }

        $this->data['result'] = $this->modelos_model->getById($this->uri->segment(3));
        $this->data['view'] = 'modelos/editarModelo';
        return $this->layout();
    }

    public function visualizar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vModelo')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar modelos.');
            redirect(base_url());
        }

        if (!$this->uri->segment(3) || !is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('steos');
        }

        $this->data['custom_error'] = '';
        $this->load->model('steos_model');
        $this->data['result'] = $this->modelos_model->getById($this->uri->segment(3));
        $this->data['emitente'] = $this->steos_model->getEmitente();

        $this->data['view'] = 'modelos/visualizarModelo';
        return $this->layout();
    }

    public function imprimir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vModelo')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para imprimir modelos.');
            redirect(base_url());
        }

        if (!$this->uri->segment(3) || !is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('steos');
        }

        $this->data['custom_error'] = '';
        $this->load->model('steos_model');
        $this->data['result'] = $this->modelos_model->getById($this->uri->segment(3));
        $this->data['emitente'] = $this->steos_model->getEmitente();

        $this->load->view('modelos/imprimirModelo', $this->data);
    }

    public function imprimirGarantiaOs()
    {
        if (!$this->uri->segment(3) || !is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('steos');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vGarantia')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para imprimir o Termo de Garantia.');
            redirect(base_url());
        }

        $this->data['custom_error'] = '';
        $this->load->model('steos_model');
        $this->data['osGarantia'] = $this->garantias_model->getByIdOsGarantia($this->uri->segment(3));
        $this->data['emitente'] = $this->steos_model->getEmitente();

        $this->load->view('garantias/imprimirGarantiaOs', $this->data);
    }

    public function excluir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dModelo')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir modelos.');
            redirect(base_url());
        }

        $id = $this->input->post('idModelos') ?: $this->input->post('id');
        if ($id == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir modelo.');
            redirect(site_url('modelos/gerenciar/'));
        }

        if ($this->modelos_model->delete('modelos', 'idModelos', $id) == true) {
            $this->session->set_flashdata('success', 'Modelo excluído com sucesso!');
            log_info('Removeu um modelo. ID: ' . $id);
        } else {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir modelo.');
        }

        redirect(site_url('modelos/gerenciar/'));
    }
}
