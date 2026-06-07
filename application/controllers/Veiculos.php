<?php
// CI3
// MIGRAÇÃO: Port direto do steos — Fase 3
// Controller: Veiculos.php
// Responsabilidade: Gestão de frota, alertas de abastecimento e troca de óleo
// ⚠️ SEGURANÇA: Este controller não verifica permissões (checkPermission).
// Qualquer usuário autenticado pode acessar. Adicionar verificação de permissão
// conforme política de acesso definida pelo negócio.
// Registrado em CONFLICTS.md — CONFLITO #6

defined('BASEPATH') OR exit('No direct script access allowed');

class Veiculos extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        // ⚠️ SEGURANÇA: checkPermission habilitado (Steos Migration)
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para acessar os Veículos da frota.');
            redirect(base_url());
        }

        $this->load->helper('form');
        $this->load->model('gasolina_model');
        $this->load->model('veiculos_model');
        $this->data['menuGasolina']      = 'Gasolina';
        $this->data['menuConfiguracoes'] = 'Configurações';
    }

    public function index()
    {
        $this->gerenciar();
    }

    // Coleta dados para enviar para views veiculos tab1
    public function gerenciar()
    {
        $this->load->library('pagination');

        $this->data['configuration']['base_url']  = base_url() . 'index.php/veiculos/gerenciar/';
        $this->data['configuration']['total_rows'] = $this->veiculos_model->count('veiculos');
        $this->pagination->initialize($this->data['configuration']);

        $this->data['veiculos'] = $this->veiculos_model->get('veiculos', '*', '', $this->data['configuration']['per_page'], $this->uri->segment(3));
        $this->data['veiculo']  = $this->veiculos_model->getById('1');
        $this->data['gasolina'] = $this->gasolina_model->getAll('gasolina', $this->data['configuration']['per_page']);

        $this->data['tab']  = 1;
        $this->data['view'] = 'veiculos/veiculos';
        return $this->layout();
    }

    public function veiculo()
    {
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        // Envia para veiculo e gasolina byId
        if (!empty($this->uri->segment(3))) {
            $this->load->library('pagination');
            $this->data['configuration']['total_rows'] = $this->veiculos_model->count('veiculos');
            $this->pagination->initialize($this->data['configuration']);
            $this->data['veiculos'] = $this->veiculos_model->get('veiculos', '*', '', $this->data['configuration']['per_page']);

            $idVeiculo = $this->uri->segment(3);
            $this->data['veiculo']  = $this->veiculos_model->getById($idVeiculo);
            $this->data['gasolina'] = $this->gasolina_model->getByIdEx($idVeiculo, $this->data['configuration']['per_page']);
        }

        $this->data['tab']  = 2;
        $this->data['view'] = 'veiculos/veiculos';
        return $this->layout();
    }

    // Coleta dados para enviar para views veiculos tab3
    public function gasolina()
    {
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if (!empty($this->input->post())) {
            $veiculos_id    = $this->input->post('idveiculos');
            $nomeVelculo    = $this->input->post('nomeVeiculo');
            $oleoProxTroc   = $this->input->post('oleoProxTroc');
            $saldoAtualVeic = $this->input->post('saldoAtualVeic');

            // Verifica se a variável 'entrada' existe no array _post
            if (!empty(set_value('entrada'))) {
                $entrada = $this->input->post('entrada');

                $array                   = "veiculos_id= '" . $veiculos_id . "'";
                $ultimoResistro          = $this->gasolina_model->getLastId('gasolina', $array);
                $array                   = "veiculos_id= '" . $veiculos_id . "'AND velocimetroEntrada >'0' AND velocimetroSaida > '0'";
                $ultimoRegistroPreenchido = $this->gasolina_model->getLastId('gasolina', $array);

                if ((int)$ultimoRegistroPreenchido->idGasolina == (int)$ultimoResistro->idGasolina) {
                    $velocimetroSaida = $this->gasolina_model->getByIdByFild($ultimoRegistroPreenchido->idGasolina, 'velocimetroSaida');
                    if ((int)$entrada > ((int)$velocimetroSaida->velocimetroSaida + 150)) {
                        $this->session->set_flashdata('error', 'O lançamento Entrada não deve superar 150km do último lançamento');
                    } elseif ((int)$entrada < (int)$velocimetroSaida->velocimetroSaida) {
                        $this->session->set_flashdata('error', 'O lançamento Entrada não deve ser menor que o último lançamento');
                    } else {
                        if (((int)$oleoProxTroc - (int)$entrada) < 100) {
                            $dataVeiculo = ['trocarOleo' => 1];
                            $this->veiculos_model->edit('veiculos', $dataVeiculo, 'idVeiculos', $veiculos_id);
                        }
                        $data = [
                            'veiculos_id'        => $veiculos_id,
                            'saldoAtual'         => 0,
                            'velocimetroEntrada' => $entrada,
                            'dataLancamento'     => date('Y/m/d H:i:s')
                        ];
                        if (is_numeric($id = $this->gasolina_model->add('gasolina', $data, true))) {
                            $this->session->set_flashdata('success', 'Entrada inserida com sucesso!');
                            log_info('Adicionou entrada de odômetro. ID: ' . $id);
                        }
                    }
                } else {
                    $this->session->set_flashdata('error', 'Você deve inserir uma Saída primeiro');
                }
            }

            // Verifica se a variável 'saida' existe no array _post
            if (!empty(set_value('saida'))) {
                $saida = $this->input->post('saida');

                $array                   = "veiculos_id= '" . $veiculos_id . "'";
                $ultimoResistro          = $this->gasolina_model->getLastId('gasolina', $array);
                $array                   = "veiculos_id= '" . $veiculos_id . "'AND velocimetroEntrada >'0' AND velocimetroSaida = '0'";
                $ultimoRegistroPreenchido = $this->gasolina_model->getLastId('gasolina', $array);

                if ((int)$ultimoRegistroPreenchido->idGasolina == (int)$ultimoResistro->idGasolina) {
                    $velocimetroEntrada = $this->gasolina_model->getByIdByFild($ultimoRegistroPreenchido->idGasolina, 'velocimetroEntrada');

                    if ((int)$saida > ((int)$velocimetroEntrada->velocimetroEntrada + 100)) {
                        $this->session->set_flashdata('error', 'O lançamento saída não deve superar 100km do último lançamento');
                    } elseif ((int)$saida < (int)$velocimetroEntrada->velocimetroEntrada) {
                        $this->session->set_flashdata('error', 'O lançamento saída não deve ser menor que o último lançamento');
                    } else {
                        if (((int)$oleoProxTroc - (int)$saida) < 100) {
                            $dataVeiculo = ['trocarOleo' => 1];
                            $this->gasolina_model->edit('veiculos', $dataVeiculo, 'idVeiculos', $veiculos_id);
                        }
                        $saldoAtual = ((int)$saldoAtualVeic - ((int)$saida - (int)$velocimetroEntrada->velocimetroEntrada));

                        if ((int)$saldoAtual < 30) {
                            $dataVeiculo  = ['abastecer' => 1, 'saldoAtualVeic' => $saldoAtual];
                            $dataGasolina = ['saldoAtual' => $saldoAtual, 'velocimetroSaida' => $saida, 'dataLancamento' => date('Y/m/d H:i:s')];
                        } else {
                            $dataVeiculo  = ['saldoAtualVeic' => $saldoAtual];
                            $dataGasolina = ['saldoAtual' => $saldoAtual, 'velocimetroSaida' => $saida, 'dataLancamento' => date('Y/m/d H:i:s')];
                        }

                        if ($this->veiculos_model->edit('veiculos', $dataVeiculo, 'idVeiculos', $veiculos_id) && $this->gasolina_model->edit('gasolina', $dataGasolina, 'idGasolina', $ultimoRegistroPreenchido->idGasolina)) {
                            $this->session->set_flashdata('success', 'Saída Alterada com sucesso!');
                            log_info('Alterada Saída de odômetro.');
                        } else {
                            $this->session->set_flashdata('error', 'Algo deu errado!');
                        }
                    }
                } else {
                    $this->session->set_flashdata('error', 'Você deve inserir uma Saída válida');
                }
            }

            // Abastecimento
            if (!empty(set_value('gasolina'))) {
                $idVeiculos         = $this->input->post('idveiculos');
                $abastecimentoLitro = $this->input->post('gasolina');
                $autonomia          = $this->input->post('autonomia');
                $saldoAtualVeic     = $this->input->post('saldoAtualVeic');
                $abastecimentoKm    = ((int)$abastecimentoLitro * (int)$autonomia);
                $saldoAtual         = (int)$saldoAtualVeic + (int)$abastecimentoKm;

                $veiculo = $this->veiculos_model->getById($idVeiculos);

                if (!empty($veiculo->abastecer)) {
                    $dataVeiculo  = ['idVeiculos' => $idVeiculos, 'abastecimentoKm' => $abastecimentoKm, 'ultimoAbastecimentoData' => date('Y/m/d H:i:s'), 'saldoAtualVeic' => $saldoAtual, 'abastecer' => 0];
                    $dataGasolina = ['saldoAtual' => $saldoAtual];
                    $array        = "veiculos_id= '" . $idVeiculos . "'";
                    $ultimoResistro = $this->gasolina_model->getLastId('gasolina', $array);

                    if ($this->gasolina_model->edit('veiculos', $dataVeiculo, 'idVeiculos', $idVeiculos) && $this->gasolina_model->edit('gasolina', $dataGasolina, 'idGasolina', $ultimoResistro->idGasolina)) {
                        $this->session->set_flashdata('success', 'Abastecimento Realizado com sucesso!');
                        log_info('Abastecimento realizado.');
                    } else {
                        $this->session->set_flashdata('error', 'Algo deu errado!');
                    }
                } else {
                    $this->session->set_flashdata('error', 'Você já abasteceu!');
                }
            }

            // Troca de óleo
            if (!empty(set_value('oleo'))) {
                $idVeiculos   = $this->input->post('idveiculos');
                $kmVelocAtual = $this->input->post('oleo');
                $oleoKm       = $this->input->post('oleoKm');
                $proxTroca    = (int)$kmVelocAtual + (int)$oleoKm;

                $veiculo = $this->veiculos_model->getById($idVeiculos);

                if (!empty($veiculo->trocarOleo)) {
                    $dataVeiculo = [
                        'ultimaTrocaDeOleoData' => date('Y/m/d H:i:s'),
                        'ultimaTrocaOleoVeloc'  => $kmVelocAtual,
                        'oleoKmVeloc'           => $proxTroca,
                        'trocarOleo'            => 0
                    ];
                    if ($this->gasolina_model->edit('veiculos', $dataVeiculo, 'idVeiculos', $idVeiculos)) {
                        $this->session->set_flashdata('success', 'Óleo Alterado com sucesso!');
                        log_info('Troca de óleo registrada.');
                    } else {
                        $this->session->set_flashdata('error', 'Algo deu errado!');
                    }
                } else {
                    $this->session->set_flashdata('error', 'Você já inseriu óleo!');
                }
            }
        }

        // Envia para veiculo e gasolina byId
        if (!empty($this->uri->segment(3))) {
            $this->load->library('pagination');
            $this->data['configuration']['total_rows'] = $this->veiculos_model->count('veiculos');
            $this->pagination->initialize($this->data['configuration']);
            $this->data['veiculos'] = $this->veiculos_model->get('veiculos', '*', '', $this->data['configuration']['per_page']);

            $idVeiculo = $this->uri->segment(3);
            $this->data['veiculo']  = $this->veiculos_model->getById($idVeiculo);
            $this->data['gasolina'] = $this->gasolina_model->getByIdEx($idVeiculo, $this->data['configuration']['per_page']);
        }

        $this->data['tab']  = 3;
        $this->data['view'] = 'veiculos/veiculos';
        return $this->layout();
    }
}
