<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/table-custom.css" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>

<style>
    select {
        width: 70px;
    }
</style>

<div class="row-fluid" style="margin-top: 0">

    <?php
    $producaoTec = 0;
    $totalServicosTecnico = 0;
    $totalComissaoServicos = 0;
    $totalServicos = 0;
    $totalVendedor = 0;
    $comissaoProdutos = 0;
    /*    if ($this->input->post()) {
        echo "<pre> comissaoVendas :<br>";
        print_r($results);
        echo "</pre>";
        exit;
    } */

    if (!empty($results)) {
        foreach ($results as $r) {

            if (!empty($r['totalProdutos'])) {
                $totalVendedor += (float)$r['totalProdutos'];
                $comissaoProdutos += (float)$r['totalProdutos']  * 0.03;
            }
        }
    }
    ?>
    <div class="new122">
        <div class="widget-box">

            <div class="widget-title">
                <h5>Comissões de Vendas</h5>
            </div>
            <div class="span12" style="margin-left: 0">
                <form action="<?php echo base_url(); ?>index.php/relatorios/comissaoVendas/" method="post">
                    <div class="span2" style="margin-left: 0">
                        <label>Vendedor</label>
                        <select name="vendedor" id="" class="span12">
                            <option value="">Todos Vendedores</option>
                            <?php

                            if ($this->session->userdata('permissao') == '1' || $this->session->userdata('permissao') == '2') {

                                foreach ($usuarios as $usuario) {
                                    if ($this->input->post('vendedor') == $usuario->nome) {
                                        echo '<option selected>' . $usuario->nome . '</option>';
                                    } else {
                                        echo '<option>' . $usuario->nome . '</option>';
                                    }
                                }
                            } else {
                                echo '<option selected>' . $this->session->userdata('nome_admin') . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="span2">
                        <label>.</label>
                        <select class="span12" name="tipo" id="tipo" value="">
                            <option value="">Todos os tipos</option>
                            <option value="Avulso" <?= $this->input->post('tipo') == "Avulso" ? "selected" : ""; ?>>Avulso</option>
                            <option value="Contrato" <?= $this->input->post('tipo') == "Contrato" ? "selected" : ""; ?>>Contrato</option>
                        </select>

                        <select class="span12" name="local" id="local" value="">
                            <option value="">Todos Locais</option>
                            <option value="Externo" <?= $this->input->post('local') == "Externo" ? "selected" : ""; ?>>Externo</option>
                            <option value="Interno" <?= $this->input->post('local') == "Interno" ? "selected" : ""; ?>>Interno</option>
                        </select>

                    </div>

                    <div class="span2">
                        <label>Finalizado (de)</label>
                        <!-- <input id="finalizado_de" type="text" class="span12 datepicker" name="finalizado_de" value=""> -->
                        <input id="finalizado_de" autocomplete="off" class="span12 " type="datetime-local" name="finalizado_de" value="<?= $this->input->post('finalizado_de') ? $this->input->post('finalizado_de') : date('Y-m-d\TH:i', strtotime('-1 month')) ?>">
                        <input type="checkbox" id="manPrevnt" name="manPrevnt" value="1">
                        <label>Manut Peventiva</label>
                    </div>

                    <div class="span2">
                        <label>Finalizado (até)</label>
                        <!-- <input id="finalizado_ate" type="text" class="span12 datepicker" name="finalizado_ate" value=""> -->
                        <input id="finalizado_ate" autocomplete="off" class="span12 " type="datetime-local" name="finalizado_ate" value="<?= $this->input->post('finalizado_ate') ? $this->input->post('finalizado_ate') : date('Y-m-d\TH:i') ?>">
                    </div>

                    <div class="span2">
                        <label>Comissões</label>

                        <div style="text-align: left"><strong>Produção Vendedor:</strong>
                            <strong style="text-align: right;">R$ <?php if (!empty($totalVendedor)) {
                                                                        echo number_format($totalVendedor, 2, ',', '.');
                                                                    } ?></strong>
                        </div>

                        <div style="text-align: left; color: green"><strong>Comissão:</strong>
                            <strong style="text-align: right; color: green"><?php if (!empty($comissaoProdutos)) {
                                                                                echo number_format($comissaoProdutos, 2, ',', '.');
                                                                            } ?></strong>
                        </div>
                        <div style="text-align: left"><strong>Total de pedidos:</strong>
                            <strong style="text-align: right"> <?php if (!empty($totalLancamentos)) {
                                                                    echo $totalLancamentos;
                                                                } ?></strong>
                        </div>
                    </div>

                    <div class="span2 pull-right">

                        <button type="submit" class="button btn btn-primary btn-sm" style="min-width: 120px;margin: 0">
                            <span class="button__icon"><i class='bx bx-filter-alt'></i></span><span class="button__text2">Filtrar</span>
                        </button>
                        <button type="submit" formaction="<?php echo base_url(); ?>index.php/relatorios/comissaoVendasImprimir" method="post" class=" button btn btn-info btn-sm" style="min-width: 120px;margin: 0">

                            <span class="button__icon"><i class="bx bx-printer"></i></span><span class="button__text2">Imprimir</span>

                        </button>

                        <button type="reset" class="button btn btn-warning btn-sm" style="min-width: 120px;margin: 0">
                            <a href=" <?php echo base_url(); ?>index.php/relatorios/comissaoVendas">
                                <span class="button__icon"><i class="bx bx-brush-alt"></i></span><span class="button__text2">Limpar</span>
                            </a>
                        </button>
                    </div>
                </form>
            </div>
            <div class="widget-content nopadding">
                <div class="table-responsive ">
                    <table class="table table-bordered  ">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th width="20%">Cliente</th>
                                <th class="ph1">Tecnico</th>
                                <th>Data Inicial</th>
                                <th class="ph3">Data Final</th>
                                <th>Valor Produtos</th>
                                <th>Valor Serviços</th>
                                <th class="ph4">Qtd Tecnicos</th>
                                <th class="ph4">Vendedor <?php if (!empty($this->input->post('vendedor'))) {
                                                                echo $this->input->post('vendedor');
                                                            }; ?></th>

                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            if (!empty($results)) {
                                $this->load->model('os_model');
                                foreach ($results as $r) {
                                    $percentual = "";

                                    $dataInicial = date(('d/m/Y'), strtotime($r['dataInicial']));
                                    if ($r['dataFinal'] != null) {
                                        $dataFinal = date(('d/m/Y'), strtotime($r['dataFinal']));
                                    } else {
                                        $dataFinal = "";
                                    }

                                    if ($this->input->get('pesquisa') === null && is_array(json_decode($configuration['os_status_list']))) {
                                        if (in_array($r['status'], json_decode($configuration['os_status_list'])) != true) {
                                            continue;
                                        }
                                    }

                                    switch ($r['status']) {
                                        case 'A Sair | Aguard Conclusão':
                                            $cor = '#00cd00';
                                            break;
                                        case 'Em Andamento':
                                            $cor = '#436eee';
                                            break;
                                        case 'Negociação':
                                            $cor = '#ffd700 ';
                                            break;
                                        case 'Orçamento':
                                            $cor = '#CDB380';
                                            break;
                                        case 'Manutenção Preventiva':
                                            $cor = '#AEB404';
                                            break;
                                        case 'Cancelado':
                                            $cor = '#CD0000';
                                            break;
                                        case 'Finalizado':
                                            $cor = '#256';
                                            break;
                                        case 'Faturado':
                                            $cor = '#B266FF';
                                            break;
                                        case 'Aguardando Peças':
                                            $cor = '#FF7F00';
                                            break;
                                        case 'Aprovado':
                                            $cor = '#808080';
                                            break;
                                        default:
                                            $cor = '#E0E4CC';
                                            break;
                                    }
                                    echo '<tr>';
                                    echo '<td>' . $r['idOs'] . '</td>';
                                    echo '<td class="cli1"><a href="' . base_url() . 'index.php/clientes/visualizar/' . $r['idClientes'] . '" style="margin-right: 1%">' . $r['nomeCliente'] . '</a></td>';
                                    if (!empty($inputGet->tecnico)) {
                                        echo '<td class="ph1">' . $r['tecnicoName'] . '</td>';
                                    } else {
                                        echo '<td></td>';
                                    }
                                    echo '<td>' . $dataInicial . '</td>';
                                    echo '<td class="ph3">' . $dataFinal . '</td>';
                                    echo '<td>R$ ' . number_format($r['totalProdutos'], 2, ',', '.') . '</td>';
                                    echo '<td>R$ ' . number_format($r['totalServicos'], 2, ',', '.') . '</td>';
                                    echo '<td class="ph4">' . $r['divizorComissaoServico'] . '</td>';
                                    echo '<td class="ph4">R$ ' . (float)$r['totalProdutos']  * 0.03 . '</td>';

                                    echo '<td><span class="badge" style="background-color: ' . $cor . '; border-color: ' . $cor . '">' . $r['status'] . '</span> </td>';
                                    echo '<td>';

                                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
                                        echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/os/visualizar/' . $r['idOs'] . '" class="btn-nwe" title="Ver mais detalhes"><i class="bx bx-show"></i></a>';
                                    }

                                    echo '</td>';
                                    echo '</tr>';

                                    /* $totalServicos += (float)$r['totalServicos'];

                                    if ($inputGet['tecnico'] == $r['tecnicoName']) {

                                        $comissaoProdutos += ((float)$r['totalServicos'] / (float)$r['divizorComissaoServico']);
                                        $totalComissaoServicos += (((float)$r['totalServicos'] / (float)$r['divizorComissaoServico']) * (float)$percentual);
                                    } */
                                }
                            } ?>
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colspan="6" style="text-align: right; color: blue"><strong>Produção do Vendedor:</strong></td>
                                <td colspan="6" style="text-align: left; color: blue">
                                    <strong>R$ <?php if (!empty($totalVendedor)) {
                                                    echo number_format($totalVendedor, 2, ',', '.');
                                                } ?></strong>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6" style="text-align: right; color: green"><strong>Total de Comissões:</strong></td>
                                <td colspan="6" style="text-align: left; color: green">
                                    <strong>R$ <?php if (!empty($comissaoProdutos)) {
                                                    echo number_format($comissaoProdutos, 2, ',', '.');
                                                } ?></strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </div>


    <script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $(".money").maskMoney();

        });
    </script>