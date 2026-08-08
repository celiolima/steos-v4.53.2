<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Bancos extends MY_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para acessar esta área.');
            redirect(base_url());
        }
        $this->load->model('Bancos_model', 'model');
        $this->data['menuLancamentos'] = 'financeiro';
    }

    public function index() {
        $this->gerenciar();
    }

    public function gerenciar() {
        $this->load->library('pagination');

        $config['base_url'] = base_url() . 'index.php/bancos/gerenciar/';
        $config['total_rows'] = $this->model->count('bancos');
        $config['per_page'] = 10;
        $config['next_link'] = 'Próxima';
        $config['prev_link'] = 'Anterior';
        $config['full_tag_open'] = '<div class="pagination alternate"><ul>';
        $config['full_tag_close'] = '</ul></div>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li><a style="color: #2D335B"><b>';
        $config['cur_tag_close'] = '</b></a></li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['first_link'] = 'Primeira';
        $config['last_link'] = 'Última';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';

        $this->pagination->initialize($config);

        $this->data['results'] = $this->model->get('bancos', '*', '', $config['per_page'], $this->uri->segment(3));

        $this->data['view'] = 'bancos/bancos';
        return $this->layout();
    }

    public function adicionar() {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aLancamento')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('nome', 'Nome do Banco', 'trim|required');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $data = [
                'nome' => $this->input->post('nome'),
            ];

            if ($this->model->add('bancos', $data) == True) {
                $this->session->set_flashdata('success', 'Bancos adicionado com sucesso!');
                redirect(base_url() . 'index.php/bancos/adicionar/');
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
            }
        }
        $this->data['view'] = 'bancos/adicionar';
        return $this->layout();
    }

    public function editar() {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eLancamento')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('nome', 'Nome do Banco', 'trim|required');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $data = [
                'nome' => $this->input->post('nome'),
            ];

            if ($this->model->edit('bancos', $data, 'id', $this->input->post('id')) == True) {
                $this->session->set_flashdata('success', 'Bancos editado com sucesso!');
                redirect(base_url() . 'index.php/bancos/editar/' . $this->input->post('id'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro</p></div>';
            }
        }

        $this->data['result'] = $this->model->getById($this->uri->segment(3));
        $this->data['view'] = 'bancos/editar';
        return $this->layout();
    }

    public function excluir() {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dLancamento')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir.');
            redirect(base_url());
        }

        $id = $this->input->post('id');
        if ($id == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir.');
            redirect(base_url() . 'index.php/bancos/gerenciar/');
        }

        $this->model->delete('bancos', 'id', $id);

        $this->session->set_flashdata('success', 'Bancos excluído com sucesso!');
        redirect(base_url() . 'index.php/bancos/gerenciar/');
    }
}
