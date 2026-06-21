<?php

use phpDocumentor\Reflection\Types\Float_;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Financeiro extends MY_Controller
{
    /**
     * author: Ramon Silva
     * email: silva018-mg@yahoo.com.br
     *
     */

    public function __construct()
    {
        parent::__construct();
        $this->load->model('financeiro_model');
        $this->load->model('contas_model');
        $this->load->model('lancamentos_contas_model');

        $this->load->helper('codegen_helper');
        $this->data['menuLancamentos'] = 'financeiro';

        //Gasolina
        $this->load->model('gasolina_model');
        $this->load->model('veiculos_model');
    }

    public function index()
    {
        $this->lancamentos();
    }

    public function lancamentos()
    {

        //echo '<pre>';
        //print_r($this->input->get());
        //echo '<pre>';
        //print_r($classificacao_financeira);
        //exit;

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar lançamentos.');
            redirect(base_url());
        }

        $where = '';
        $vencimento_de = $this->input->get('vencimento_de') ?: date('d/m/Y');
        $vencimento_ate = $this->input->get('vencimento_ate') ?: date('d/m/Y');

        $cliente = $this->input->get('cliente');
        $tipo = $this->input->get('tipo');
        $status = $this->input->get('status');
        $periodo = $this->input->get('periodo') ?: 'mes';
        $centro_de_gastos_bsca = $this->input->get('centro_de_gastos_bsca');
        $classificacao_fin_bsca = $this->input->get('classificacao_fin_bsca');
        $grupo_finaceiro_bsca = $this->input->get('grupo_finaceiro_bsca');
        $forma_pgto_bsca = $this->input->get('forma_pgto_bsca');


        if (!empty($vencimento_de)) {
            $date = DateTime::createFromFormat('d/m/Y', $vencimento_de);

            if (empty($where)) {
                $dateString = $date->format('Y-m-d');
                $where = "data_vencimento >= '$dateString'";
            } else {
                $where .= " AND data_vencimento >= '$date'";
            }
        }

        if (!empty($vencimento_ate)) {
            $date = DateTime::createFromFormat('d/m/Y', $vencimento_ate)->format('Y-m-d');

            if (empty($where)) {
                $where = "data_vencimento <= '$date'";
            } else {
                $where .= " AND data_vencimento <= '$date'";
            }
        }

        if (isset($status) && $status != '') {
            if (empty($where)) {
                $where = "baixado = '$status'";
            } else {
                $where .= " AND baixado = '$status'";
            }
        }

        if (!empty($cliente)) {
            if (empty($where)) {
                $where = "cliente_fornecedor LIKE '%{$cliente}%'";
            } else {
                $where .= " AND cliente_fornecedor LIKE '%{$cliente}%'";
            }
        }

        if (!empty($tipo)) {
            if (empty($where)) {
                $where = "tipo = '$tipo'";
            } else {
                $where .= " AND tipo = '$tipo'";
            }
        }

        if (!empty($centro_de_gastos_bsca)) {
            if (empty($where)) {
                $where = "centro_de_gastos = '$centro_de_gastos_bsca'";
            } else {
                $where .= " AND centro_de_gastos = '$centro_de_gastos_bsca'";
            }
        }

        if (!empty($classificacao_fin_bsca)) {
            if (empty($where)) {
                $where = "classificacao_fin = '$classificacao_fin_bsca'";
            } else {
                $where .= " AND classificacao_fin = '$classificacao_fin_bsca'";
            }
        }

        if (!empty($grupo_finaceiro_bsca)) {
            if (empty($where)) {
                $where = "grupo_finaceiro = '$grupo_finaceiro_bsca'";
            } else {
                $where .= " AND grupo_finaceiro = '$grupo_finaceiro_bsca'";
            }
        }

        if (!empty($forma_pgto_bsca)) {
            if (empty($where)) {
                $where = "forma_pgto = '$forma_pgto_bsca'";
            } else {
                $where .= " AND forma_pgto = '$forma_pgto_bsca'";
            }
        }

        /* echo '<pre>';
        print_r($where);
        exit; */

        $this->load->library('pagination');
        $this->load->model('usuarios_model');
        $this->load->model('classificacao_financeira_model');

        $this->data['configuration']['base_url'] = site_url("financeiro/lancamentos/?vencimento_de=$vencimento_de&vencimento_ate=$vencimento_ate&cliente=$cliente&tipo=$tipo&status=$status&periodo=$periodo");
        $this->data['configuration']['total_rows'] = $this->financeiro_model->count('lancamentos', $where);
        $this->data['configuration']['page_query_string'] = true;

        $this->pagination->initialize($this->data['configuration']);

        if (!empty($this->input->get())) {
            $this->data['lancamento'] = 1;
        } else {
            $this->data['lancamento'] = 0;
        }
        $this->data['contas'] = $this->contas_model->getAll('contas');

        $this->data['results'] = $this->financeiro_model->get('lancamentos', '*', $where, $this->data['configuration']['per_page'], $this->input->get('per_page'));
        $this->data['totals'] = $this->financeiro_model->getTotals($where);

        $this->data['estatisticas_financeiro'] = $this->financeiro_model->getEstatisticasFinanceiro2();
        $this->data['usuarios'] = $this->usuarios_model->getAll();


        $this->data['classificacao_financeira'] = $this->classificacao_financeira_model->getAll();

        //Gasolina
        $this->data['configuration']['total_rows'] = $this->veiculos_model->count('veiculos');
        $this->pagination->initialize($this->data['configuration']);
        $this->data['veiculos'] = $this->veiculos_model->get('veiculos', '*', '', $this->data['configuration']['per_page'], $this->uri->segment(3)); //tab1

        $this->data['veiculo'] = $this->veiculos_model->getById('1'); //tab2 
        $this->data['gasolina'] = $this->gasolina_model->getAll('gasolina', $this->data['configuration']['per_page']); //tab3
        //

        $this->data['view'] = 'financeiro/apagarReceber';
        return $this->layout();
    }

    public function calendario()
    {
        /* if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar O.S.');
            redirect(base_url());
        } */



        $where = '';
        /*  $vencimento_de = $this->input->get('vencimento_de') ?: date('d/m/Y');
        $vencimento_ate = $this->input->get('vencimento_ate') ?: date('d/m/Y'); */


        $cliente = $this->input->get('cliente');
        $tipo = $this->input->get('statusOsGet');
        $status = $this->input->get('status');
        $periodo = $this->input->get('periodo');
        $vencimento_de = $this->input->get('vencimento_de_cal');
        $vencimento_ate = $this->input->get('vencimento_ate_cal');

        $centro_de_gastos_bsca = $this->input->get('centro_de_gastos_bsca');
        $classificacao_fin_bsca = $this->input->get('classificacao_fin_bsca');
        $grupo_finaceiro_bsca = $this->input->get('grupo_finaceiro_bsca');
        $forma_pgto_bsca = $this->input->get('forma_pgto_bsca');


        /*    return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode(
                [
                    'status' => $status,
                    'vencimento_de_cal' => $vencimento_de,
                    'vencimento_ate_cal' => $vencimento_ate
                ]
            ));
        exit; */

        if (empty($periodo)) {
            $periodo = 'mes';
        }

        if (!empty($vencimento_de)) {
            $date = DateTime::createFromFormat('d/m/Y', $vencimento_de);

            if (empty($where)) {
                $dateString = $date->format('Y-m-d');
                $where = "data_vencimento >= '$dateString'";
            } else {
                $where .= " AND data_vencimento >= '$date'";
            }
        }


        if (!empty($vencimento_ate)) {
            $date = DateTime::createFromFormat('d/m/Y', $vencimento_ate)->format('Y-m-d');

            if (empty($where)) {
                $where = "data_vencimento <= '$date'";
            } else {
                $where .= " AND data_vencimento <= '$date'";
            }
        }

        if (isset($status) && $status != '') {
            if (empty($where)) {
                $where = "baixado = '$status'";
            } else {
                $where .= " AND baixado = '$status'";
            }
        }

        if (!empty($cliente)) {
            if (empty($where)) {
                $where = "cliente_fornecedor LIKE '%{$cliente}%'";
            } else {
                $where .= " AND cliente_fornecedor LIKE '%{$cliente}%'";
            }
        }

        if (!empty($tipo)) {
            if (empty($where)) {
                $where = "tipo = '$tipo'";
            } else {
                $where .= " AND tipo = '$tipo'";
            }
        }

        if (!empty($centro_de_gastos_bsca)) {
            if (empty($where)) {
                $where = "centro_de_gastos = '$centro_de_gastos_bsca'";
            } else {
                $where .= " AND centro_de_gastos = '$centro_de_gastos_bsca'";
            }
        }

        if (!empty($classificacao_fin_bsca)) {
            if (empty($where)) {
                $where = "classificacao_fin = '$classificacao_fin_bsca'";
            } else {
                $where .= " AND classificacao_fin = '$classificacao_fin_bsca'";
            }
        }

        if (!empty($grupo_finaceiro_bsca)) {
            if (empty($where)) {
                $where = "grupo_finaceiro = '$grupo_finaceiro_bsca'";
            } else {
                $where .= " AND grupo_finaceiro = '$grupo_finaceiro_bsca'";
            }
        }

        if (!empty($forma_pgto_bsca)) {
            if (empty($where)) {
                $where = "forma_pgto = '$forma_pgto_bsca'";
            } else {
                $where .= " AND forma_pgto = '$forma_pgto_bsca'";
            }
        }



        $allOs = $this->financeiro_model->calendario('lancamentos', '*', $where);
        $totals = $this->financeiro_model->getTotals($where);

        $events = array_map(function ($dup) {
            switch ($dup->tipo) {
                case 'receita':
                    $cor = '#00ff04';
                    break;
                case 'despesa':
                    $cor = '#ff0000';
                    break;
            }
            if ($dup->baixado) {
                $cor = '#353535';
            }
            return [
                'title' => "dup: {$dup->descricao}, Cliente: {$dup->cliente_fornecedor}",
                'start' => $dup->data_vencimento,
                'end' => $dup->data_vencimento,
                'color' => $cor,
                'extendedProps' => [
                    'id' => $dup->idLancamentos,
                    'cliente' => '<b>Cliente/Fornecedor:</b> ' . $dup->cliente_fornecedor,
                    'tipo' => '<b>Tipo:</b> ' . $dup->tipo,
                    'valor' => '<b>Valor Total:</b> R$ ' . number_format($dup->valor, 2, ',', '.'),
                    'forma_pgto' => '<b>Forma de pagamento:</b> ' . $dup->forma_pgto,
                    'observacoes' => '<b>observacoes:</b> ' . strip_tags(html_entity_decode($dup->observacoes)),
                    'data_vencimento' => '<b>Vencimento:</b> ' . date('d/m/Y ', strtotime($dup->data_vencimento)),
                    'data_pagamento' => '<b>data_pagamento:</b> ' . date('d/m/Y ', strtotime($dup->data_pagamento)),

                    //'defeito' => '<b>Defeito:</b> ' . strip_tags(html_entity_decode($dup->defeito)),
                    //'observacoes' => '<b>Observações:</b> ' . strip_tags(html_entity_decode($dup->observacoes)),
                    //'total' => '<b>Valor Total:</b> R$ ' . number_format($dup->totalProdutdup + $dup->totalServicos, 2, ',', '.'),
                    //'desconto' => '<b>Desconto: </b>R$ ' . number_format($this->desconto(floatval($os->valorTotal), floatval($os->desconto), strval($os->tipo_desconto)), 2, ',', '.'),
                    //'valorFaturado' => '<b>Valor Faturado:</b> ' . ($os->faturado ? 'R$ ' . number_format($os->valorTotal - $this->desconto(floatval($os->valorTotal), floatval($os->desconto), strval($os->tipo_desconto)), 2, ',', '.') : "PENDENTE"),
                    //'editar' => $this->os_model->isEditable($os->idOs),
                ]
            ];
        }, $allOs);

        array_push($events, $totals);


        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($events));
    }

    public function adicionarReceita()
    {

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aLancamento')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar lançamentos.');
            redirect(base_url());
        }


        $this->load->library('form_validation');
        $this->data['custom_error'] = '';
        $urlAtual = $this->input->post('urlAtual');
        if ($this->form_validation->run('receita') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            /* echo '<pre>';
            print_r($this->input->post());
            exit; */
            $centro_de_gastos = $this->input->post('centGast');
            $classificacao_fin = $this->input->post('clasFin');

            $this->load->model('classificacao_financeira_model');
            $data = $this->data['classificacao_financeira'] = $this->classificacao_financeira_model->getByClassFin($classificacao_fin);

            $grupo_finaceiro = $data->grupoFinaceiro;

            if (empty($this->input->post('usuario'))) {
                $clien_forn_user = $this->input->post('cliente');
            } else {
                $clien_forn_user = $this->input->post('usuario');
            }

            $vencimento = $this->input->post('vencimento');
            $recebimento = $this->input->post('recebimento');

            if ($recebimento != null) {
                $recebimento = explode('/', $recebimento);
                $recebimento = $recebimento[2] . '-' . $recebimento[1] . '-' . $recebimento[0];
            }

            if ($vencimento == null) {
                $vencimento = date('d/m/Y');
            }

            try {
                $vencimento = explode('/', $vencimento);
                $vencimento = $vencimento[2] . '-' . $vencimento[1] . '-' . $vencimento[0];
            } catch (Exception $e) {
                $vencimento = date('Y/m/d');
            }
            $valor = $this->input->post('valor');
            //Se o valor_desconto for vázio, seta a variavel com valor 0, se não for vazio recebe o valor de desconto
            $valor_desconto = floatval($this->input->post('valor_desconto'));
            $desconto = $valor_desconto;
            //cria variavel para pegar o valor total ja sem o desconto e soma com o desconto
            $total_sem_desconto = $valor + $valor_desconto;
            //$valor =  $total_sem_desconto;
            //cria variavel para pegar o valor total ja com o desconto e diminui com o desconto
            $total_com_desconto = $valor - $valor_desconto;
            $valor_desconto = $total_com_desconto;

            if (!validate_money($valor_desconto)) {
                $valor_desconto = str_replace([',', '.'], ['', ''], $valor_desconto);
            }

            if (!validate_money($valor)) {
                $valor = str_replace([',', '.'], ['', ''], $valor);
            }

            $conta = $this->contas_model->getById($this->input->post('conta'));

            $idContas = $conta->idContas;
            $saldo = (float)$conta->saldo;

            $ndoc = !empty($this->input->post('documento')) ? $this->input->post('documento')  : null;

            if ($this->input->post('tipo') == "receita") {
                $osId = $this->input->post('os_id');
                if (isset($osId)) {
                    $this->load->model('os_model');
                    $editavel = $this->os_model->isEditable($osId);
                    if (!$editavel) {
                        $this->session->set_flashdata('error', 'Essa Os não pode ser Editada.');
                        redirect($urlAtual);
                    } else {
                        $os = $this->os_model->getById($osId);
                        $desconto = $os->desconto;
                        $valor_descontoOs = $os->valor_desconto;
                        $vendas_id = $os->idOs;

                        if (!empty($valor_descontoOs) && $valor_descontoOs > 0) {

                            $valor = (float) $valor_descontoOs;
                        } else {

                            $valor = (float) $valor;
                        }
                        //lANÇAMENTO NO CAIXA DO VALOR EM ESPECIE  
                        if ($this->input->post('formaPgto') == "Pix" || $this->input->post('formaPgto') == "Dinheiro" || $this->input->post('formaPgto') == "Depósito" || $this->input->post('formaPgto') == "Transferência DOC" || $this->input->post('formaPgto') == "Transferência TED") {
                            $total = $saldo + (float)$valor;
                            $data = [
                                'contas_id'          => $idContas,
                                'lancamento'         => $valor,
                                'saldo'              => $total,
                                'tipo_lacamento'     => 'VEND/SER',
                                'dataLancamento'     => date('Y/m/d H:i:s')
                            ];

                            $dataContas = [
                                'saldo'              => $total,
                            ];
                        }

                        $data2 = [
                            'descricao' => set_value('descricao'),
                            'valor' => $valor,
                            'valor_desconto' => $valor_desconto,
                            'desconto' => $desconto,
                            'tipo_desconto' => 'real',
                            'data_vencimento' => $vencimento,
                            'data_pagamento' => $recebimento != null ? $recebimento : date('Y-m-d'),
                            'baixado' => $this->input->post('recebido') ?: 0,
                            'cliente_fornecedor' => $clien_forn_user,
                            'forma_pgto' => $this->input->post('formaPgto'),
                            'tipo' => set_value('tipo'),
                            'observacoes' => set_value('observacoes'),
                            'usuarios_id' => $this->session->userdata('id_admin'),
                            'centro_de_gastos' =>   $centro_de_gastos,
                            'classificacao_fin' =>   $classificacao_fin,
                            'grupo_finaceiro' =>   $grupo_finaceiro,
                            //'vendas_id' => set_value('tipo') == "receita" ? $vendas_id : null,
                            'vendas_id' => $ndoc,
                        ];

                        //lANÇAMENTO
                        if ($this->financeiro_model->add('lancamentos', $data2) == true) {

                            $this->db->set('afaturar', 0);
                            $this->db->set('faturado', 1);
                            //$this->db->set('valorTotal', $valor_desconto);                            
                            $this->db->set('status', 'Faturado');
                            $this->db->where('idOs', $os->idOs);
                            $this->db->update('os');

                            $this->session->set_flashdata('success', 'Lançamento adicionado com sucesso!');
                            log_info('Adicionou um lançamento em Financeiro');
                            redirect($urlAtual);
                        } else {
                            $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
                        }
                    }
                } else {

                    //lANÇAMENTO NO CAIXA DO VALOR EM ESPECIE  
                    if ($this->input->post('formaPgto') == "Pix" || $this->input->post('formaPgto') == "Dinheiro" || $this->input->post('formaPgto') == "Depósito" || $this->input->post('formaPgto') == "Transferência DOC" || $this->input->post('formaPgto') == "Transferência TED") {

                        $total = $saldo + (float)$valor;
                        $data = [
                            'contas_id'          => $idContas,
                            'lancamento'         => $valor,
                            'saldo'              => $total,
                            'tipo_lacamento'     => 'DEPOSITO',
                            'dataLancamento'     => date('Y/m/d H:i:s')
                        ];

                        $dataContas = [
                            'saldo'              => $total,
                        ];
                        $this->contas_model->edit('contas', $dataContas, 'idContas', $idContas);
                        $this->contas_model->add('lancamentos_contas', $data, true);
                    }

                    $data2 = [
                        'descricao' => set_value('descricao'),
                        'valor' => $valor,
                        'valor_desconto' => $valor_desconto,
                        'desconto' => $desconto,
                        'tipo_desconto' => 'real',
                        'data_vencimento' => $vencimento,
                        'data_pagamento' => $recebimento != null ? $recebimento : date('Y-m-d'),
                        'baixado' => $this->input->post('recebido') ?: 0,
                        'cliente_fornecedor' => $clien_forn_user,
                        'forma_pgto' => $this->input->post('formaPgto'),
                        'tipo' => set_value('tipo'),
                        'observacoes' => set_value('observacoes'),
                        'usuarios_id' => $this->session->userdata('id_admin'),
                        'centro_de_gastos' =>   $centro_de_gastos,
                        'classificacao_fin' =>   $classificacao_fin,
                        'grupo_finaceiro' =>   $grupo_finaceiro,
                        //'vendas_id' => set_value('tipo') == "receita" ? $ndoc : null,
                        'vendas_id' => $ndoc,
                    ];
                    //lANÇAMENTO
                    if ($this->financeiro_model->add('lancamentos', $data2) == true) {
                        $this->session->set_flashdata('success', 'Adicionou uma Receita em Financeiro!');
                        log_info('Adicionou uma Receita em Financeiro');
                        redirect($urlAtual);
                    } else {
                        $this->session->set_flashdata('error', 'não foi possivel Adicionoar uma Receita em Financeiro!');
                        $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
                    }
                }
            }
            if ($this->input->post('tipo') == "despesa") {

                if ((float)$valor > $saldo) {
                    $this->session->set_flashdata('error', 'Não tem saldo suficiete para o Pagamento.');
                } else {

                    $total = $saldo - (float)$valor;
                    $data = [
                        'contas_id'          => $idContas,
                        'lancamento'         => $valor,
                        'saldo'              => $total,
                        'tipo_lacamento'     => 'PAGAMENTO',
                        'dataLancamento'     => date('Y/m/d H:i:s')
                    ];

                    $dataContas = [
                        'saldo'              => $total,
                    ];
                    $this->contas_model->edit('contas', $dataContas, 'idContas', $idContas);
                    $this->contas_model->add('lancamentos_contas', $data, true);

                    $data2 = [
                        'descricao' => set_value('descricao'),
                        'valor' => $valor,
                        'valor_desconto' => $valor_desconto,
                        'desconto' => $desconto,
                        'tipo_desconto' => 'real',
                        'data_vencimento' => $vencimento,
                        'data_pagamento' => $recebimento != null ? $recebimento : date('Y-m-d'),
                        'baixado' => $this->input->post('recebido') ?: 0,
                        'cliente_fornecedor' => $clien_forn_user,
                        'forma_pgto' => $this->input->post('formaPgto'),
                        'tipo' => set_value('tipo'),
                        'observacoes' => set_value('observacoes'),
                        'usuarios_id' => $this->session->userdata('id_admin'),
                        'centro_de_gastos' =>   $centro_de_gastos,
                        'classificacao_fin' =>   $classificacao_fin,
                        'grupo_finaceiro' =>   $grupo_finaceiro,
                        'vendas_id' => $ndoc,
                    ];

                    if ($this->financeiro_model->add('lancamentos', $data2) == true) {
                        $this->session->set_flashdata('success', 'Lançamento adicionado com sucesso!');
                        log_info('Adicionou um lançamento em Financeiro');
                        redirect($urlAtual);
                    } else {
                        $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
                    }
                }
            }
        }

        $this->session->set_flashdata('error', 'Ocorreu um erro ao tentar adicionar o lançamento.');
        redirect($urlAtual);
    }

    public function adicionarReceita_parc()
    {
        /* echo '<pre>';
        print_r($this->input->post());
        exit; */

        $urlAtual = $this->input->post('urlAtual');
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aLancamento')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar lançamentos.');
            redirect(base_url());
        } else {

            $centro_de_gastos = $this->input->post('centGast');
            $classificacao_fin = $this->input->post('clasFin');

            if (!empty($this->input->post('os_id'))) {
                $vendas_id = $this->input->post('os_id');
            } else {
                $vendas_id = $this->input->post('documento_parc');
            }


            $this->load->model('classificacao_financeira_model');
            $data = $this->data['classificacao_financeira'] = $this->classificacao_financeira_model->getByClassFin($classificacao_fin);

            $grupo_finaceiro = $data->grupoFinaceiro;

            if (empty($this->input->post('usuario_parc'))) {
                $clien_forn_user = $this->input->post('cliente_parc');
                $idClietFor = $this->input->post('idCliente_parc');
            } else {
                $clien_forn_user = $this->input->post('usuario_parc');
                $idClietFor = 1;
            }

            if (!empty($this->input->post('entrada'))) {
                $entrada = (float)$this->input->post('entrada');
            } else {
                $entrada = 0;
            }


            $valor_desconto = $this->input->post('desconto_parc') ?: 0;
            $valor_desconto = str_replace(',', '.', $valor_desconto);
            $qtdparcelas_parc = $this->input->post('qtdparcelas_parc') ?: 1; //4x
            $valor_parc = $this->input->post('valor_parc'); //450


            $valorparcelas = ((float)$valor_parc - (float)$entrada) / (float)$qtdparcelas_parc;

            $desconto_por_parcela  =  $valor_desconto > 0 ? ($valor_desconto / $qtdparcelas_parc) : 0;

            //para por na descrição, valor total sem desconto e sem parcelamento
            $descricao_parc_valor = $valor_parc + $valor_desconto;

            //cria variavel para pegar o valor total ja com o desconto e diminui com o desconto
            $total_com_desconto = $valorparcelas + $desconto_por_parcela;



            if ($entrada >= $valor_parc) {
                $this->session->set_flashdata('error', 'O valor da entrada não pode ser maior ou igual ao valor total da receita/Despesa!');
                redirect($urlAtual);
            }

            $dia_pgto = $this->input->post('dia_pgto');
            $dia_base_pgto = $this->input->post('dia_base_pgto');
            $recebimento = $this->input->post('recebimento');

            try {
                $dia_pgto = explode('/', $dia_pgto);
                $dia_pgto = $dia_pgto[2] . '-' . $dia_pgto[1] . '-' . $dia_pgto[0];

                $dia_base_pgto = explode('/', $dia_base_pgto);
                $dia_base_pgto = $dia_base_pgto[2] . '-' . $dia_base_pgto[1] . '-' . $dia_base_pgto[0];
            } catch (Exception $e) {
                $dia_pgto = date('Y/m/d');
                $dia_base_pgto = date('Y/m/d');
            }

            $comissao = $this->input->post('comissao');

            if (!validate_money($comissao)) {
                $comissao = str_replace([',', '.'], ['', ''], $comissao);
            }
            $multiplica_parc = $this->input->post('multiplica_parc');

            if ((int)$multiplica_parc == 1) {
                $valorParcela = $this->input->post('valor_parc');
                $total_com_desconto = $valorParcela;
                $valorparcelas = 0;
                //echo $total_com_desconto;
            }

            /* echo "<pre>";
            print_r('entrada' .   $entrada);
            exit; */

            if ($entrada == 0) {
                $loops = 1;
                while ($loops <= $qtdparcelas_parc) {
                    $myDateTimeISO = $dia_base_pgto;
                    $loopsmes = $loops - 1;
                    $addThese = $loopsmes;
                    $myDateTime = new DateTime($myDateTimeISO);
                    $myDayOfMonth = date_format($myDateTime, 'j');
                    date_modify($myDateTime, "+$addThese months");

                    //Descobre se o dia do mês caiu
                    $myNewDayOfMonth = date_format($myDateTime, 'j');
                    if ($myDayOfMonth > 28 && $myNewDayOfMonth < 4) {
                        //Em caso afirmativo, corrija voltando o número de dias que transbordaram
                        date_modify($myDateTime, "-$myNewDayOfMonth days");
                    }

                    $data = [
                        'descricao' => $this->input->post('descricao_parc') . ' - Parcelamento de R$' . $descricao_parc_valor . '  [' . $loops . '/' . $qtdparcelas_parc . ']',
                        'valor' => $total_com_desconto,
                        'desconto' => $desconto_por_parcela,
                        'tipo_desconto' => 'real',
                        'valor_desconto' =>   $valorparcelas,
                        'data_vencimento' => date_format($myDateTime, "Y-m-d"),
                        'data_pagamento' => $recebimento ?: date_format($myDateTime, "Y-m-d"),
                        'baixado' => 0,
                        'cliente_fornecedor' => $clien_forn_user,
                        'clientes_id ' => $idClietFor,
                        'observacoes' => $this->input->post('observacoes_parc'),
                        'forma_pgto' => $this->input->post('formaPgto_parc'),
                        'tipo' => $this->input->post('tipo_parc'),
                        'usuarios_id' => $this->session->userdata('id_admin'),
                        'centro_de_gastos' =>   $centro_de_gastos,
                        'classificacao_fin' =>   $classificacao_fin,
                        'grupo_finaceiro' =>   $grupo_finaceiro,
                        //'vendas_id' => $this->input->post('tipo_parc') == "receita" ? $vendas_id : null,
                        'vendas_id' => $vendas_id,
                    ];
                    if ($this->financeiro_model->add('lancamentos', $data) == true) {
                        $this->session->set_flashdata('success', 'Lançamento adicionado com sucesso!');
                        log_info('Adicionou um lançamento em Financeiro');
                    } else {
                        $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
                    }
                    $loops++;
                }
                $osId = $this->input->post('os_id');
                if (isset($osId)) {
                    $this->load->model('os_model');
                    $os = $this->os_model->getById($osId);
                    $this->db->set('afaturar', 0);
                    $this->db->set('faturado', 1);
                    //$this->db->set('valorTotal', $valor_desconto);
                    $this->db->set('status', 'Faturado');
                    $this->db->where('idOs', $os->idOs);
                    $this->db->update('os');
                }
                redirect($urlAtual);
            } else {

                $desconto_entrada = "0";
                $data1 = [
                    'descricao' => $this->input->post('descricao_parc')  . ' - Entrada do parc. de R$' . $descricao_parc_valor . ' ',
                    'valor' => $entrada,
                    'desconto' =>  $desconto_entrada,
                    'valor_desconto' => $entrada,
                    'tipo_desconto' => 'real',
                    'data_vencimento' => $dia_pgto,
                    'data_pagamento' => $dia_pgto != null ? $dia_pgto : date_format("Y-m-d"),
                    'baixado' => 0,
                    'cliente_fornecedor' => $clien_forn_user,
                    'clientes_id' => $this->input->post('idCliente_parc'),
                    'observacoes' => $this->input->post('observacoes_parc'),
                    'forma_pgto' => $this->input->post('formaPgto_parc'),
                    'tipo' => $this->input->post('tipo_parc'),
                    'usuarios_id' => $this->session->userdata('id_admin'),
                    'centro_de_gastos' =>   $centro_de_gastos,
                    'classificacao_fin' =>   $classificacao_fin,
                    'grupo_finaceiro' =>   $grupo_finaceiro,
                    //'vendas_id' => $this->input->post('tipo_parc') == "receita" ? $vendas_id : null,
                    'vendas_id' => $vendas_id,
                ];

                $this->financeiro_model->add1('lancamentos', $data1);
                $loops = 1;
                while ($loops <= $qtdparcelas_parc) {
                    $myDateTimeISO = $dia_base_pgto;
                    $loopsmes = $loops - 1;
                    $addThese = $loopsmes;
                    $myDateTime = new DateTime($myDateTimeISO);
                    $myDayOfMonth = date_format($myDateTime, 'j');
                    date_modify($myDateTime, "+$addThese months");

                    //Find out if the day-of-month has dropped
                    $myNewDayOfMonth = date_format($myDateTime, 'j');
                    if ($myDayOfMonth > 28 && $myNewDayOfMonth < 4) {
                        //If so, fix by going back the number of days that have spilled over
                        date_modify($myDateTime, "-$myNewDayOfMonth days");
                    }

                    $data = [
                        'descricao' => $this->input->post('descricao_parc') . ' - Parcelamento de R$' . $descricao_parc_valor . ' [' . $loops . '/' . $qtdparcelas_parc . ']',
                        'valor' => $total_com_desconto,
                        'desconto' => $desconto_por_parcela,
                        'tipo_desconto' => 'real',
                        'valor_desconto' => $valorparcelas,
                        'data_vencimento' => date_format($myDateTime, "Y-m-d"),
                        'data_pagamento' => date_format($myDateTime, "Y-m-d"),
                        'baixado' => 0,
                        'cliente_fornecedor' => $clien_forn_user,
                        'observacoes' => $this->input->post('observacoes_parc'),
                        'forma_pgto' => $this->input->post('formaPgto_parc'),
                        'tipo' => $this->input->post('tipo_parc'),
                        'usuarios_id' => $this->session->userdata('id_admin'),
                        'centro_de_gastos' =>   $centro_de_gastos,
                        'classificacao_fin' =>   $classificacao_fin,
                        'grupo_finaceiro' =>   $grupo_finaceiro,
                        //'vendas_id' => $this->input->post('tipo_parc') == "receita" ? $vendas_id : null,
                        'vendas_id' => $vendas_id,

                    ];

                    // if (empty($data['valor_desconto'])) {
                    //     $data['valor_desconto'] =  "0";
                    // }

                    if ($this->financeiro_model->add('lancamentos', $data) == true) {
                        $this->session->set_flashdata('success', 'Lançamento adicionado com sucesso!');
                        log_info('Adicionou um lançamento em Financeiro');
                    } else {
                        $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
                    }
                    $loops++;
                }
                $osId = $this->input->post('os_id');
                if (isset($osId)) {
                    $this->load->model('os_model');
                    $os = $this->os_model->getById($osId);
                    $this->db->set('afaturar', 0);
                    $this->db->set('faturado', 1);
                    //$this->db->set('valorTotal', $valor_desconto);
                    $this->db->set('status', 'Faturado');
                    $this->db->where('idOs', $os->idOs);
                    $this->db->update('os');
                }

                redirect($urlAtual);
            }
        }

        $this->session->set_flashdata('error', 'Ocorreu um erro ao tentar adicionar o lançamento');
        redirect($urlAtual);
    }

    public function adicionarDespesa()
    {
        echo '<pre>';
        print_r($this->input->post());
        exit;
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aLancamento')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar lançamentos.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';
        $urlAtual = $this->input->post('urlAtual');
        if ($this->form_validation->run('despesa') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $vencimento = $this->input->post('vencimento');
            $pagamento = $this->input->post('pagamento');

            if ($pagamento != null) {
                $pagamento = explode('/', $pagamento);
                $pagamento = $pagamento[2] . '-' . $pagamento[1] . '-' . $pagamento[0];
            }

            if ($vencimento == null) {
                $vencimento = date('d/m/Y');
            }

            try {
                $vencimento = explode('/', $vencimento);
                $vencimento = $vencimento[2] . '-' . $vencimento[1] . '-' . $vencimento[0];
            } catch (Exception $e) {
                $vencimento = date('Y/m/d');
            }

            $valor = $this->input->post('valor');

            if (!validate_money($valor)) {
                $valor = str_replace([',', '.'], ['', ''], $valor);
            }

            $data = [
                'descricao' => set_value('descricao'),
                'valor' => $valor,
                'data_vencimento' => $vencimento,
                'data_pagamento' => $pagamento != null ? $pagamento : date('Y-m-d'),
                'baixado' => $this->input->post('pago') ?: 0,
                'cliente_fornecedor' => set_value('fornecedor'),
                'forma_pgto' => $this->input->post('formaPgto'),
                'tipo' => set_value('tipo'),
                'observacoes' => set_value('observacoes'),
                'usuarios_id' => $this->session->userdata('id_admin'),
            ];

            if (set_value('idFornecedor')) {
                $data['clientes_id'] =  set_value('idFornecedor');
            }
            if (set_value('idCliente')) {
                $data['clientes_id'] =  set_value('idCliente');
            }
            if ($this->financeiro_model->add('lancamentos', $data) == true) {
                $this->session->set_flashdata('success', 'Despesa adicionada com sucesso!');
                log_info('Adicionou uma despesa');
                redirect($urlAtual);
            } else {
                $this->session->set_flashdata('error', 'Ocorreu um erro ao tentar adicionar despesa!');
                redirect($urlAtual);
            }
        }

        $this->session->set_flashdata('error', 'Ocorreu um erro ao tentar adicionar despesa.');
        redirect($urlAtual);
    }

    public function editar()
    {

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eLancamento')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar lançamentos.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';
        $urlAtual = $this->input->post('urlAtual');

        $this->form_validation->set_rules('descricao', '', 'trim|required');
        $this->form_validation->set_rules('fornecedor', '', 'trim|required');
        $this->form_validation->set_rules('valor', '', 'trim|required');
        $this->form_validation->set_rules('vencimento', '', 'trim|required');
        $this->form_validation->set_rules('pagamento', '', 'trim');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {

            $centro_de_gastos = $this->input->post('centGast');
            $classificacao_fin = $this->input->post('clasFin');

            $this->load->model('classificacao_financeira_model');
            $data = $this->data['classificacao_financeira'] = $this->classificacao_financeira_model->getByClassFin($classificacao_fin);

            $grupo_finaceiro = $data->grupoFinaceiro;

            $vencimento = $this->input->post('vencimento');
            $pagamento = $this->input->post('pagamento');

            try {
                $vencimento = explode('/', $vencimento);
                $vencimento = $vencimento[2] . '-' . $vencimento[1] . '-' . $vencimento[0];

                $pagamento = explode('/', $pagamento);
                $pagamento = $pagamento[2] . '-' . $pagamento[1] . '-' . $pagamento[0];
            } catch (Exception $e) {
                $vencimento = date('Y/m/d');
            }

            $valor = floatval($this->input->post('valor'));
            //Se o valor_desconto for vázio, seta a variavel com valor 0, se não for vazio recebe o valor de desconto
            $valor_desconto = floatval($this->input->post('descontos_editar')); // valor do total + desconto

            $valor_total =  $valor + $valor_desconto; //90 + 10=100
            $valor_com_desconto = $valor_total - $valor_desconto;


            if (empty($this->input->post('usuario'))) {
                $cliente_fornecedor = $this->input->post('fornecedor');
            } else {
                $cliente_fornecedor = $this->input->post('usuario');
            }

            $tipo = $this->input->post('tipo');
            $conta = $this->contas_model->getById($this->input->post('conta'));
            $idContas = $conta->idContas;
            $saldo = (float)$conta->saldo;



            //lANÇAMENTO NO CAIXA DO VALOR EM ESPECIE  
            if ($tipo == 'receita') {

                if ($this->input->post('formaPgto') == "Pix" || $this->input->post('formaPgto') == "Dinheiro" || $this->input->post('formaPgto') == "Depósito") {

                    $total = $saldo + (float)$valor;
                    $data1 = [
                        'contas_id'          => $idContas,
                        'lancamento'         => $valor,
                        'saldo'              => $total,
                        'tipo_lacamento'     => 'VEND/SER',
                        'dataLancamento'     => date('Y/m/d H:i:s')
                    ];

                    $dataContas = [
                        'saldo'              => $total,
                    ];
                    $this->contas_model->edit('contas', $dataContas, 'idContas', $idContas);
                    $this->contas_model->add('lancamentos_contas', $data1, true);
                }
            }
            if ($tipo == 'despesa') {
                if ((float)$valor > $saldo) {
                    $this->session->set_flashdata('error', 'Não tem saldo suficiete para o Pagamento.');
                    redirect($urlAtual);
                } else {
                    if ($this->input->post('formaPgto') == "Pix" || $this->input->post('formaPgto') == "Dinheiro" || $this->input->post('formaPgto') == "Depósito") {

                        $total = $saldo - (float)$valor;
                        $data1 = [
                            'contas_id'          => $idContas,
                            'lancamento'         => $valor,
                            'saldo'              => $total,
                            'tipo_lacamento'     => 'PAGAMENTO',
                            'dataLancamento'     => date('Y/m/d H:i:s')
                        ];

                        $dataContas = [
                            'saldo'              => $total,
                        ];
                        $this->contas_model->edit('contas', $dataContas, 'idContas', $idContas);
                        $this->contas_model->add('lancamentos_contas', $data1, true);
                    }
                }
            }

            $data = [
                'descricao' => $this->input->post('descricao'),
                'data_vencimento' => $vencimento,
                'data_pagamento' => $pagamento,
                'valor' => $valor_total,
                'desconto' => $valor_desconto,
                'tipo_desconto' => 'real',
                'valor_desconto' =>  $valor_com_desconto,
                'baixado' => $this->input->post('pago') ?: 0,
                'cliente_fornecedor' => $cliente_fornecedor,
                'forma_pgto' => $this->input->post('formaPgto'),
                'tipo' => $this->input->post('tipo'),
                'observacoes' => $this->input->post('observacoes'),
                'usuarios_id' => $this->session->userdata('id_admin'),
                'centro_de_gastos' =>   $centro_de_gastos,
                'classificacao_fin' =>   $classificacao_fin,
                'grupo_finaceiro' =>   $grupo_finaceiro,
            ];

            if (set_value('idFornecedor')) {
                $data['clientes_id'] =  set_value('idFornecedor');
            }
            if (empty($data['valor_desconto'])) {
                $data['valor_desconto'] =  "0";
            }

            if (set_value('idCliente')) {
                $data['clientes_id'] =  set_value('idCliente');
            }
            if ($this->financeiro_model->edit('lancamentos', $data, 'idLancamentos', $this->input->post('id')) == true) {
                $this->session->set_flashdata('success', 'lançamento editado com sucesso!');
                log_info('Alterou um lançamento no financeiro. ID' . $this->input->post('id'));
                redirect($urlAtual);
            } else {
                $this->session->set_flashdata('error', 'Ocorreu um erro ao tentar editar lançamento!');
                redirect($urlAtual);
            }
        }

        $this->session->set_flashdata('error', 'Ocorreu um erro ao tentar editar lançamento.');
        redirect($urlAtual);

        /*   $data = [
            'descricao' => $this->input->post('descricao'),
            'data_vencimento' => $this->input->post('vencimento'),
            'data_pagamento' => $pagamento,
            'valor' => $this->input->post('valor'),
            'valor_desconto' => $this->input->post('valor_desconto_editar'),
            'tipo_desconto' => 'real',
            'baixado' => $this->input->post('pago'),
            'cliente_fornecedor' => set_value('fornecedor'),
            'forma_pgto' => $this->input->post('formaPgto'),
            'tipo' => $this->input->post('tipo'),
            'usuarios_id' => $this->session->userdata('id_admin'),
        ];
        if (set_value('idFornecedor')) {
            $data['clientes_id'] =  set_value('idFornecedor');
        }
        if (empty($data['valor_desconto'])) {
            $data['valor_desconto'] =  "0";
        }
        if (set_value('idCliente')) {
            $data['clientes_id'] =  set_value('idCliente');
        }

        print_r($data); */
    }

    public function excluirLancamento()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dLancamento')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir lançamentos.');
            redirect(base_url());
        }

        $id = $this->input->post('id');

        if ($id == null || !is_numeric($id)) {
            $json = ['result' => false];
            echo json_encode($json);
        } else {
            // Começa a transação
            $this->db->trans_start();

            // Atualiza a tabela vendas, removendo o ID do lançamento e alterando o faturado e status
            $this->db->set('lancamentos_id', null);
            $this->db->set('faturado', 0);
            $this->db->set('status', 'Finalizado');
            $this->db->where('lancamentos_id', $id);
            $this->db->update('vendas');

            // Exclui o lançamento
            $result = $this->financeiro_model->delete('lancamentos', 'idLancamentos', $id);

            if ($result) {
                $this->db->trans_complete();

                if ($this->db->trans_status() === false) {
                    $this->db->trans_rollback();
                    $json = ['result' => false];
                    echo json_encode($json);
                } else {
                    log_info('Removeu um lançamento. ID: ' . $id);
                    $json = ['result' => true];
                    echo json_encode($json);
                }
            } else {
                $json = ['result' => false];
                echo json_encode($json);
            }
        }
    }

    public function autoCompleteClienteFornecedor()
    {
        if ($this->input->get('term')) {
            $q = strtolower($this->input->get('term'));
            $this->financeiro_model->autoCompleteClienteFornecedor($q);
        }
    }

    public function autoCompleteClienteAddReceita()
    {
        if ($this->input->get('term')) {
            $q = strtolower($this->input->get('term'));
            $this->financeiro_model->autoCompleteClienteReceita($q);
        }
    }

    protected function getThisYear()
    {
        $dias = date("z");
        $primeiro = date("Y-m-d", strtotime("-" . ($dias) . " day"));
        $ultimo = date("Y-m-d", strtotime("+" . (364 - $dias) . " day"));
        return [$primeiro, $ultimo];
    }

    protected function getThisWeek()
    {
        return [date("Y/m/d", strtotime("last sunday", strtotime("now"))), date("Y/m/d", strtotime("next saturday", strtotime("now")))];
    }

    protected function getLastSevenDays()
    {
        return [date("Y-m-d", strtotime("-7 day", strtotime("now"))), date("Y-m-d", strtotime("now"))];
    }

    protected function getThisMonth()
    {
        $mes = date('m');
        $ano = date('Y');
        $qtdDiasMes = date('t');
        $inicia = $ano . "-" . $mes . "-01";

        $ate = $ano . "-" . $mes . "-" . $qtdDiasMes;
        return [$inicia, $ate];
    }

    public function anexar()
    {
        $this->load->library('upload');
        $this->load->library('image_lib');

        $directory = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'anexos' . DIRECTORY_SEPARATOR . date('m-Y') . DIRECTORY_SEPARATOR . 'OS-' . $this->input->post('idOsServico');

        // If it exist, check if it's a directory
        if (!is_dir($directory . DIRECTORY_SEPARATOR . 'thumbs')) {
            // make directory for images and thumbs
            try {
                mkdir($directory . DIRECTORY_SEPARATOR . 'thumbs', 0755, true);
            } catch (Exception $e) {
                echo json_encode(['result' => false, 'mensagem' => $e->getMessage()]);
                die();
            }
        }

        $upload_conf = [
            'upload_path' => $directory,
            'allowed_types' => 'jpg|png|gif|jpeg|JPG|PNG|GIF|JPEG|pdf|PDF|cdr|CDR|docx|DOCX|txt', // formatos permitidos para anexos de os
            'max_size' => 0,
        ];

        $this->upload->initialize($upload_conf);

        foreach ($_FILES['userfile'] as $key => $val) {
            $i = 1;
            foreach ($val as $v) {
                $field_name = "file_" . $i;
                $_FILES[$field_name][$key] = $v;
                $i++;
            }
        }
        unset($_FILES['userfile']);

        $error = [];
        $success = [];

        foreach ($_FILES as $field_name => $file) {
            if (!$this->upload->do_upload($field_name)) {
                $error['upload'][] = $this->upload->display_errors();
            } else {
                $upload_data = $this->upload->data();

                // Gera um nome de arquivo aleatório mantendo a extensão original
                $new_file_name = uniqid() . '.' . pathinfo($upload_data['file_name'], PATHINFO_EXTENSION);
                $new_file_path = $upload_data['file_path'] . $new_file_name;

                rename($upload_data['full_path'], $new_file_path);

                if ($upload_data['is_image'] == 1) {
                    $resize_conf = [
                        'source_image' => $new_file_path,
                        'new_image' => $upload_data['file_path'] . 'thumbs' . DIRECTORY_SEPARATOR . 'thumb_' . $new_file_name,
                        'width' => 200,
                        'height' => 125,
                    ];

                    $this->image_lib->initialize($resize_conf);

                    if (!$this->image_lib->resize()) {
                        $error['resize'][] = $this->image_lib->display_errors();
                    } else {
                        $success[] = $upload_data;
                        $this->load->model('Os_model');
                        $result = $this->Os_model->anexar($this->input->post('idOsServico'), $new_file_name, base_url('assets' . DIRECTORY_SEPARATOR . 'anexos' . DIRECTORY_SEPARATOR . date('m-Y') . DIRECTORY_SEPARATOR . 'OS-' . $this->input->post('idOsServico')), 'thumb_' . $new_file_name, $directory);
                        if (!$result) {
                            $error['db'][] = 'Erro ao inserir no banco de dados.';
                        }
                    }
                } else {
                    $success[] = $upload_data;

                    $this->load->model('Os_model');

                    $result = $this->Os_model->anexar($this->input->post('idOsServico'), $new_file_name, base_url('assets' . DIRECTORY_SEPARATOR . 'anexos' . DIRECTORY_SEPARATOR . date('m-Y') . DIRECTORY_SEPARATOR . 'OS-' . $this->input->post('idOsServico')), '', $directory);
                    if (!$result) {
                        $error['db'][] = 'Erro ao inserir no banco de dados.';
                    }
                }
            }
        }

        if (count($error) > 0) {
            echo json_encode(['result' => false, 'mensagem' => 'Ocorreu um erro ao processar os arquivos.', 'errors' => $error]);
        } else {
            log_info('Adicionou anexo(s) a uma OS. ID (OS): ' . $this->input->post('idOsServico'));
            echo json_encode(['result' => true, 'mensagem' => 'Arquivo(s) anexado(s) com sucesso.']);
        }
    }

    public function excluirAnexo($id = null)
    {
        if ($id == null || !is_numeric($id)) {
            echo json_encode(['result' => false, 'mensagem' => 'Erro ao tentar excluir anexo.']);
        } else {
            $this->db->where('idAnexos', $id);
            $file = $this->db->get('anexos', 1)->row();
            $idOs = $this->input->post('idOs');

            unlink($file->path . DIRECTORY_SEPARATOR . $file->anexo);

            if ($file->thumb != null) {
                unlink($file->path . DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR . $file->thumb);
            }

            if ($this->os_model->delete('anexos', 'idAnexos', $id) == true) {
                log_info('Removeu anexo de uma OS. ID (OS): ' . $idOs);
                echo json_encode(['result' => true, 'mensagem' => 'Anexo excluído com sucesso.']);
            } else {
                echo json_encode(['result' => false, 'mensagem' => 'Erro ao tentar excluir anexo.']);
            }
        }
    }

    public function downloadanexo($id = null)
    {
        if ($id != null && is_numeric($id)) {
            $this->db->where('idAnexos', $id);
            $file = $this->db->get('anexos', 1)->row();

            $this->load->library('zip');
            $path = $file->path;
            $this->zip->read_file($path . '/' . $file->anexo);
            $this->zip->download('file' . date('d-m-Y-H.i.s') . '.zip');
        }
    }
}
