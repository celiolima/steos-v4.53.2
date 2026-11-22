<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Permissoes extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para configurar as permissões no sistema.');
            redirect(base_url());
        }

        $this->load->helper(['form', 'codegen_helper']);
        $this->load->model('permissoes_model');
        $this->data['menuConfiguracoes'] = 'Permissões';
    }

    public function index()
    {
        $this->gerenciar();
    }

    public function gerenciar()
    {
        $this->load->library('pagination');

        $this->data['configuration']['base_url'] = site_url('permissoes/gerenciar/');
        $this->data['configuration']['total_rows'] = $this->permissoes_model->count('permissoes');

        $this->pagination->initialize($this->data['configuration']);

        $this->data['results'] = $this->permissoes_model->get('permissoes', 'idPermissao,nome,data,situacao', '', $this->data['configuration']['per_page'], $this->uri->segment(3));

        $this->data['view'] = 'permissoes/permissoes';

        return $this->layout();
    }

    public function adicionar()
    {
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('nome', 'Nome', 'trim|required');
        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $nomePermissao = $this->input->post('nome');
            $cadastro = date('Y-m-d');
            $situacao = 1;

            $permissoes = json_encode($this->coletarPermissoesPost());

            $data = [
                'nome' => $nomePermissao,
                'data' => $cadastro,
                'permissoes' => $permissoes,
                'situacao' => $situacao,
            ];

            if ($this->permissoes_model->add('permissoes', $data) == true) {
                $this->session->set_flashdata('success', 'Permissão adicionada com sucesso!');
                log_info('Adicionou uma permissão');
                redirect(site_url('permissoes/adicionar/'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
            }
        }

        $this->data['view'] = 'permissoes/adicionarPermissao';

        return $this->layout();
    }

    public function editar()
    {
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('nome', 'Nome', 'trim|required');
        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $nomePermissao = $this->input->post('nome');
            $situacao = $this->input->post('situacao');
            $permissoes = json_encode($this->coletarPermissoesPost());

            $data = [
                'nome' => $nomePermissao,
                'permissoes' => $permissoes,
                'situacao' => $situacao,
            ];

            if ($this->permissoes_model->edit('permissoes', $data, 'idPermissao', $this->input->post('idPermissao')) == true) {
                $this->session->set_flashdata('success', 'Permissão editada com sucesso!');
                log_info('Alterou uma permissão. ID: ' . $this->input->post('idPermissao'));
                redirect(site_url('permissoes/editar/') . $this->input->post('idPermissao'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um errro.</p></div>';
            }
        }

        $this->data['result'] = $this->permissoes_model->getById($this->uri->segment(3));

        $this->data['view'] = 'permissoes/editarPermissao';

        return $this->layout();
    }

    public function desativar()
    {
        $id = $this->input->post('id');
        if (! $id) {
            $this->session->set_flashdata('error', 'Erro ao tentar desativar permissão.');
            redirect(site_url('permissoes/gerenciar/'));
        }
        $data = [
            'situacao' => false,
        ];
        if ($this->permissoes_model->edit('permissoes', $data, 'idPermissao', $id)) {
            log_info('Desativou uma permissão. ID: ' . $id);
            $this->session->set_flashdata('success', 'Permissão desativada com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao desativar permissão!');
        }

        redirect(site_url('permissoes/gerenciar/'));
    }

    private function coletarPermissoesPost(): array
    {
        $keys = [
            'aCliente', 'eCliente', 'dCliente', 'vCliente',
            'aProduto', 'eProduto', 'dProduto', 'vProduto',
            'aServico', 'eServico', 'dServico', 'vServico',
            'aOs', 'eOs', 'dOs', 'vOs',
            'aVenda', 'eVenda', 'dVenda', 'vVenda',
            'aArquivo', 'eArquivo', 'dArquivo', 'vArquivo',
            'aPagamento', 'ePagamento', 'dPagamento', 'vPagamento',
            'aLancamento', 'eLancamento', 'dLancamento', 'vLancamento',
            'cUsuario', 'cEmitente', 'cPermissao', 'cBackup', 'cAuditoria', 'cEmail', 'cSistema',
            'rCliente', 'rProduto', 'rServico', 'rOs', 'rVenda', 'rFinanceiro', 'rComissao',
            'vGarantia', 'aGarantia', 'eGarantia', 'dGarantia',
            'aCobranca', 'eCobranca', 'dCobranca', 'vCobranca',
            'aVeiculo', 'eVeiculo', 'dVeiculo', 'vVeiculo',
            'vContrato', 'aContrato', 'eContrato', 'dContrato',
            'vNfse', 'aNfse', 'eNfse', 'dNfse',
            'vEquipamento', 'aEquipamento', 'eEquipamento', 'dEquipamento',
            'vSistema', 'aSistema', 'eSistema', 'dSistema',
            'vModelo', 'aModelo', 'eModelo', 'dModelo',
            'vTecnico', 'aTecnico', 'eTecnico', 'dTecnico',
            'vConta', 'aConta', 'eConta', 'dConta',
            'vCadFinanceiro', 'aCadFinanceiro', 'eCadFinanceiro', 'dCadFinanceiro',
            'vLogsAsaas', 'cAsaas',
        ];

        $permissoes = [];
        foreach ($keys as $key) {
            $permissoes[$key] = $this->input->post($key);
        }

        return $permissoes;
    }
}

/* End of file permissoes.php */
/* Location: ./application/controllers/permissoes.php */
