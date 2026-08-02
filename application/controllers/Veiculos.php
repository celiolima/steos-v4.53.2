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

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vVeiculo')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar os Veículos da frota.');
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
    public function adicionar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aVeiculo')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar Veículos.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('nomeVeiculo', 'Nome do Veículo', 'trim|required');
        $this->form_validation->set_rules('autonomia', 'Autonomia (km/l)', 'trim|required|numeric');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $data = [
                'nomeVeiculo' => set_value('nomeVeiculo'),
                'observacoes' => set_value('observacoes'),
                'autonomia' => set_value('autonomia'),
                'situacao' => set_value('situacao') == '1' ? 1 : 0,
                'abastecimentoLitro' => 0,
                'valorGasolinAabastecimento' => 0,
                'ultimoAbastecimentoData' => date('Y-m-d H:i:s'),
                'abastecimentoKm' => 0,
                'ultimaTrocaDeOleoData' => date('Y-m-d H:i:s'),
                'ultimaTrocaOleoVeloc' => 0,
                'oleoKmVeloc' => 0,
                'saldoAtualVeic' => 0,
                'abastecer' => 0,
                'trocarOleo' => 0
            ];

            if ($this->veiculos_model->add('veiculos', $data) == true) {
                $this->session->set_flashdata('success', 'Veículo adicionado com sucesso!');
                log_info('Adicionou um veículo');
                redirect(base_url() . 'index.php/veiculos');
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
            }
        }

        $this->data['view'] = 'veiculos/adicionarVeiculo';
        return $this->layout();
    }

    public function editar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eVeiculo')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar Veículos.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('nomeVeiculo', 'Nome do Veículo', 'trim|required');
        $this->form_validation->set_rules('autonomia', 'Autonomia (km/l)', 'trim|required|numeric');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $data = [
                'nomeVeiculo' => $this->input->post('nomeVeiculo'),
                'observacoes' => $this->input->post('observacoes'),
                'autonomia' => $this->input->post('autonomia'),
                'situacao' => $this->input->post('situacao') == '1' ? 1 : 0
            ];

            if ($this->veiculos_model->edit('veiculos', $data, 'idVeiculos', $this->input->post('idVeiculos')) == true) {
                $this->session->set_flashdata('success', 'Veículo editado com sucesso!');
                log_info('Alterou um veículo. ID: ' . $this->input->post('idVeiculos'));
                redirect(base_url() . 'index.php/veiculos/editar/' . $this->input->post('idVeiculos'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
            }
        }

        $this->data['result'] = $this->veiculos_model->getById($this->uri->segment(3));
        if (!$this->data['result']) {
            $this->session->set_flashdata('error', 'Veículo não encontrado.');
            redirect(base_url() . 'index.php/veiculos');
        }

        $this->data['view'] = 'veiculos/editarVeiculo';
        return $this->layout();
    }

    public function excluir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dVeiculo')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir Veículos.');
            redirect(base_url());
        }

        $id = $this->input->post('id');
        if ($id == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir veículo.');
            redirect(base_url() . 'index.php/veiculos');
        }

        // Antes de deletar, deveria deletar/verificar logs no gasolina, mas o db cuida disso se houver FK. 
        // Aqui usaremos deleção lógica ou forçada no veículo? A tabela não tem regra CASCADE visivel.
        // Vamos desativar o veículo (mudar situação para 0). O excluir fisicamente pode quebrar histórico.
        $data = ['situacao' => 0];
        if ($this->veiculos_model->edit('veiculos', $data, 'idVeiculos', $id)) {
            $this->session->set_flashdata('success', 'Veículo desativado com sucesso!');
            log_info('Desativou um veículo. ID: ' . $id);
        } else {
            $this->session->set_flashdata('error', 'Erro ao desativar veículo!');
        }

        redirect(base_url() . 'index.php/veiculos');
    }

    public function logs()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vVeiculo')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar Logs de Veículos.');
            redirect(base_url());
        }

        $this->load->library('pagination');
        $this->data['configuration']['base_url'] = site_url('veiculos/logs/');
        $this->data['configuration']['total_rows'] = $this->veiculos_model->count('veiculos_logs');
        $this->pagination->initialize($this->data['configuration']);
        
        $this->db->select('veiculos_logs.*, veiculos.nomeVeiculo, usuarios.nome as nomeUsuario');
        $this->db->from('veiculos_logs');
        $this->db->join('veiculos', 'veiculos.idVeiculos = veiculos_logs.veiculo_id', 'left');
        $this->db->join('usuarios', 'usuarios.idUsuarios = veiculos_logs.usuario_id', 'left');
        $this->db->order_by('veiculos_logs.data_hora', 'desc');
        $this->db->limit($this->data['configuration']['per_page'], $this->uri->segment(3));
        
        $this->data['logs'] = $this->db->get()->result();

        $this->data['view'] = 'veiculos/logsLancamentos';
        return $this->layout();
    }

    // Função auxiliar para gravar logs
    private function gravarLog($idVeiculo, $tipo, $conteudo) {
        $usuario_id = $this->session->userdata('id_admin') ? $this->session->userdata('id_admin') : 1;
        $data = [
            'veiculo_id' => $idVeiculo,
            'usuario_id' => $usuario_id,
            'tipo' => $tipo,
            'conteudo' => $conteudo,
            'data_hora' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('veiculos_logs', $data);
    }

    // Lançamentos veiculos tab3
    public function gasolina()
    {
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if (!empty($this->input->post())) {
            $veiculos_id    = $this->input->post('idveiculos');
            $nomeVelculo    = $this->input->post('nomeVeiculo');
            $oleoProxTroc   = $this->input->post('oleoProxTroc');
            $saldoAtualVeic = $this->input->post('saldoAtualVeic');
            $hoje = date('Y-m-d');

            // Verifica Permissão de Edição
            $podeEditar = $this->permission->checkPermission($this->session->userdata('permissao'), 'eVeiculo');

            // Pega o registro de hoje (se existir)
            $this->db->where('veiculos_id', $veiculos_id);
            $this->db->like('dataLancamento', $hoje, 'after');
            $this->db->order_by('idGasolina', 'desc');
            $lancamentoHoje = $this->db->get('gasolina')->row();
            
            // Pega o último registro independente do dia
            $this->db->where('veiculos_id', $veiculos_id);
            $this->db->order_by('idGasolina', 'desc');
            $ultimoRegistro = $this->db->get('gasolina')->row();

            // Verifica se a variável 'entrada' existe no array _post
            if (!empty(set_value('entrada'))) {
                $entrada = $this->input->post('entrada');
                
                if ($lancamentoHoje) {
                    if (!$podeEditar) {
                        $this->session->set_flashdata('error', 'Apenas 1 lançamento de odômetro permitido por dia.');
                    } else {
                        // Admin editando a entrada de hoje
                        if ($lancamentoHoje->velocimetroSaida > 0 && $entrada > $lancamentoHoje->velocimetroSaida) {
                            $this->session->set_flashdata('error', 'A Entrada não pode ser maior que a Saída atual!');
                        } else {
                            $this->gasolina_model->edit('gasolina', ['velocimetroEntrada' => $entrada], 'idGasolina', $lancamentoHoje->idGasolina);
                            $this->session->set_flashdata('success', 'Entrada editada com sucesso!');
                            $this->gravarLog($veiculos_id, 'Odômetro Entrada Editada', "Odômetro Entrada alterado para {$entrada}km");
                        }
                    }
                } else {
                    // Sem lançamento hoje. Só pode adicionar se o último estiver concluído (Entrada e Saída) ou não houver último.
                    if (!$ultimoRegistro || ($ultimoRegistro->velocimetroEntrada > 0 && $ultimoRegistro->velocimetroSaida > 0)) {
                        
                        $velocimetroSaida = $ultimoRegistro ? $ultimoRegistro->velocimetroSaida : 0;

                        if ($velocimetroSaida > 0 && (int)$entrada > ((int)$velocimetroSaida + 150)) {
                            $this->session->set_flashdata('error', 'O lançamento Entrada não deve superar 150km do último lançamento');
                        } elseif ($velocimetroSaida > 0 && (int)$entrada < (int)$velocimetroSaida) {
                            $this->session->set_flashdata('error', 'O lançamento Entrada não deve ser menor que o último lançamento');
                        } else {
                            if (((int)$oleoProxTroc > 0) && (((int)$oleoProxTroc - (int)$entrada) < 100)) {
                                $dataVeiculo = ['trocarOleo' => 1];
                                $this->veiculos_model->edit('veiculos', $dataVeiculo, 'idVeiculos', $veiculos_id);
                            }
                            $data = [
                                'veiculos_id'        => $veiculos_id,
                                'saldoAtual'         => 0,
                                'velocimetroEntrada' => $entrada,
                                'dataLancamento'     => date('Y-m-d H:i:s')
                            ];
                            
                            if (is_numeric($id = $this->gasolina_model->add('gasolina', $data, true))) {
                                $this->session->set_flashdata('success', 'Entrada inserida com sucesso!');
                                $this->gravarLog($veiculos_id, 'Odômetro Entrada', "Odômetro de Entrada registrado em {$entrada}km");
                            }
                        }
                    } else {
                        $this->session->set_flashdata('error', 'Você deve inserir uma Saída primeiro no lançamento anterior.');
                    }
                }
            }

            // Verifica se a variável 'saida' existe no array _post
            if (!empty(set_value('saida'))) {
                $saida = $this->input->post('saida');

                if ($ultimoRegistro && $ultimoRegistro->velocimetroEntrada > 0 && $ultimoRegistro->velocimetroSaida == 0) {
                    // Existe um registro ABERTO. Vamos fechá-lo.
                    
                    if ((int)$saida > ((int)$ultimoRegistro->velocimetroEntrada + 100)) {
                        $this->session->set_flashdata('error', 'O lançamento saída não deve superar 100km da entrada atual');
                    } elseif ((int)$saida < (int)$ultimoRegistro->velocimetroEntrada) {
                        $this->session->set_flashdata('error', 'O lançamento saída não deve ser menor que a entrada atual');
                    } else {
                        if (((int)$oleoProxTroc > 0) && (((int)$oleoProxTroc - (int)$saida) < 100)) {
                            $dataVeiculo = ['trocarOleo' => 1];
                            $this->veiculos_model->edit('veiculos', $dataVeiculo, 'idVeiculos', $veiculos_id);
                        }
                        
                        $saldoAtual = ((int)$saldoAtualVeic - ((int)$saida - (int)$ultimoRegistro->velocimetroEntrada));
                        
                        $dataVeiculo  = ['saldoAtualVeic' => $saldoAtual];
                        if ((int)$saldoAtual < 30) {
                            $dataVeiculo['abastecer'] = 1;
                        }
                        $dataGasolina = ['saldoAtual' => $saldoAtual, 'velocimetroSaida' => $saida, 'dataLancamento' => date('Y-m-d H:i:s')];

                        if ($this->veiculos_model->edit('veiculos', $dataVeiculo, 'idVeiculos', $veiculos_id) && $this->gasolina_model->edit('gasolina', $dataGasolina, 'idGasolina', $ultimoRegistro->idGasolina)) {
                            $this->session->set_flashdata('success', 'Saída Registrada com sucesso!');
                            $this->gravarLog($veiculos_id, 'Odômetro Saída', "Odômetro Saída registrado em {$saida}km");
                        } else {
                            $this->session->set_flashdata('error', 'Algo deu errado!');
                        }
                    }
                } else {
                    // Não há registro aberto. Pode ser que o admin queira EDITAR a saída de hoje.
                    if ($lancamentoHoje && $lancamentoHoje->velocimetroSaida > 0) {
                        if (!$podeEditar) {
                            $this->session->set_flashdata('error', 'Apenas 1 lançamento de odômetro permitido por dia.');
                        } else {
                            // Editando saída de hoje
                            if ($saida < $lancamentoHoje->velocimetroEntrada) {
                                $this->session->set_flashdata('error', 'A Saída não pode ser menor que a Entrada atual!');
                            } else {
                                $saldoAtual = ((int)$saldoAtualVeic - ((int)$saida - (int)$lancamentoHoje->velocimetroSaida));
                                $dataGasolina = ['velocimetroSaida' => $saida];
                                $dataVeiculo  = ['saldoAtualVeic' => $saldoAtual];
                                $this->veiculos_model->edit('veiculos', $dataVeiculo, 'idVeiculos', $veiculos_id);
                                $this->gasolina_model->edit('gasolina', $dataGasolina, 'idGasolina', $lancamentoHoje->idGasolina);
                                $this->session->set_flashdata('success', 'Saída editada com sucesso!');
                                $this->gravarLog($veiculos_id, 'Odômetro Saída Editada', "Odômetro Saída alterado para {$saida}km");
                            }
                        }
                    } else {
                        $this->session->set_flashdata('error', 'Você deve registrar uma Entrada primeiro.');
                    }
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
                    $dataVeiculo  = ['idVeiculos' => $idVeiculos, 'abastecimentoKm' => $abastecimentoKm, 'ultimoAbastecimentoData' => date('Y-m-d H:i:s'), 'saldoAtualVeic' => $saldoAtual, 'abastecer' => 0];
                    $dataGasolina = ['saldoAtual' => $saldoAtual];
                    
                    if ($ultimoRegistro) {
                        $this->gasolina_model->edit('gasolina', $dataGasolina, 'idGasolina', $ultimoRegistro->idGasolina);
                    }
                    if ($this->veiculos_model->edit('veiculos', $dataVeiculo, 'idVeiculos', $idVeiculos)) {
                        $this->session->set_flashdata('success', 'Abastecimento Realizado com sucesso!');
                        $this->gravarLog($idVeiculos, 'Abastecimento', "Abasteceu {$abastecimentoLitro} litros. Nova autonomia {$saldoAtual}km.");
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
                        'ultimaTrocaDeOleoData' => date('Y-m-d H:i:s'),
                        'ultimaTrocaOleoVeloc'  => $kmVelocAtual,
                        'oleoKmVeloc'           => $proxTroca,
                        'trocarOleo'            => 0
                    ];
                    if ($this->veiculos_model->edit('veiculos', $dataVeiculo, 'idVeiculos', $idVeiculos)) {
                        $this->session->set_flashdata('success', 'Óleo Alterado com sucesso!');
                        $this->gravarLog($idVeiculos, 'Óleo', "Troca de óleo na quilometragem {$kmVelocAtual}. Próxima: {$proxTroca}.");
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
            
            // Busca logs para a TAB 2
            $this->db->select('veiculos_logs.*, usuarios.nome as nomeUsuario');
            $this->db->from('veiculos_logs');
            $this->db->join('usuarios', 'usuarios.idUsuarios = veiculos_logs.usuario_id', 'left');
            $this->db->where('veiculo_id', $idVeiculo);
            $this->db->where_in('tipo', ['Abastecimento', 'Óleo']);
            $this->db->order_by('data_hora', 'desc');
            $this->db->limit(10);
            $this->data['veiculo_logs'] = $this->db->get()->result();
        }

        $this->data['tab']  = 3;
        $this->data['view'] = 'veiculos/veiculos';
        return $this->layout();
    }
}