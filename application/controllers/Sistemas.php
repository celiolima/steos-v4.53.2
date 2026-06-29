<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Sistemas extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->model('sistemas_model');
        $this->data['menuSistemas'] = 'Sistemas';
    }

    public function index()
    {
        $this->gerenciar();
    }

    public function gerenciar()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar sistemas.');
            redirect(base_url());
        }

        $pesquisa = $this->input->get('pesquisa');
        $this->load->library('pagination');

        $this->data['configuration']['base_url'] = site_url('sistemas/gerenciar/');
        $this->data['configuration']['total_rows'] = $this->sistemas_model->count('sistemas');
        if ($pesquisa) {
            $this->data['configuration']['suffix'] = "?pesquisa={$pesquisa}";
            $this->data['configuration']['first_url'] = base_url("index.php/sistemas")."\?pesquisa={$pesquisa}";
        }

        $this->pagination->initialize($this->data['configuration']);
        $this->data['results'] = $this->sistemas_model->get('sistemas', '*', $pesquisa, $this->data['configuration']['per_page'], $this->uri->segment(3));

        $this->data['view'] = 'sistemas/sistemas';
        return $this->layout();
    }

    public function adicionar()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar sistemas.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('nome', 'Nome', 'trim|required');
        $this->form_validation->set_rules('preco', 'Preço Base', 'trim|required');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $preco = $this->input->post('preco');
            $preco = str_replace(',', '', $preco);
            $data = [
                'nome' => set_value('nome'),
                'descricao' => set_value('descricao'),
                'preco' => $preco,
                'situacao' => set_value('situacao'),
            ];

            if ($this->sistemas_model->add('sistemas', $data) == true) {
                $this->session->set_flashdata('success', 'Sistema adicionado com sucesso!');
                log_info('Adicionou um sistema');
                redirect(site_url('sistemas/gerenciar/'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
            }
        }
        $this->data['view'] = 'sistemas/adicionarSistema';
        return $this->layout();
    }

    public function editar()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3)) || ! $this->sistemas_model->getById($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Sistema não encontrado ou parâmetro inválido.');
            redirect('sistemas/gerenciar');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar sistemas.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('nome', 'Nome', 'trim|required');
        $this->form_validation->set_rules('preco', 'Preço Base', 'trim|required');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $preco = $this->input->post('preco');
            $preco = str_replace(',', '', $preco);
            $data = [
                'nome' => $this->input->post('nome'),
                'descricao' => $this->input->post('descricao'),
                'preco' => $preco,
                'situacao' => $this->input->post('situacao'),
            ];

            if ($this->sistemas_model->edit('sistemas', $data, 'idSistemas', $this->input->post('idSistemas')) == true) {
                $this->session->set_flashdata('success', 'Sistema editado com sucesso!');
                log_info('Alterou um sistema. ID: ' . $this->input->post('idSistemas'));
                redirect(site_url('sistemas/editar/') . $this->input->post('idSistemas'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro</p></div>';
            }
        }

        $this->data['result'] = $this->sistemas_model->getById($this->uri->segment(3));
        $this->data['checks'] = $this->sistemas_model->getChecksBySistema($this->uri->segment(3));
        $this->data['view'] = 'sistemas/editarSistema';

        return $this->layout();
    }

    public function excluir()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir sistemas.');
            redirect(base_url());
        }

        $id = $this->input->post('id');
        if ($id == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir sistema.');
            redirect(base_url() . 'index.php/sistemas/gerenciar/');
        }

        $this->sistemas_model->delete('sistemas_checks', 'sistemas_id', $id);
        $this->sistemas_model->delete('sistemas_contratos', 'sistemas_id', $id);
        $this->sistemas_model->delete('sistemas', 'idSistemas', $id);

        log_info('Removeu um sistema. ID: ' . $id);

        $this->session->set_flashdata('success', 'Sistema excluido com sucesso!');
        redirect(site_url('sistemas/gerenciar/'));
    }

    public function adicionarCheck()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
            echo json_encode(['result' => false, 'messages' => 'Acesso negado.']);
            return;
        }

        $sistemas_id = $this->input->post('sistemas_id');
        $descricao = $this->input->post('descricao');

        if(empty($descricao)) {
            echo json_encode(['result' => false, 'messages' => 'A descrição do check é obrigatória.']);
            return;
        }

        $data = [
            'sistemas_id' => $sistemas_id,
            'descricao' => $descricao
        ];

        if ($this->sistemas_model->add('sistemas_checks', $data)) {
            echo json_encode(['result' => true, 'messages' => 'Checklist adicionado com sucesso.']);
        } else {
            echo json_encode(['result' => false, 'messages' => 'Ocorreu um erro ao adicionar checklist.']);
        }
    }

    public function excluirCheck()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
            echo json_encode(['result' => false, 'messages' => 'Acesso negado.']);
            return;
        }

        $id = $this->input->post('idSistemas_checks');
        
        if ($this->sistemas_model->delete('sistemas_checks', 'idSistemas_checks', $id)) {
            echo json_encode(['result' => true, 'messages' => 'Checklist removido com sucesso.']);
        } else {
            echo json_encode(['result' => false, 'messages' => 'Ocorreu um erro ao remover checklist.']);
        }
    }
}
