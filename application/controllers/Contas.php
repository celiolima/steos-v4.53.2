<?php
// CI3
// MIGRAÇÃO: Port direto do steos — Fase 3
// Controller: Contas.php
// Responsabilidade: Gerenciamento de contas bancárias com extrato e transferências

defined('BASEPATH') OR exit('No direct script access allowed');

class Contas extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar O.S.');
            redirect(base_url());
        }

        $this->load->helper('form');
        $this->load->model('contas_model');
        $this->load->model('lancamentos_contas_model');
        $this->data['menuContas'] = 'Contas';
        $this->data['menuConfiguracoes'] = 'Configurações';
    }

    public function index()
    {
        $this->gerenciar();
    }

    // Coleta dados para enviar para views contas tab1
    public function gerenciar()
    {
        $this->load->library('pagination');
        $this->data['configuration']['base_url'] = base_url() . 'index.php/contas/gerenciar/';
        $this->data['configuration']['total_rows'] = $this->contas_model->count('contas');
        $this->pagination->initialize($this->data['configuration']);
        $this->data['contas'] = $this->contas_model->get('contas', '*', '', $this->data['configuration']['per_page'], $this->uri->segment(3));

        $this->data['conta']  = $this->contas_model->getById(1);
        $this->data['bancos'] = $this->contas_model->getAll('bancos');
        $this->data['tab']    = 1;
        $this->data['view']   = 'contas/contas';
        return $this->layout();
    }

    public function conta()
    {
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if (!empty($this->uri->segment(3))) {
            $this->load->library('pagination');
            $this->data['configuration']['total_rows'] = $this->contas_model->count('contas');
            $this->pagination->initialize($this->data['configuration']);
            $this->data['conta'] = $this->contas_model->get('contas', '*', '', $this->data['configuration']['per_page']);

            $idConta = $this->uri->segment(3);
            $this->data['conta']            = $this->contas_model->getById($idConta);
            $this->data['lancamentos_conta'] = $this->lancamentos_contas_model->getAllFild('lancamentos_contas', $this->uri->segment(3));
        }

        if (!empty($this->input->post())) {
            $idContas     = $this->input->post('idContas');
            $conta        = $this->input->post('conta');
            $saldo        = $this->input->post('saldo');
            $entrada      = $this->input->post('entrada');
            $totE         = (float)$saldo + (float)$entrada;
            $saida        = $this->input->post('saida');
            $totS         = (float)$saldo - (float)$saida;
            $trasferencia = $this->input->post('trasferencia');

            // Verifica se a variável 'entrada' existe no array _post
            if (!empty(set_value('entrada'))) {
                $conta = $this->contas_model->getById($idConta);
                if ((float)$saldo == (float)$conta->saldo) {
                    if ((float)$entrada >= 0) {
                        $data = [
                            'contas_id'      => $idContas,
                            'lancamento'     => $entrada,
                            'saldo'          => $totE,
                            'tipo_lacamento' => 'ENTRADA',
                            'dataLancamento' => date('Y/m/d H:i:s')
                        ];
                        $dataContas = ['saldo' => $totE];
                        if ($this->contas_model->edit('contas', $dataContas, 'idContas', $idContas) && $this->contas_model->add('lancamentos_contas', $data, true)) {
                            $this->session->set_flashdata('success', 'Entrada inserida com sucesso!');
                            $this->data['reload'] = 1;
                        }
                    } else {
                        $this->session->set_flashdata('error', 'Você deve inserir um valor válido');
                    }
                }
                $this->data['reload'] = 1;
            }

            // Verifica se a variável 'saida' existe no array _post
            if (!empty(set_value('saida'))) {
                $conta = $this->contas_model->getById($idConta);
                if ((float)$saldo == (float)$conta->saldo) {
                    if ((float)$entrada >= 0) {
                        $data = [
                            'contas_id'      => $idContas,
                            'lancamento'     => $saida,
                            'saldo'          => $totS,
                            'tipo_lacamento' => 'SAIDA',
                            'dataLancamento' => date('Y/m/d H:i:s')
                        ];
                        $dataContas = ['saldo' => $totS];
                        if ($this->contas_model->edit('contas', $dataContas, 'idContas', $idContas) && $this->contas_model->add('lancamentos_contas', $data, true)) {
                            $this->session->set_flashdata('success', 'Saída inserida com sucesso!');
                            $this->data['reload'] = 1;
                        }
                    } else {
                        $this->session->set_flashdata('error', 'Você deve inserir um valor válido');
                    }
                }
                $this->data['reload'] = 1;
            }

            // Transferência entre contas
            if (!empty(set_value('trasferencia'))) {
                $contaSaida = $this->contas_model->getById($idConta);
                $contaTras  = $this->contas_model->getById($this->input->post('contasTrans'));

                if ((float)$saldo == (float)$contaSaida->saldo) {
                    if ($conta != $contaTras->conta) {
                        $idContasTrans = $contaTras->idContas;
                        $saldoTrans    = $contaTras->saldo;
                        $totT          = (float)$saldoTrans + (float)$trasferencia;

                        if ((float)$trasferencia >= 0) {
                            $dataT      = ['contas_id' => $idContasTrans, 'lancamento' => $trasferencia, 'saldo' => $totT, 'tipo_lacamento' => 'TRANSFERENCIA', 'dataLancamento' => date('Y/m/d H:i:s')];
                            $dataContasT = ['saldo' => $totT];
                            $totS        = (float)$saldo - (float)$trasferencia;
                            $dataS      = ['contas_id' => $idContas, 'lancamento' => $trasferencia, 'saldo' => $totS, 'tipo_lacamento' => 'SAIDA', 'dataLancamento' => date('Y/m/d H:i:s')];
                            $dataContasS = ['saldo' => $totS];

                            if ($this->contas_model->edit('contas', $dataContasT, 'idContas', $idContasTrans) && $this->contas_model->add('lancamentos_contas', $dataT, true)) {
                                if ($this->contas_model->edit('contas', $dataContasS, 'idContas', $idContas) && $this->contas_model->add('lancamentos_contas', $dataS, true)) {
                                    $this->session->set_flashdata('success', 'Transferência realizada com sucesso!');
                                    $this->data['reload'] = 1;
                                }
                            }
                        } else {
                            $this->session->set_flashdata('error', 'Você deve inserir um valor válido');
                        }
                    } else {
                        $this->session->set_flashdata('error', 'Você não pode transferir para a mesma conta');
                    }
                }
            }
        }

        $this->data['conta']  = $this->contas_model->getById($this->uri->segment(3));
        $this->data['bancos'] = $this->contas_model->getAll('bancos');
        $this->data['contas'] = $this->contas_model->getAll('contas');
        $this->data['tab']    = 2;
        $this->data['view']   = 'contas/contas';
        return $this->layout();
    }

    public function adicionar()
    {
        if (!empty($this->input->post())) {
            $data = [
                'conta'             => $this->input->post('conta'),
                'banco'             => $this->input->post('banco'),
                'tipo'              => $this->input->post('tipo'),
                'agencia'           => $this->input->post('agencia'),
                'numero'            => $this->input->post('numero'),
                'saldo'             => $this->input->post('saldo'),
                'vencimento_cartao' => $this->input->post('vencimento_cartao'),
                'status'            => 1,
                'cadastro'          => date('Y/m/d')
            ];

            if ($this->contas_model->add('contas', $data) == true) {
                $this->session->set_flashdata('success', 'Conta adicionada com sucesso!');
                $json = ['result' => true];
                echo json_encode($json);
                die();
            } else {
                $this->session->set_flashdata('error', 'Ocorreu um erro ao tentar adicionar uma Conta.');
                $json = ['result' => false];
                echo json_encode($json);
                die();
            }
        }
    }

    public function excluir()
    {
        if (!empty($this->input->post())) {
            $id = $this->input->post('id');
            if ($id == null) {
                $this->session->set_flashdata('error', 'Erro ao tentar excluir CONTA.');
                redirect(site_url('contas/gerenciar/'));
            }

            $this->contas_model->delete('lancamentos_contas', 'contas_id', $id);
            $this->contas_model->delete('contas', 'idContas', $id);

            log_info('Removeu uma conta. ID: ' . $id);

            $this->session->set_flashdata('success', 'Conta e Lançamentos excluídos com sucesso!');
            redirect(site_url('contas/gerenciar/'));
        }
    }
}
