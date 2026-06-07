<?php
// CI3
// MIGRAÇÃO: Port direto do steos — Fase 3
// Controller: Gasolina.php
// Responsabilidade: Registro de abastecimentos e histórico de odômetro

defined('BASEPATH') OR exit('No direct script access allowed');

class Gasolina extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cUsuario')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para configurar os usuários.');
            redirect(base_url());
        }

        $this->load->helper('form');
        $this->load->model('gasolina_model');
        $this->data['menuGasolina']      = 'Gasolina';
        $this->data['menuConfiguracoes'] = 'Configurações';
    }

    public function index()
    {
        $this->gerenciar();
    }

    // Coleta dados para enviar para views gasolina
    public function gerenciar()
    {
        $this->load->library('pagination');

        $this->data['configuration']['base_url']  = base_url() . 'index.php/gasolina/gerenciar/';
        $this->data['configuration']['total_rows'] = $this->gasolina_model->count('gasolina');

        $this->pagination->initialize($this->data['configuration']);

        $this->data['veiculos'] = $this->gasolina_model->get('veiculos');
        $this->data['results']  = $this->gasolina_model->getAll('gasolina', $this->data['configuration']['per_page'], $this->uri->segment(3));

        $this->data['view'] = 'gasolina/gasolina';
        return $this->layout();
    }

    // Coleta dados para enviar para views gasolinaTable
    public function listar()
    {
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->input->get()) {
            $nomeVelculo = $this->input->get('nomeVeiculo');
            $mes         = $this->input->get('mes');

            $veiculo   = $this->data['veiculo']   = $this->gasolina_model->getName('veiculos', $nomeVelculo);
            $resultado2 = $this->data['results1'] = $this->gasolina_model->getByIdEx($veiculo->idVeiculos, $this->data['configuration']['per_page'], $this->uri->segment(3));
        }

        if ($this->input->post()) {
            $veiculos_id    = $this->input->post('idveiculos');
            $nomeVelculo    = $this->input->post('nomeVeiculo');
            $saldoAtualVeic = $this->input->post('saldoAtualVeic');

            if (set_value('entrada')) {
                $entrada = $this->input->post('entrada');
                if ((int)(set_value('saldoAtualVeic') < 20)) {
                    $dataVeiculo = ['abastecer' => 1];
                    $this->gasolina_model->edit('veiculos', $dataVeiculo, 'idVeiculos', $veiculos_id);
                }
                $veiculos = $this->gasolina_model->getById('veiculos', 'idVeiculos', $veiculos_id);
                if (((int)$veiculos->oleoKmVeloc - (int)$entrada) < 150) {
                    $dataVeiculo = ['trocarOleo' => 1];
                    $this->gasolina_model->edit('veiculos', $dataVeiculo, 'idVeiculos', $veiculos_id);
                }
                $array           = "veiculos_id= '" . $veiculos_id . "'AND velocimetroEntrada >'0' AND velocimetroSaida >'0'";
                $saidaMaioQZero  = $this->gasolina_model->getLastId('gasolina', $array);
                $array           = "veiculos_id= '" . $veiculos_id . "'";
                $saida           = $this->gasolina_model->getLastId('gasolina', $array);

                if ($saidaMaioQZero == $saida) {
                    $data = [
                        'veiculos_id'        => set_value('idveiculos'),
                        'saldoAnterior'      => set_value('saldoAnteriorVeic'),
                        'saldoAtual'         => set_value('saldoAtualVeic'),
                        'velocimetroEntrada' => set_value('entrada'),
                        'dataLancamento'     => set_value('dataLancamento')
                    ];
                    if ($this->gasolina_model->add('gasolina', $data) == true) {
                        $this->session->set_flashdata('success', 'Entrada inserida com sucesso!');
                        log_info('Adicionou Entrada.');
                        $veiculo   = $this->data['veiculo']   = $this->gasolina_model->getName('veiculos', $nomeVelculo);
                        $resultado2 = $this->data['results1'] = $this->gasolina_model->getByIdEx($veiculo->idVeiculos, $this->data['configuration']['per_page'], $this->uri->segment(3));
                    } else {
                        $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
                    }
                } else {
                    $veiculo   = $this->data['veiculo']   = $this->gasolina_model->getName('veiculos', $nomeVelculo);
                    $resultado2 = $this->data['results1'] = $this->gasolina_model->getByIdEx($veiculo->idVeiculos, $this->data['configuration']['per_page'], $this->uri->segment(3));
                    $this->data['custom_error'] = '<p>Você deve cadastrar uma Saída.</p>';
                }
            }

            if (set_value('saida')) {
                $saida = $this->input->post('saida');
                if ((int)(set_value('saldoAtualVeic') < 20)) {
                    $dataVeiculo = ['abastecer' => 1];
                    $this->gasolina_model->edit('veiculos', $dataVeiculo, 'idVeiculos', $veiculos_id);
                }
                $veiculos = $this->gasolina_model->getById('veiculos', 'idVeiculos', $veiculos_id);
                if (((int)$veiculos->oleoKmVeloc - (int)$saida) < 100) {
                    $dataVeiculo = ['trocarOleo' => 1];
                    $this->gasolina_model->edit('veiculos', $dataVeiculo, 'idVeiculos', $veiculos_id);
                }
                $arraySaidaMaioQZero = "veiculos_id= '" . $veiculos_id . "'AND velocimetroEntrada >'0' AND velocimetroSaida ='0'";
                $saidaMaioQZero      = $this->gasolina_model->getLastId('gasolina', $arraySaidaMaioQZero);
                $arraySaida          = "veiculos_id= '" . $veiculos_id . "'";
                $saida               = $this->gasolina_model->getLastId('gasolina', $arraySaida);
                $postSaida           = $this->input->post('saida');
                $array               = "idGasolina = '" . $saidaMaioQZero->idGasolina . "'";
                $velocimetroEntrada  = $this->gasolina_model->getLastFild('gasolina', 'velocimetroEntrada', $array);
                $saldoAtual          = ((int)$saldoAtualVeic - ((int)$postSaida - (int)$velocimetroEntrada->velocimetroEntrada));

                if ($saidaMaioQZero->idGasolina == $saida->idGasolina && (int)$postSaida > (int)$velocimetroEntrada->velocimetroEntrada) {
                    $dataVeiculo  = ['saldoAtualVeic' => $saldoAtual];
                    $dataGasolina = [
                        'saldoAtual'        => $saldoAtual,
                        'velocimetroSaida'  => $postSaida,
                        'dataLancamento'    => set_value('dataLancamento')
                    ];
                    if ($this->gasolina_model->edit('veiculos', $dataVeiculo, 'idVeiculos', $veiculos_id) && $this->gasolina_model->edit('gasolina', $dataGasolina, 'idGasolina', $saida->idGasolina)) {
                        $this->session->set_flashdata('success', 'Saída Alterada com sucesso!');
                        log_info('Alterada Saída.');
                        $veiculo   = $this->data['veiculo']   = $this->gasolina_model->getName('veiculos', $nomeVelculo);
                        $resultado2 = $this->data['results1'] = $this->gasolina_model->getByIdEx($veiculo->idVeiculos, $this->data['configuration']['per_page'], $this->uri->segment(3));
                    } else {
                        $veiculo   = $this->data['veiculo']   = $this->gasolina_model->getName('veiculos', $nomeVelculo);
                        $resultado2 = $this->data['results1'] = $this->gasolina_model->getByIdEx($veiculo->idVeiculos, $this->data['configuration']['per_page'], $this->uri->segment(3));
                        $this->data['custom_error'] = '<p>Ocorreu um erro.</p>';
                    }
                } else {
                    if ($saidaMaioQZero->idGasolina != $saida->idGasolina) {
                        $veiculo   = $this->data['veiculo']   = $this->gasolina_model->getName('veiculos', $nomeVelculo);
                        $resultado2 = $this->data['results1'] = $this->gasolina_model->getByIdEx($veiculo->idVeiculos, $this->data['configuration']['per_page'], $this->uri->segment(3));
                        $this->data['custom_error'] = '<p>Você deve cadastrar uma Entrada.</p>';
                    }
                    if ((int)$postSaida < (int)$velocimetroEntrada->velocimetroEntrada) {
                        $veiculo   = $this->data['veiculo']   = $this->gasolina_model->getName('veiculos', $nomeVelculo);
                        $resultado2 = $this->data['results1'] = $this->gasolina_model->getByIdEx($veiculo->idVeiculos, $this->data['configuration']['per_page'], $this->uri->segment(3));
                        $this->data['custom_error'] = '<p>A saída deve ser maior que a entrada.</p>';
                    }
                }
            }

            if (set_value('gasolina')) {
                $idVeiculos         = $this->input->post('idveiculos');
                $abastecimentoLitro = $this->input->post('gasolina');
                $autonomia          = $this->input->post('autonomia');
                $saldoAtualVeic     = $this->input->post('saldoAtualVeic');
                $abastecimentoKm    = ((int)$abastecimentoLitro * (int)$autonomia);

                $dataVeiculo = [
                    'idVeiculos'              => $this->input->post('idveiculos'),
                    'abastecimentoKm'         => $abastecimentoKm,
                    'ultimoAbastecimentoData' => $this->input->post('ultimoAbastecimentoData'),
                    'saldoAtualVeic'          => $saldoAtualVeic + $abastecimentoKm,
                    'abastecer'               => 0
                ];

                if ($this->gasolina_model->edit('veiculos', $dataVeiculo, 'idVeiculos', $idVeiculos)) {
                    $this->session->set_flashdata('success', 'Gasolina Alterada com sucesso!');
                    log_info('Alterada Saída.');
                    $veiculo   = $this->data['veiculo']   = $this->gasolina_model->getName('veiculos', $nomeVelculo);
                    $resultado2 = $this->data['results1'] = $this->gasolina_model->getByIdEx($veiculo->idVeiculos, $this->data['configuration']['per_page'], $this->uri->segment(3));
                } else {
                    $veiculo   = $this->data['veiculo']   = $this->gasolina_model->getName('veiculos', $nomeVelculo);
                    $resultado2 = $this->data['results1'] = $this->gasolina_model->getByIdEx($veiculo->idVeiculos, $this->data['configuration']['per_page'], $this->uri->segment(3));
                    $this->data['custom_error'] = '<p>Ocorreu um erro.</p>';
                }
            }

            if (set_value('oleo')) {
                $idVeiculos = $this->input->post('idveiculos');
                $dataVeiculo = [
                    'ultimaTrocaDeOleoData' => $this->input->post('ultimaTrocaDeOleoData'),
                    'ultimaTrocaOleoVeloc'  => $this->input->post('oleo'),
                    'oleoKmVeloc'           => (int)($this->input->post('oleoKm')) + (int)($this->input->post('oleo')),
                    'trocarOleo'            => 0
                ];

                if ($this->gasolina_model->edit('veiculos', $dataVeiculo, 'idVeiculos', $idVeiculos)) {
                    $this->session->set_flashdata('success', 'Óleo Alterado com sucesso!');
                    log_info('Alterado Óleo.');
                    $veiculo   = $this->data['veiculo']   = $this->gasolina_model->getName('veiculos', $nomeVelculo);
                    $resultado2 = $this->data['results1'] = $this->gasolina_model->getByIdEx($veiculo->idVeiculos, $this->data['configuration']['per_page'], $this->uri->segment(3));
                } else {
                    $veiculo   = $this->data['veiculo']   = $this->gasolina_model->getName('veiculos', $nomeVelculo);
                    $resultado2 = $this->data['results1'] = $this->gasolina_model->getByIdEx($veiculo->idVeiculos, $this->data['configuration']['per_page'], $this->uri->segment(3));
                    $this->data['custom_error'] = '<p>Ocorreu um erro.</p>';
                }
            }
        }

        $this->data['view'] = 'gasolina/gasolinaTable';
        return $this->layout();
    }
}
