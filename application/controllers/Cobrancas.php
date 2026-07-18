<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cobrancas extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('form');
        $this->load->model('cobrancas_model');
        $this->data['menuCobrancas'] = 'financeiro';
    }

    public function index()
    {
        $this->cobrancas();
    }

    public function adicionar()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'aCobranca')) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(403)
                ->set_output(json_encode(['message' => 'Você não tem permissão para adicionar cobrança!']));
        }

        $this->load->library('form_validation');
        if ($this->form_validation->run('cobrancas') == false) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['message' => validation_errors()]));
        } else {
            $id = $this->input->post('id');
            $tipo = $this->input->post('tipo');
            $formaPagamento = $this->input->post('forma_pagamento');
            $gatewayDePagamento = $this->input->post('gateway_de_pagamento');

            $this->load->model('Os_model');
            $this->load->model('vendas_model');

            $data = [
                'tipo_cobranca' => $this->input->post('tipo_cobranca'),
                'installmentCount' => ($this->input->post('tipo_cobranca') === 'parcelamento' && $this->input->post('installment_count')) ? intval($this->input->post('installment_count')) : 1,
                'vencimento' => $this->input->post('vencimento'),
            ];

            $this->load->library("Gateways/$gatewayDePagamento", null, 'PaymentGateway');

            try {
                $cobranca = $this->PaymentGateway->gerarCobranca(
                    $id,
                    $tipo,
                    $formaPagamento,
                    $data
                );

                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(200)
                    ->set_output(json_encode($cobranca));
            } catch (\Exception $e) {
                $expMsg = $e->getMessage();
                if ($expMsg == 'unauthorized: Must provide your access_token to proceed' || $expMsg == 'Unauthorized') {
                    $expMsg = 'Por favor configurar os dados da API em Config/payment_gatways.php';
                }

                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(500)
                    ->set_output(json_encode(['message' => $expMsg]));
            }
        }
    }

    public function cobrancas()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vCobranca')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar cobrancas.');
            redirect(base_url());
        }

        $this->load->library('pagination');
        $this->load->config('payment_gateways');

        $where_array = [];

        $pesquisa = $this->input->get('pesquisa');
        $os_id = $this->input->get('os_id');
        $vendas_id = $this->input->get('vendas_id');
        $tipo = $this->input->get('tipo');
        $status = $this->input->get('status');
        $inputDe = $this->input->get('data_de');
        $inputAte = $this->input->get('data_ate');
        $valor_de = $this->input->get('valor_de');
        $valor_ate = $this->input->get('valor_ate');

        if ($pesquisa) { $where_array['pesquisa'] = $pesquisa; }
        if ($os_id) { $where_array['os_id'] = $os_id; }
        if ($vendas_id) { $where_array['vendas_id'] = $vendas_id; }
        if ($tipo && $tipo !== 'Todos') { $where_array['tipo'] = $tipo; }
        if ($status && $status !== 'Todos') { $where_array['status'] = $status; }
        if ($inputDe) {
            $deArr = explode('/', $inputDe);
            if (count($deArr) == 3) {
                $where_array['data_de'] = $deArr[2] . '-' . $deArr[1] . '-' . $deArr[0];
            } else {
                $where_array['data_de'] = $inputDe;
            }
        }
        if ($inputAte) {
            $ateArr = explode('/', $inputAte);
            if (count($ateArr) == 3) {
                $where_array['data_ate'] = $ateArr[2] . '-' . $ateArr[1] . '-' . $ateArr[0];
            } else {
                $where_array['data_ate'] = $inputAte;
            }
        }
        if ($valor_de) {
            $v_clean = preg_replace('/[^0-9,.]/', '', $valor_de);
            if (strpos($v_clean, ',') !== false) {
                $v_clean = str_replace('.', '', $v_clean);
                $v_clean = str_replace(',', '.', $v_clean);
            }
            $where_array['valor_de'] = round(floatval($v_clean) * 100);
        }
        if ($valor_ate) {
            $v_clean = preg_replace('/[^0-9,.]/', '', $valor_ate);
            if (strpos($v_clean, ',') !== false) {
                $v_clean = str_replace('.', '', $v_clean);
                $v_clean = str_replace(',', '.', $v_clean);
            }
            $where_array['valor_ate'] = round(floatval($v_clean) * 100);
        }

        $this->data['configuration']['base_url'] = site_url('cobrancas/cobrancas/');
        $this->data['configuration']['total_rows'] = $this->cobrancas_model->count('cobrancas', $where_array);
        if (count($where_array) > 0) {
            $query_params = http_build_query([
                'pesquisa' => $pesquisa,
                'os_id' => $os_id,
                'vendas_id' => $vendas_id,
                'tipo' => $tipo,
                'status' => $status,
                'data_de' => $inputDe,
                'data_ate' => $inputAte,
                'valor_de' => $valor_de,
                'valor_ate' => $valor_ate
            ]);
            $this->data['configuration']['suffix'] = '?' . $query_params;
            $this->data['configuration']['first_url'] = site_url('cobrancas/cobrancas/') . '?' . $query_params;
        }

        $this->pagination->initialize($this->data['configuration']);

        $this->data['results'] = $this->cobrancas_model->get('cobrancas', '*', $where_array, $this->data['configuration']['per_page'], $this->uri->segment(3));

        $this->data['view'] = 'cobrancas/cobrancas';

        return $this->layout();
    }

    public function excluir()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'dCobranca')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir cobranças');
            redirect(site_url('cobrancas/cobrancas/'));
        }
        try {
            $this->cobrancas_model->cancelarPagamento($this->input->post('excluir_id'));

            if ($this->cobrancas_model->delete('cobrancas', 'idCobranca', $this->input->post('excluir_id')) == true) {
                log_info('Removeu uma cobrança. ID' . $this->input->post('excluir_id'));
                $this->session->set_flashdata('success', 'Cobrança excluida com sucesso!');
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro</p></div>';
            }
        } catch (Exception $e) {
            $this->session->set_flashdata('error', $e->getMessage());
        }
        redirect($_SERVER['HTTP_REFERER'] ?? site_url('cobrancas/cobrancas/'));
    }

    public function atualizar()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('steos');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'eCobranca')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para atualizar cobrança.');
            redirect(base_url());
        }
        try {
            $this->load->model('cobrancas_model');
            $this->cobrancas_model->atualizarStatus($this->uri->segment(3));
        } catch (Exception $e) {
            $this->session->set_flashdata('error', $e->getMessage());
        }
        redirect($_SERVER['HTTP_REFERER'] ?? site_url('cobrancas/cobrancas/'));
    }

    public function confirmarPagamento()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'eCobranca')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para confirmar pagamento da cobrança.');
            redirect(base_url());
        }
        try {
            $this->load->model('cobrancas_model');
            $this->cobrancas_model->confirmarPagamento($this->input->post('confirma_id'));
        } catch (Exception $e) {
            $this->session->set_flashdata('error', $e->getMessage());
        }
        redirect($_SERVER['HTTP_REFERER'] ?? site_url('cobrancas/cobrancas/'));
    }

    public function cancelar()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'eCobranca')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para cancelar cobrança.');
            redirect(base_url());
        }
        try {
            $this->load->model('cobrancas_model');
            $this->cobrancas_model->cancelarPagamento($this->input->post('cancela_id'));
        } catch (Exception $e) {
            $this->session->set_flashdata('error', $e->getMessage());
        }
        redirect($_SERVER['HTTP_REFERER'] ?? site_url('cobrancas/cobrancas/'));
    }

    public function visualizar()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('cobrancas');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vCobranca')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar cobranças.');
            redirect(base_url());
        }
        $this->load->model('cobrancas_model');
        $this->load->config('payment_gateways');

        $this->data['result'] = $this->cobrancas_model->getById($this->uri->segment(3));
        if ($this->data['result'] == null) {
            $this->session->set_flashdata('error', 'Cobrança não encontrada.');
            redirect(site_url('cobrancas/'));
        }

        $this->data['view'] = 'cobrancas/visualizarCobranca';

        return $this->layout();
    }

    public function enviarEmail()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('cobrancas');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vCobranca')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar cobranças.');
            redirect(base_url());
        }

        $this->load->model('cobrancas_model');
        $this->cobrancas_model->enviarEmail($this->uri->segment(3));
        $this->session->set_flashdata('success', 'Email adicionado na fila.');

        redirect($_SERVER['HTTP_REFERER'] ?? site_url('cobrancas/cobrancas/'));
    }
}
