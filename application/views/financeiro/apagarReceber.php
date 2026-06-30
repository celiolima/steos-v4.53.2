<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>

<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<script language="javascript" type="text/javascript" src="<?= base_url(); ?>assets/js/dist/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>assets/js/dist/plugins/jqplot.pieRenderer.min.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>assets/js/dist/plugins/jqplot.donutRenderer.min.js"></script>
<script src='<?= base_url(); ?>assets/js/fullcalendar.min.js'></script>
<script src='<?= base_url(); ?>assets/js/fullcalendar/locales/pt-br.js'></script>

<link href='<?= base_url(); ?>assets/css/fullcalendar.min.css' rel='stylesheet' />
<link rel="stylesheet" type="text/css" href="<?= base_url(); ?>assets/js/dist/jquery.jqplot.min.css" />

<script src="<?php echo base_url() ?>assets/js/dayjs.min.js"></script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&display=swap" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>

<?php
$situacao = $this->input->get('situacao');
$periodo = $this->input->get('periodo');
?>
<?php
/* echo "<pre>";
print_r($this->input->get());
exit; */
?>

<style type="text/css">
    label.error {
        color: #b94a48;
    }

    input.error {
        border-color: #b94a48;
    }

    input.valid {
        border-color: #5bb75b;
    }

    textarea {
        resize: vertical;
    }
</style>
<?php



?>


<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-diagnoses"></i>
                </span>
                <h5>Financeiro</h5>
                <!-- Botões -->
                <div class="buttons">
                    <!-- <a href="#modal-faturar" id="btn-faturar" role="button" data-toggle="modal" class="button btn btn-mini btn-danger">
                        <span class="button__icon"><i class='bx bx-dollar'></i></span> <span class="button__text">Faturar</span></a> -->
                    <a target="_blank" title="adicionar Contas" class="button btn btn-mini btn-inverse" href="<?php echo site_url() ?>/contas">
                        <span class="button__icon"><i class="bx bxs-dollar-circle"></i></span> <span class="button__text">Contas</span></a>
                    <a target="_blank" title="adicionar Bancos" class="button btn btn-mini btn-inverse" href="<?php echo site_url() ?>/#">
                        <span class="button__icon"><i class="bx bxs-landmark"></i></span> <span class="button__text">Bancos</span></a>
                    <a target="_blank" title="adicionar Class.Financ" class="button btn btn-mini btn-inverse" href="<?php echo site_url() ?>/#">
                        <span class="button__icon"><i class="bx bx-barcode-reader"></i></span> <span class="button__text">Class. Financeira</span></a>
                    <!--   <a href="<?php //echo base_url(); 
                                    ?>index.php/os/adicionar" class="button btn btn-mini btn-success" style="max-width: 160px">
                        <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Ordem de Serviço</span></a> -->
                    <?php if ($lancamento == "0") { ?>
                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aLancamento')) { ?>
                            <a href="#modalReceita" data-toggle="modal" role="button" class="button btn btn-mini btn-success" style="width: 230px">
                                <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2" title="Cadastrar nova receita ou despesa"> Despesa/Receita à Vista</span></a>
                            <a href="#modalReceitaParcelada" data-toggle="modal" role="button" class="button btn btn-mini btn-success" style="width: 230px">
                                <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2" title="Cadastrar nova receita ou despesa parcelada "> Despesa/Receita Parcelada</span></a>

                        <?php } ?>
                    <?php } ?>
                    <?php
                    if (!empty($veiculos)) {
                        foreach ($veiculos as $r) {
                            if ($r->situacao) {
                                if ((int)$r->saldoAtualVeic < 20) { ?>
                                    <a target="_blank" title="verifique aba gasolina" class="button btn btn-mini btn-danger" href="<?php echo base_url(); ?>index.php/veiculos/gasolina/<?php echo $r->idVeiculos; ?>">
                                        <span class="button__icon"><i class="bx bxs-gas-pump "></i></span> <span class=" button__text"><?php echo $r->nomeVeiculo; ?></span></a>
                    <?php }
                            }
                        }
                    } ?>

                    <div id="alerta" class="span12 alert alert-info" style="margin-left: 0"> não conseguimos listar o calendario.
                    </div>
                </div>
            </div>
            <div class="widget-content nopadding tab-content">
                <div class="span12" id="divProdutosServicos" style=" margin-left: 0">
                    <!--  Tabs -->
                    <ul class="nav nav-tabs">
                        <?php if ($lancamento == "0") { ?>
                            <li class="active" id="tabDetalhes"><a href="#tab1" data-toggle="tab">Agenda de Pagamentos e Receitas</a></li>
                        <?php } ?>
                        <li <?php if ($lancamento == "1") {
                                echo 'class="active"';
                            } ?> id="tabLancamentos"><a href="#tab2" data-toggle="tab">Lançamentos</a></li>
                        <li id="tabGasolina"><a href="#tab3" data-toggle="tab">Gasolina</a></li>
                    </ul>
                    <div class="tab-content">
                        <!--Agenda de Pagamentos-->
                        <?php if ($lancamento == "0") { ?>
                            <div class="tab-pane  active" id="tab1">
                                <div class="span12" id="divCadastrarOs">
                                    <div class="row-fluid" style="margin-top: 0; display: flex">
                                        <div class="span12">
                                            <div class="widget-box2">
                                                <div>
                                                    <h5 class="cardHeader">Agenda de Pagamentos e Receitas</h5>
                                                </div>
                                                <div class="widget-content">
                                                    <table>

                                                        <form class="form-horizontal" method="get">
                                                            <div class="widget-content nopadding tab-content">
                                                                <div class="row-fluid">
                                                                    <div class="span2" style="margin-left: 0">
                                                                        <label>Receitas/Despesas</label>
                                                                        <select style="padding-left: 30px;" name="statusOsGet" id="statusOsGet">
                                                                            <option value="">Todos os Status</option>
                                                                            <option value="receita" <?= ($this->input->get('statusOsGet') === 'receita' || $this->input->get('tipo') === 'receita') ? 'selected' : '' ?>>Receita</option>
                                                                            <option value="despesa" <?= ($this->input->get('statusOsGet') === 'despesa' || $this->input->get('tipo') === 'despesa') ? 'selected' : '' ?>>Despesa</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="span2">
                                                                        <label>Vencimento (de)</label>
                                                                        <input class="datepicker" style="padding-left: 30px;" id="vencimento_de_cal" placeholder="vencimento_de" required="required" name="vencimento_de_cal" type="text" value="<?= $this->input->get('vencimento_de_cal') ?: ($this->input->get('vencimento_de') ?: date('01/m/Y')) ?>">
                                                                    </div>
                                                                    <div class="span2">
                                                                        <label>Vencimento (até)</label>
                                                                        <input class="datepicker" style="padding-left: 30px;" id="vencimento_ate_cal" placeholder="vencimento_ate" required="required" name="vencimento_ate_cal" type="text" value="<?= $this->input->get('vencimento_ate_cal') ?: ($this->input->get('vencimento_ate') ?: date('t/m/Y')) ?>">
                                                                    </div>
                                                                    <div class="span2">
                                                                        <label>Status</label>
                                                                        <select name="status" id="status" class="span12">
                                                                            <option value="">Todos (Pendente e Pago)</option>
                                                                            <option value="0" <?= $this->input->get('status') === '0' ? 'selected' : '' ?>>Pendente</option>
                                                                            <option value="1" <?= $this->input->get('status') === '1' ? 'selected' : '' ?>>Pago</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="span4">
                                                                        <label>Cliente/Fornecedor</label>
                                                                        <input id="cliente_busca" type="text" class="span12" name="cliente" value="<?= $this->input->get('cliente') ?>">
                                                                    </div>
                                                                </div>

                                                                <div class="row-fluid" style="margin-top: 10px;">
                                                                    <div class="span2" style="margin-left: 0;">
                                                                        <label>Centro de Gastos</label>
                                                                        <select id="centro_de_gastos_bsca_cal" class="span12" name="centro_de_gastos_bsca">
                                                                            <option value="">Todos</option>
                                                                            <option value="SERVICOS" <?= $this->input->get('centro_de_gastos_bsca') === 'SERVICOS' ? 'selected' : '' ?>>SERVICOS</option>
                                                                            <option value="VENDAS" <?= $this->input->get('centro_de_gastos_bsca') === 'VENDAS' ? 'selected' : '' ?>>VENDAS</option>
                                                                            <option value="OPERACIONAIS" <?= $this->input->get('centro_de_gastos_bsca') === 'OPERACIONAIS' ? 'selected' : '' ?>>OPERACIONAIS</option>
                                                                            <option value="RH" <?= $this->input->get('centro_de_gastos_bsca') === 'RH' ? 'selected' : '' ?>>RH</option>
                                                                            <option value="ADMINISTRATIVO" <?= $this->input->get('centro_de_gastos_bsca') === 'ADMINISTRATIVO' ? 'selected' : '' ?>>ADMINISTRATIVO</option>
                                                                            <option value="MARKETING" <?= $this->input->get('centro_de_gastos_bsca') === 'MARKETING' ? 'selected' : '' ?>>MARKETING</option>
                                                                            <option value="GASTOS FINANCEIROS" <?= $this->input->get('centro_de_gastos_bsca') === 'GASTOS FINANCEIROS' ? 'selected' : '' ?>>GASTOS FINANCEIROS</option>
                                                                            <option value="INVESTISTIMENTOS" <?= $this->input->get('centro_de_gastos_bsca') === 'INVESTISTIMENTOS' ? 'selected' : '' ?>>INVESTIMENTOS</option>
                                                                        </select>
                                                                    </div>

                                                                    <div class="span2">
                                                                        <label>Classificação Financeira</label>
                                                                        <select id="classificacao_fin_bsca_cal" class="span12" name="classificacao_fin_bsca">
                                                                            <option value="">Todos</option>
                                                                            <?php foreach ($classificacao_financeira as $f) {
                                                                                if ($this->input->get("classificacao_fin_bsca") === $f->nomeClassFin) {
                                                                                    echo '<option value="' . $f->nomeClassFin . '" selected >' . $f->nomeClassFin . '</option>';
                                                                                } else {
                                                                                    echo '<option value="' . $f->nomeClassFin . '" >' . $f->nomeClassFin . '</option>';
                                                                                }
                                                                            } ?>
                                                                        </select>
                                                                    </div>
                                                                    <div class="span2">
                                                                        <label>Grupo Financeiro</label>
                                                                        <select id="grupo_finaceiro_bsca_cal" class="span12" name="grupo_finaceiro_bsca">
                                                                            <option value="">Todos</option>
                                                                            <?php $grupoFinavceiro = "";
                                                                            foreach ($classificacao_financeira as $f) {
                                                                                if ($this->input->get("grupo_finaceiro_bsca") === $f->grupoFinaceiro) {
                                                                                    if ($f->grupoFinaceiro !=  $grupoFinavceiro) {
                                                                                        echo '<option value="' . $f->grupoFinaceiro . '" selected>' . $f->grupoFinaceiro . '</option>';
                                                                                    }
                                                                                } else {
                                                                                    if ($f->grupoFinaceiro !=  $grupoFinavceiro) {
                                                                                        echo '<option value="' . $f->grupoFinaceiro . '">' . $f->grupoFinaceiro . '</option>';
                                                                                    }
                                                                                }
                                                                                $grupoFinavceiro = $f->grupoFinaceiro;
                                                                            } ?>
                                                                        </select>
                                                                    </div>
                                                                    <div class="span2">
                                                                        <label>Forma de Pagamento</label>
                                                                        <select id="forma_pgto_bsca_cal" class="span12" name="forma_pgto_bsca">
                                                                            <option value="">Todos</option>
                                                                            <option value="Dinheiro" <?= $this->input->get('forma_pgto_bsca') === 'Dinheiro' ? 'selected' : '' ?>>Dinheiro</option>
                                                                            <option value="Pix" <?= $this->input->get('forma_pgto_bsca') === 'Pix' ? 'selected' : '' ?>>Pix</option>
                                                                            <option value="Boleto" <?= $this->input->get('forma_pgto_bsca') === 'Boleto' ? 'selected' : '' ?>>Boleto</option>
                                                                            <option value="Cartão de Crédito" <?= $this->input->get('forma_pgto_bsca') === 'Cartão de Crédito' ? 'selected' : '' ?>>Cartão de Crédito</option>
                                                                            <option value="Cartão de Débito" <?= $this->input->get('forma_pgto_bsca') === 'Cartão de Débito' ? 'selected' : '' ?>>Cartão de Débito</option>
                                                                            <option value="Cheque" <?= $this->input->get('forma_pgto_bsca') === 'Cheque' ? 'selected' : '' ?>>Cheque</option>
                                                                            <option value="Cheque Pré-datado" <?= $this->input->get('forma_pgto_bsca') === 'Cheque Pré-datado' ? 'selected' : '' ?>>Cheque Pré-datado</option>
                                                                            <option value="Depósito" <?= $this->input->get('forma_pgto_bsca') === 'Depósito' ? 'selected' : '' ?>>Depósito</option>
                                                                            <option value="Transferência DOC" <?= $this->input->get('forma_pgto_bsca') === 'Transferência DOC' ? 'selected' : '' ?>>Transferência DOC</option>
                                                                            <option value="Transferência TED" <?= $this->input->get('forma_pgto_bsca') === 'Transferência TED' ? 'selected' : '' ?>>Transferência TED</option>
                                                                            <option value="Promissória" <?= $this->input->get('forma_pgto_bsca') === 'Promissória' ? 'selected' : '' ?>>Promissória</option>
                                                                        </select>
                                                                    </div>

                                                                    <div class="span2">
                                                                        <label>&nbsp;</label>
                                                                        <button type="button" class="button btn btn-mini btn-warning" id="btn-calendar" style="min-width: 120px;">
                                                                            <span class="button__icon"><i class='bx bx-search-alt'></i></span>
                                                                            <span class="button__text2">Pesquisar</span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>

                                                        <div id='source-calendar'>
                                                        </div>
                                                        <div>
                                                            <strong>Total Receitas:</strong>
                                                            <strong style="text-align: right; color: green">R$</strong>
                                                            <strong style="text-align: right; color: green" id="receitasTot" value="<?php echo number_format("0.00", 2, ',', '.') ?>"> </strong>
                                                        </div>
                                                        <div>
                                                            <strong>Total Despesas:</strong>
                                                            <strong style="text-align: right; color: red">R$</strong>
                                                            <strong style="text-align: right; color: red" id="despesasTot" value="<?php echo number_format("0.00", 2, ',', '.') ?>"> </strong>
                                                        </div>

                                                        <h5>Total =<strong id="tot" value="<?php echo number_format("0.00", 2, ',', '.') ?>"></strong></h5>

                                                    </table>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>
                        <?php } ?>

                        <!--lancamentos-->
                        <div class="tab-pane <?php if ($lancamento == '1') {
                                                    echo 'active';
                                                } ?>" id="tab2">
                            <div class="span12" style="padding: 1%; margin-left: 0">
                                <div class="span12 well" style="padding: 1%; margin-left: 0" id="form-anexos">

                                    <div class="new122">
                                        <div class="widget-title">
                                            <span class="icon">
                                                <i class="fas fa-hand-holding-usd"></i>
                                            </span>
                                            <h5>Lançamentos Financeiros</h5>
                                        </div>

                                        <div class="span12" style="margin-left: 0;margin-top: 1rem;">
                                            <form action="<?php echo current_url(); ?>" method="get">
                                                <div class="row-fluid">
                                                    <div class="span2" style="margin-left: 0">
                                                        <label>Período</label>
                                                        <select id="periodo" name="periodo" class="span12">
                                                            <option value="mes" <?= $this->input->get('periodo') === 'mes' ? 'selected' : '' ?>>Mês</option>
                                                            <option value="dia" <?= $this->input->get('periodo') === 'dia' ? 'selected' : '' ?>>Dia</option>
                                                            <option value="semana" <?= $this->input->get('periodo') === 'semana' ? 'selected' : '' ?>>Semana
                                                            </option>
                                                            <option value="ano" <?= $this->input->get('periodo') === 'ano' ? 'selected' : '' ?>>Ano</option>
                                                        </select>
                                                    </div>

                                                    <div class="span2">
                                                        <label>Vencimento (de)</label>
                                                        <input id="vencimento_de" type="text" class="span12 datepicker" name="vencimento_de" value="<?= $this->input->get('vencimento_de') ? $this->input->get('vencimento_de') : date('01/m/Y') ?>">
                                                    </div>

                                                    <div class="span2">
                                                        <label>Vencimento (até)</label>
                                                        <input id="vencimento_ate" type="text" class="span12 datepicker" name="vencimento_ate" value="<?= $this->input->get('vencimento_ate') ? $this->input->get('vencimento_ate') : date('t/m/Y') ?>">
                                                    </div>

                                                    <div class="span1">
                                                        <label>Tipo</label>
                                                        <select name="tipo" class="span12">
                                                            <option value="">Todos</option>
                                                            <option value="receita" <?= $this->input->get('tipo') === 'receita' ? 'selected' : '' ?>>Receita
                                                            </option>
                                                            <option value="despesa" <?= $this->input->get('tipo') === 'despesa' ? 'selected' : '' ?>>Despesa
                                                            </option>
                                                        </select>
                                                    </div>

                                                    <div class="span1">
                                                        <label>Status</label>
                                                        <select name="status" class="span12">
                                                            <option value="">Todos</option>
                                                            <option value="0" <?= $this->input->get('status') === '0' ? 'selected' : '' ?>>Pendente</option>
                                                            <option value="1" <?= $this->input->get('status') === '1' ? 'selected' : '' ?>>Pago</option>
                                                        </select>
                                                    </div>

                                                    <div class="span4">
                                                        <label>Cliente/Fornecedor</label>
                                                        <input id="cliente_lanc" type="text" class="span12" name="cliente" value="<?= $this->input->get('cliente') ?>">
                                                    </div>
                                                </div>

                                                <div class="row-fluid" style="margin-top: 10px;">
                                                    <div class="span2" style="margin-left: 0;">
                                                        <label>Centro de Gastos</label>
                                                        <select id="centro_de_gastos_bsca" class="span12" name="centro_de_gastos_bsca">
                                                            <option value="">Todos</option>
                                                            <option value="SERVICOS" <?= $this->input->get('centro_de_gastos_bsca') === 'SERVICOS' ? 'selected' : '' ?>>SERVICOS</option>
                                                            <option value="VENDAS" <?= $this->input->get('centro_de_gastos_bsca') === 'VENDAS' ? 'selected' : '' ?>>VENDAS</option>
                                                            <option value="OPERACIONAIS" <?= $this->input->get('centro_de_gastos_bsca') === 'OPERACIONAIS' ? 'selected' : '' ?>>OPERACIONAIS</option>
                                                            <option value="RH" <?= $this->input->get('centro_de_gastos_bsca') === 'RH' ? 'selected' : '' ?>>RH</option>
                                                            <option value="ADMINISTRATIVO" <?= $this->input->get('centro_de_gastos_bsca') === 'ADMINISTRATIVO' ? 'selected' : '' ?>>ADMINISTRATIVO</option>
                                                            <option value="MARKETING" <?= $this->input->get('centro_de_gastos_bsca') === 'MARKETING' ? 'selected' : '' ?>>MARKETING</option>
                                                            <option value="GASTOS FINANCEIROS" <?= $this->input->get('centro_de_gastos_bsca') === 'GASTOS FINANCEIROS' ? 'selected' : '' ?>>GASTOS FINANCEIROS</option>
                                                            <option value="INVESTISTIMENTOS" <?= $this->input->get('centro_de_gastos_bsca') === 'INVESTISTIMENTOS' ? 'selected' : '' ?>>INVESTIMENTOS</option>
                                                        </select>
                                                    </div>

                                                    <div class="span2">
                                                        <label>Classificação Financeira</label>
                                                        <select id="classificacao_fin_bsca" class="span12" name="classificacao_fin_bsca">
                                                            <option value="">Todos</option>
                                                            <?php foreach ($classificacao_financeira as $f) {
                                                                if ($this->input->get("classificacao_fin_bsca") === $f->nomeClassFin) {
                                                                    echo '<option value="' . $f->nomeClassFin . '" selected >' . $f->nomeClassFin . '</option>';
                                                                } else {
                                                                    echo '<option value="' . $f->nomeClassFin . '" >' . $f->nomeClassFin . '</option>';
                                                                }
                                                            } ?>
                                                        </select>
                                                    </div>
                                                    <div class="span2">
                                                        <label>Grupo Financeiro</label>
                                                        <select id="grupo_finaceiro_bsca" class="span12" name="grupo_finaceiro_bsca">
                                                            <option value="">Todos</option>
                                                            <?php $grupoFinavceiro = "";
                                                            foreach ($classificacao_financeira as $f) {
                                                                if ($this->input->get("grupo_finaceiro_bsca") === $f->grupoFinaceiro) {
                                                                    if ($f->grupoFinaceiro !=  $grupoFinavceiro) {
                                                                        echo '<option value="' . $f->grupoFinaceiro . '" selected>' . $f->grupoFinaceiro . '</option>';
                                                                    }
                                                                } else {
                                                                    if ($f->grupoFinaceiro !=  $grupoFinavceiro) {
                                                                        echo '<option value="' . $f->grupoFinaceiro . '">' . $f->grupoFinaceiro . '</option>';
                                                                    }
                                                                }
                                                                $grupoFinavceiro = $f->grupoFinaceiro;
                                                            } ?>
                                                        </select>
                                                    </div>
                                                    <div class="span2">
                                                        <label>Forma de Pagamento</label>
                                                        <select id="forma_pgto_bsca" class="span12" name="forma_pgto_bsca">
                                                            <option value="">Todos</option>
                                                            <option value="Dinheiro" <?= $this->input->get('forma_pgto_bsca') === 'Dinheiro' ? 'selected' : '' ?>>Dinheiro</option>
                                                            <option value="Pix" <?= $this->input->get('forma_pgto_bsca') === 'Pix' ? 'selected' : '' ?>>Pix</option>
                                                            <option value="Boleto" <?= $this->input->get('forma_pgto_bsca') === 'Boleto' ? 'selected' : '' ?>>Boleto</option>
                                                            <option value="Cartão de Crédito" <?= $this->input->get('forma_pgto_bsca') === 'Cartão de Crédito' ? 'selected' : '' ?>>Cartão de Crédito</option>
                                                            <option value="Cartão de Débito" <?= $this->input->get('forma_pgto_bsca') === 'Cartão de Débito' ? 'selected' : '' ?>>Cartão de Débito</option>
                                                            <option value="Cheque" <?= $this->input->get('forma_pgto_bsca') === 'Cheque' ? 'selected' : '' ?>>Cheque</option>
                                                            <option value="Cheque Pré-datado" <?= $this->input->get('forma_pgto_bsca') === 'Cheque Pré-datado' ? 'selected' : '' ?>>Cheque Pré-datado</option>
                                                            <option value="Depósito" <?= $this->input->get('forma_pgto_bsca') === 'Depósito' ? 'selected' : '' ?>>Depósito</option>
                                                            <option value="Transferência DOC" <?= $this->input->get('forma_pgto_bsca') === 'Transferência DOC' ? 'selected' : '' ?>>Transferência DOC</option>
                                                            <option value="Transferência TED" <?= $this->input->get('forma_pgto_bsca') === 'Transferência TED' ? 'selected' : '' ?>>Transferência TED</option>
                                                            <option value="Promissória" <?= $this->input->get('forma_pgto_bsca') === 'Promissória' ? 'selected' : '' ?>>Promissória</option>
                                                        </select>
                                                    </div>
                                                    <div class="span2">
                                                        <label>&nbsp;</label>
                                                        <button type="submit" class="button btn btn-primary btn-sm" style="min-width: 120px">
                                                            <span class="button__icon"><i class='bx bx-filter-alt'></i></span><span class="button__text2">Filtrar</span></button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <div>
                                            <div class="widget-box">
                                                <div class="widget-content nopadding tab-content">
                                                    <table class="table table-bordered " id="divLancamentos">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Tipo</th>
                                                                <th>Cliente / Fornecedor</th>
                                                                <th>Descrição</th>
                                                                <th>Vencimento</th>
                                                                <th>Status</th>
                                                                <th>Observações</th>
                                                                <th>Forma de Pagamento</th>
                                                                <th>Valor (+)</th>
                                                                <th>Desconto (-)</th>
                                                                <th>Valor Total (=)</th>
                                                                <th>Ações</th>
                                                            </tr>
                                                        </thead>
                                                        <?php
                                                        /*  echo "<pre>";
                                                        print_r($results);
                                                        exit; */
                                                        ?>
                                                        <tbody>
                                                            <?php


                                                            if (!$results) {
                                                                echo '<tr>
                                                                <td colspan="9" >Nenhum lançamento encontrado</td>
                                                                </tr>';
                                                            }
                                                            foreach ($results as $r) {
                                                                $vencimento = date(('d/m/Y'), strtotime($r->data_vencimento));

                                                                if ($r->baixado == 0) {
                                                                    $status = 'Pendente';
                                                                    $style  = '';
                                                                } else {
                                                                    $status = 'Pago';
                                                                    $style  = 'style = "background-color: #353535;"';
                                                                };
                                                                if ($r->tipo == 'receita') {
                                                                    $label = 'success';
                                                                }
                                                                if ($r->tipo == 'despesa') {
                                                                    $label = 'important';
                                                                }
                                                                echo '<tr>';
                                                                echo '<td>' . $r->idLancamentos . '</td>';
                                                                echo '<td><span class="label label-' . $label . '"' . $style . '>' . ucfirst($r->tipo) . '</span></td>';
                                                                echo '<td>' . $r->cliente_fornecedor . '</td>';
                                                                echo '<td>' . $r->descricao . '</td>';
                                                                echo '<td>' . $vencimento . '</td>';
                                                                echo '<td>' . $status . '</td>';
                                                                echo '<td><div style="max-height: 80px; overflow-y: auto; max-width: 350px;">' . $r->observacoes . '</div></td>';
                                                                echo '<td>' . $r->forma_pgto . '</td>';
                                                                echo '<td> R$ ' . number_format($r->valor, 2, ',', '.') . '</td>'; //valor total sem o desconto
                                                                echo  $r->tipo_desconto == "real" ? '<td>' . "R$ " . $r->desconto . '</td>' : ($r->tipo_desconto == "porcento" ? '<td>' . $r->desconto . " %" . '</td>' : '<td>' . "0" . '</td>'); // valor do desconto
                                                                echo $r->valor_desconto != 0 ? '<td> R$ ' . number_format($r->valor_desconto, 2, ',', '.') . '</td>' : '<td> R$ ' . number_format($r->valor, 2, ',', '.') . '</td>'; // valor total  com o desconto

                                                                echo '<td>';
                                                                if ($r->data_pagamento == "0000-00-00") {
                                                                    $data_pagamento = "";
                                                                } else {
                                                                    $data_pagamento = date('d/m/Y', strtotime($r->data_pagamento));
                                                                }


                                                                if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eLancamento')) {
                                                                    if ($r->valor_desconto == 0) {
                                                                        echo '<a href="#modalEditar" style="margin-right: 1%" data-toggle="modal" role="button" idLancamento="' . $r->idLancamentos . '" descricao="' . $r->descricao . '" valor="' . $r->valor . '" vencimento="' . date('d/m/Y', strtotime($r->data_vencimento)) . '" pagamento="' . $data_pagamento . '" baixado="' . $r->baixado . '" cliente="' . $r->cliente_fornecedor . '" formaPgto="' . $r->forma_pgto . '" tipo="' . $r->tipo . '" observacoes="' . $r->observacoes . '" descontos_editar="' . $r->desconto . '" valor_desconto_editar="' . $r->valor . '" valorEditar_sem_desconto="' . $r->valor . '" usuario="' . $r->nome . '" centro_de_gastos="' . $r->centro_de_gastos . '" classificacao_fin="' . $r->classificacao_fin . '" grupo_finaceiro="' . $r->grupo_finaceiro . '" class="btn-nwe3 editar" title="Editar OS"><i class="bx bx-edit"></i></a>';
                                                                    } else {
                                                                        echo '<a href="#modalEditar" style="margin-right: 1%" data-toggle="modal" role="button" idLancamento="' . $r->idLancamentos . '" descricao="' . $r->descricao . '" valor="' . $r->valor_desconto . '" vencimento="' . date('d/m/Y', strtotime($r->data_vencimento)) . '" pagamento="' . $data_pagamento . '" baixado="' . $r->baixado . '" cliente="' . $r->cliente_fornecedor . '" formaPgto="' . $r->forma_pgto . '" tipo="' . $r->tipo . '" observacoes="' . $r->observacoes . '" descontos_editar="' . $r->desconto . '" valor_desconto_editar="' . $r->desconto . '" valorEditar_sem_desconto="' . $r->valor  . '" usuario="' . $r->nome . '" centro_de_gastos="' . $r->centro_de_gastos . '" classificacao_fin="' . $r->classificacao_fin . '" grupo_finaceiro="' . $r->grupo_finaceiro . '" class="btn-nwe3 editar" title="Editar OS"><i class="bx bx-edit"></i></a>';
                                                                    }
                                                                }
                                                                if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dLancamento')) {
                                                                    echo '<a href="#modalExcluir" data-toggle="modal" role="button" idLancamento="' . $r->idLancamentos . '" class="btn-nwe4 excluir" title="Excluir OS"><i class="bx bx-trash-alt"></i></a>';
                                                                }

                                                                echo '</td>';
                                                                echo '</tr>';
                                                            } ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td colspan="6" style="text-align: right; color: green"><strong>Total Receitas:</strong></td>
                                                                <td colspan="6" style="text-align: left; color: green">
                                                                    <strong>R$ <?php echo number_format($totals['receitas'], 2, ',', '.') ?></strong>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="6" style="text-align: right; color: red"><strong>Total Despesas:</strong></td>
                                                                <td colspan="6" style="text-align: left; color: red">
                                                                    <strong>R$ <?php echo number_format($totals['despesas'], 2, ',', '.') ?></strong>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="6" style="text-align: right"><strong>Saldo:</strong></td>
                                                                <td colspan="6" style="text-align: left;">
                                                                    <strong>R$ <?php echo number_format($totals['receitas'] - $totals['despesas'], 2, ',', '.') ?></strong>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td colspan="7" style="text-align: left;"><strong>Estatísticas Gerais do Financeiro:</strong></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="7" style="text-align: left; color: green">Total Receitas (Pagas): R$ <?php echo number_format($estatisticas_financeiro->total_receita, 2, ',', '.'); ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="7" style="text-align: left; color: red">Total Despesas (Pagas): R$ <?php echo number_format($estatisticas_financeiro->total_despesa, 2, ',', '.'); ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="7" style="text-align: left;"><strong>Total Receitas (-) Despesas = Saldo Líquido: R$ <?php $sub_receita_despesa = $estatisticas_financeiro->total_receita - $estatisticas_financeiro->total_despesa;
                                                                                                                                                                    echo number_format($sub_receita_despesa, 2, ',', '.') ?></strong></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="7" style="text-align: left;">Total Receitas (+) Despesas: R$ <?php $soma_receita_despesa = $estatisticas_financeiro->total_receita + $estatisticas_financeiro->total_despesa;
                                                                                                                                            echo number_format($soma_receita_despesa, 2, ',', '.') ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="7" style="text-align: left;">Total Receitas Pendentes: R$ <?php echo number_format($estatisticas_financeiro->total_receita_pendente, 2, ',', '.'); ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="7" style="text-align: left;">Total Despesas Pendentes: R$ <?php echo number_format($estatisticas_financeiro->total_despesa_pendente, 2, ',', '.'); ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="7" style="text-align: left;">Total de Receitas Pendentes (-) Despesas Pendentes: R$ <?php $sub_recpendente_despependente = $estatisticas_financeiro->total_receita_pendente - $estatisticas_financeiro->total_despesa_pendente;
                                                                                                                                                                    echo number_format($sub_recpendente_despependente, 2, ',', '.') ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="7" style="text-align: left;"><strong>Total de Receitas Pendentes (+) Despesas Pendentes: R$ <?php $sub_recpendente_despependente = $estatisticas_financeiro->total_receita_pendente + $estatisticas_financeiro->total_despesa_pendente;
                                                                                                                                                                            echo number_format($sub_recpendente_despependente, 2, ',', '.') ?></strong></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="7" style="text-align: left;">Total de Descontos aplicados á lançamentos Pagos: R$ <?php echo number_format($estatisticas_financeiro->total_valor_desconto, 2, ',', '.'); ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="7" style="text-align: left;">Total de Descontos aplicados á lançamentos Pendentes: R$ <?php echo number_format($estatisticas_financeiro->total_valor_desconto_pendente, 2, ',', '.'); ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="7" style="text-align: left;"><strong>Total de descontos aplicados (pagos + pendentes): R$ <?php $soma_descontos_pagos = $estatisticas_financeiro->total_valor_desconto + $estatisticas_financeiro->total_valor_desconto_pendente;
                                                                                                                                                                        echo number_format($soma_descontos_pagos, 2, ',', '.') ?></strong></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="7" style="text-align: left;">Total de Receitas sem descontos aplicados (pagos + pendentes): R$ <?php echo number_format($estatisticas_financeiro->total_receita_sem_desconto, 2, ',', '.'); ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="7" style="text-align: left;">Total de Despesas sem descontos aplicados (pagos + pendentes): R$ <?php echo number_format($estatisticas_financeiro->total_despesa_sem_desconto, 2, ',', '.'); ?></td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <?php echo $this->pagination->create_links(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Gasolina-->
                        <div class="tab-pane " id="tab3">
                            <div class="span12" style="padding: 1%; margin-left: 0">
                                <div class="span12 well" style="padding: 1%; margin-left: 0" id="form-anexos">
                                    <!-- gasolina inicio  -->
                                    <div class="widget-content nopadding tab-content">
                                        <div class="span12" id="divProdutosServicos" style=" margin-left: 0">
                                            <!--veiculos     -->
                                            <div class="widget-box">

                                                <div class="widget-content nopadding tab-content">
                                                    <table id="tabela1" class="table table-bordered ">
                                                        <thead>
                                                            <tr>
                                                                <th>Veiculo</th>
                                                                <th>Marca/Modelo</th>
                                                                <th>Autonomia</th>
                                                                <th>Saldo Km</th>
                                                                <th>Óleo Próx Troca</th>
                                                                <th>Ações</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            /* echo "<pre>";
                                                            print_r($veiculos);
                                                            exit; */
                                                            if (!empty($veiculos)) {
                                                                foreach ($veiculos as $r) {
                                                                    if ($r->situacao) {
                                                                        echo '<tr>';
                                                                        echo '<input type="hidden">' . $r->idVeiculos . '</input>';
                                                                        echo '<td>' . $r->nomeVeiculo . '</td>';
                                                                        echo '<td>' . $r->observacoes . '</td>';
                                                                        echo '<td>' . $r->autonomia . '</td>';
                                                                        echo '<td>' . $r->saldoAtualVeic . '</td>';
                                                                        echo '<td>' . $r->oleoKmVeloc . '</td>';

                                                                        echo '<td>';

                                                                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eProduto')) {
                                                                            echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/veiculos/veiculo/' . $r->idVeiculos . '" class="btn-nwe3" title="Veiculo"><i class="bx bxs-car" style="color:#2945bb" ></i></a>';
                                                                        }
                                                                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eProduto')) {
                                                                            echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/veiculos/gasolina/' . $r->idVeiculos . '" class="btn-nwe3" title="lançar gasolina"><i class="bx bxs-gas-pump" style="color:#4a9e58"  ></i></a>';
                                                                        }
                                                                        if ((int)$r->saldoAtualVeic < 20) {
                                                                            echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/veiculos/gasolina/' . $r->idVeiculos . '" class="btn-nwe3" title="gasolina acabando "><i class="bx bxs-gas-pump" style="color:#ff0000"  ></i></a>';
                                                                        }
                                                                        echo '</td>';
                                                                        echo '</tr>';
                                                                    }
                                                                }
                                                            } ?>
                                                        </tbody>

                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- gasolina inicio  -->
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                &nbsp
            </div>
        </div>
    </div>
</div>

<!-- Modal nova receita e despesa AVISTA -->
<div id="modalReceita" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form id="formReceita" action="<?php echo base_url() ?>index.php/financeiro/adicionarReceita" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">Adicionar Despesa/Receita Avista</h3>
        </div>
        <div class="modal-body">
            <div class="span12 alert alert-info" style="margin-left: 0"> Obrigatório o preenchimento dos campos com
                asterisco.
            </div>
            <div class="span3" style="margin-left: 0">
                <label for="tipo">Tipo</label>
                <select name="tipo" id="tipo" class="span10">
                    <option value="despesa">Despesa</option>
                    <option value="receita">Receita</option>
                </select>
            </div>
            <div class="span9" style="margin-left: 0">
                <label for="descricao">Descrição/Referência*</label>
                <input class="span12" id="descricao" type="text" name="descricao" required />
                <input id="urlAtual" type="hidden" name="urlAtual" value="<?php echo current_url() ?>" />
            </div>

            <div class="span5" style="margin-left: 0">
                <label for="centGast" style="margin-left: 0">Centro de gastos</label>
                <select name="centGast" id="centGast" class="span10">
                    <option value="SERVICOS">SERVICOS</option>
                    <option value="VENDAS">VENDAS</option>
                    <option value="OPERACIONAIS">OPERACIONAIS</option>
                    <option value="MARKETING">MARKETING</option>
                    <option value="RH">RH</option>
                    <option value="ADMINISTRATIVO">ADMINISTRATIVO</option>
                    <option value="GASTOS FINANCEIROS">GASTOS FINANCEIROS</option>
                    <option value="INVESTISTIMENTOS">INVESTISTIMENTOS</option>
                </select>
            </div>

            <div class="span5" style="margin-left: 0">
                <label for="clasFin" style="margin-left: 0">Clas. finaceira</label>
                <select name="clasFin" id="clasFin" class="span12">
                    <option value="">Selecione</option>
                    <?php foreach ($classificacao_financeira as $f) {
                        echo '<option value="' . $f->nomeClassFin . '">' . $f->nomeClassFin . '</option>';
                    } ?>
                </select>
            </div>
            <div class="span2" style="margin-left: 1">
                <label for="documento">Nº Doc.</label>
                <input class="span12" id="documento" type="text" name="documento" placeholder="Os/Venda" />

            </div>
            <!--  -->
            <div class="span5" style="margin-left: 0">
                <label id="label_cliente"><input type="radio" value="option1" name="campo-radio" id="cliente_label" checked />&nbspCliente/Fornecedor*</label>
                <label id="label_Usuario"><input type="radio" value="option2" name="campo-radio" id="usuario_label" />&nbspUsuario*</label>
                <!-- 
                <label id="label_cliente_r_editar"><input type="radio" value="option1" name="campo-radio" id="cliente_label" checked />&nbspCliente/Fornecedor*</label>
                <label><input type="radio" value="option2" name="campo-radio" id="usuario_label" />&nbspUsuario*</label>
             -->
            </div>
            <div class="span7" style="margin-left: 0">
                <input class="span12" id="clienteAvista" type="text" name="cliente" />
                <input class="span12" id="idClienteAvista" type="hidden" name="idCliente" value="" />

                <select name="usuario" id="usuario" class="span12">
                    <option value="">Selecione</option>
                    <?php foreach ($usuarios as $u) {
                        echo '<option value="' . $u->nome . '">' . $u->nome . '</option>';
                    }
                    ?>
                </select>
            </div>
            <!--  -->

            <div class="span12" style="margin-left: 0">
                <label for="observacoes">Observações</label>
                <textarea class="span12" id="observacoes" name="observacoes"></textarea>
            </div>

            <div class="span12" style="margin-left: 0">
                <div class="span4" style="margin-left: 0">
                    <label for="valor">Valor*</label>
                    <input class="span12 money" id="valor" type="text" name="valor" data-affixes-stay="true" data-thousands="" data-decimal="." required />
                </div>

                <div class="span4">
                    <label for="descontos">Desconto</label>
                    <input class="span6 money" id="descontos" type="text" name="descontos" value="" placeholder="em R$" style="float: left;" />
                    <input class="btn btn-inverse" onclick="mostrarValores();" type="button" name="valor_desconto" value="Aplicar" placeholder="R$" style="margin-left:3px; width: 70px;" />
                </div>

                <div class="span3">
                    <label for="valor_desconto">Val.Desc <i class="icon-info-sign tip-left" title="Não altere esta campo, caso clicar nele e sair e ficar vázio, terá que recarregar á pagina e inserir de novo"></i></label>
                    <input class="span12 money" id="valor_desconto" readOnly="true" title="Não altere este campo" type="text" name="valor_desconto" value="<?php echo number_format("0.00", 2, ',', '.') ?>" />
                </div>

                <div class="span4" style="margin-left: 0">
                    <label for="vencimento">Data Vencimento*</label>
                    <input class="span12 datepicker" autocomplete="off" id="vencimento" type="text" name="vencimento" value="<?php echo date('d/m/Y'); ?>" required />
                </div>
                <div class="span6" id="parcelado_label_div">
                    <label id="parcelado_label">Fatura pré</label>
                    <input class="btn btn-inverse" id="parcelado" type="button" name="Parcelado" value="Fatura pré" style="margin-left:3px; width: 80%;" />
                </div>
                <div class="span6" id="qtdparcelas_div" style="display: none">
                    <label for="qtdparcelas">Qtd Parcelas</label>
                    <select name="qtdparcelas" id="qtdparcelas" class="span10">
                        <option value="1">1x</option>
                        <option value="2">2x</option>
                        <option value="3">3x</option>
                        <option value="4">4x</option>
                        <option value="5">5x</option>
                        <option value="6">6x</option>
                        <option value="7">7x</option>
                        <option value="8">8x</option>
                        <option value="9">9x</option>
                        <option value="10">10x</option>
                        <option value="11">11x</option>
                        <option value="12" selected>12x</option>
                        <option value="13">13x</option>
                        <option value="14">14x</option>
                        <option value="15">15x</option>
                        <option value="16">16x</option>
                        <option value="17">17x</option>
                        <option value="18">18x</option>
                        <option value="19">19x</option>
                        <option value="20">20x</option>
                        <option value="21">21x</option>
                        <option value="22">22x</option>
                        <option value="23">23x</option>
                        <option value="24">24x</option>
                        <option value="25">25x</option>
                        <option value="26">26x</option>
                        <option value="27">27x</option>
                        <option value="28">28x</option>
                        <option value="29">29x</option>
                        <option value="30">20x</option>
                        <option value="31">31x</option>
                        <option value="32">32x</option>
                        <option value="33">33x</option>
                        <option value="34">34x</option>
                        <option value="35">35x</option>
                        <option value="36">36x</option>
                        <option value="37">37x</option>
                        <option value="38">38x</option>
                        <option value="39">39x</option>
                        <option value="40">40x</option>
                        <option value="41">41x</option>
                        <option value="42">42x</option>
                    </select>
                    <a href="#modalReceitaParcelada" id="abrirmodalreceitaparcelada" data-toggle="modal" style="display: none;" role="button"> </a>
                </div>
                <div class="span2" style="margin-left: 0">
                    <div class="span3" style="margin-left: 0">
                        <label for="recebido">Pago?</label>
                        <input id="recebido" type="checkbox" name="recebido" value="1" checked />
                    </div>
                </div>

                <!-- <div id="divRecebimento" class="span12" style=" display: none;margin-left: 0"> -->
                <div id="divRecebimento" class="span12" style=" margin-left: 0">
                    <div class="span4" style="margin-left: 0">
                        <label for="recebimento">Data Pgto</label>
                        <input class="span12 datepicker" autocomplete="off" id="recebimento" type="text" name="recebimento" value="<?php echo date('d/m/Y'); ?>" />
                    </div>
                    <div class="span4">
                        <label for="formaPgto">Forma Pgto</label>
                        <select name="formaPgto" id="formaPgto" class="span12">
                            <option value="Dinheiro">Dinheiro</option>
                            <option value="Pix">Pix</option>
                            <option value="Cartão de Débito">Cartão de Débito</option>
                            <option value="Depósito">Depósito</option>
                        </select>
                    </div>
                    <div class="span4">
                        <label for="conta">Conta Pgto</label>
                        <select name="conta" id="conta" class="span12">
                            <?php foreach ($contas as $u) {
                                echo '<option value="' . $u->idContas . '">' . $u->conta . '</option>';
                            } ?>

                        </select>
                    </div>
                </div>

            </div>

        </div>
        <div class="modal-footer" style="display:flex;justify-content: right">
            <button class="button btn btn-warning" id="cancelar_nova_receita" data-dismiss="modal" aria-hidden="true" style="min-width: 110px">
                <span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span></button>
            <button class="button btn btn-primary" style="min-width: 110px">
                <span class="button__icon"><i class='bx bx-save'></i></span><span class="button__text2">Adicionar Registro</span></button>
        </div>
    </form>
</div>

<!-- Modal nova receita e despesa parcelada -->
<div id="modalReceitaParcelada" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form id="formReceita_parc" action="<?php echo base_url() ?>index.php/financeiro/adicionarReceita_parc" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">Adicionar Despesa/Receita Parcelada</h3>
        </div>
        <div class="modal-body">
            <div class="span12 alert alert-info" style="margin-left: 0"> Obrigatório o preenchimento dos campos com asterisco.</div>
            <div class="span3" style="margin-left: 0">
                <label for="tipo_parc" style="margin-left: 0">Tipo</label>
                <select name="tipo_parc" id="tipo_parc" class="span10">
                    <option value="despesa">Despesa</option>
                    <option value="receita">Receita</option>
                </select>
            </div>

            <div class="span9" style="margin-left: 0">
                <label for="descricao_parc">Descrição/Referência*</label>
                <input class="span12" id="descricao_parc" type="text" name="descricao_parc" required />
                <input id="urlAtual" type="hidden" name="urlAtual" value="<?php echo current_url() ?>" />
            </div>

            <div class="span5" style="margin-left: 0">
                <label for="centGast" style="margin-left: 0">Centro de gastos</label>
                <select name="centGast" id="centGast_parc" class="span10" required>
                    <option value="SERVICOS">SERVICOS</option>
                    <option value="VENDAS">VENDAS</option>
                    <option value="OPERACIONAIS">OPERACIONAIS</option>
                    <option value="RH">RH</option>
                    <option value="ADMINISTRATIVO">ADMINISTRATIVO</option>
                    <option value="MARKETING">MARKETING</option>
                    <option value="GASTOS FINANCEIROS">GASTOS FINANCEIROS</option>
                    <option value="INVESTISTIMENTOS">INVESTISTIMENTOS</option>
                </select>
            </div>
            <div class="span5" style="margin-left: 0">
                <label for="clasFin" style="margin-left: 0">Clas. finaceira</label>
                <select name="clasFin" id="clasFin_parc" class="span12" required>
                    <option value="">Selecione</option>
                    <?php foreach ($classificacao_financeira as $f) {
                        echo '<option value="' . $f->nomeClassFin . '">' . $f->nomeClassFin . '</option>';
                    } ?>
                </select>
            </div>
            <div class="span2" style="margin-left: 1">
                <label for="documento_parc">Nº Doc.</label>
                <input class="span12" id="documento_parc" type="text" name="documento_parc" placeholder="Os/Venda" />

            </div>
            <!--  -->
            <div class="span5" style="margin-left: 0">
                <label id="label_cliente_par"><input type="radio" value="option1" name="campo-radio" id="cliente_label_par" checked />&nbspCliente/Fornecedor*</label>
                <label id="label_Usuario_par"><input type="radio" value="option2" name="campo-radio" id="usuario_label_par" />&nbspUsuario*</label>
            </div>
            <div class="span7" style="margin-left: 0">
                <input class="span12" id="cliente_parc" type="text" name="cliente_parc" />
                <input class="span12" id="idCliente_parc" type="hidden" name="idCliente_parc" value="" />

                <select name="usuario_parc" id="usuario_parc" class="span12" style="display:none">
                    <option value="">Selecione</option>
                    <?php foreach ($usuarios as $u) {
                        echo '<option value="' . $u->nome . '">' . $u->nome . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="span12" style="margin-left: 0">
                <label for="observacoes_parc">Observações</label>
                <textarea class="span12" id="observacoes_parc" name="observacoes_parc"></textarea>
            </div>

            <div class="span12" style="margin-left: 0">
                <div class="span3" style="margin-left: 0">
                    <label for="valor_parc">Valor*</label>
                    <input class="span12 money" id="valor_parc" type="text" name="valor_parc" required />
                </div>

                <div class="span4" style="margin-left: 2">
                    <label for="descontos_parc">Descontos</label>
                    <input class="span6 money" id="descontos_parc" type="text" name="descontos_parc" value="" placeholder="em R$" style="float: left;" />
                    <input class="btn btn-inverse" onclick="mostrarValoresParc();" type="button" name="desconto_parc" value="Aplicar" placeholder="R$" style="width: 70px; margin-left:3px;" />
                </div>

                <div class="span3" style="margin-left: 0">
                    <label for="desconto_parc">Valor S/desc <i class="icon-info-sign tip-left" title="Não altere esta campo, caso clicar nele e sair e ficar vázio, terá que recarregar á pagina e inserir de novo"></i></label>
                    <input type="hidden" class="span10 money" id="desconto_parc" readOnly="true" title="Não altere este campo" type="text" name="desconto_parc" value="<?php echo number_format("0.00", 2, ',', '.') ?>" style="float: left;" />
                    <input class="span10 money" id="valor_parc_sem_desconto" readOnly="true" title="Não altere este campo" type="text" name="valor_parc_sem_desconto" value="<?php echo number_format("0.00", 2, ',', '.') ?>" style="float: left;" />
                </div>

                <div id="divParcelamento" class="span2" style="margin-left: 0">
                    <label for="qtdparcelas_parc">Parcelas</label>
                    <select name="qtdparcelas_parc" id="qtdparcelas_parc" class="span12" style="margin-left: 0">
                        <option value="1">1x</option>
                        <option value="2">2x</option>
                        <option value="3">3x</option>
                        <option value="4">4x</option>
                        <option value="5">5x</option>
                        <option value="6">6x</option>
                        <option value="7">7x</option>
                        <option value="8">8x</option>
                        <option value="9">9x</option>
                        <option value="10">10x</option>
                        <option value="11">11x</option>
                        <option value="12">12x</option>
                        <option value="13">13x</option>
                        <option value="14">14x</option>
                        <option value="15">15x</option>
                        <option value="16">16x</option>
                        <option value="17">17x</option>
                        <option value="18">18x</option>
                        <option value="19">19x</option>
                        <option value="20">20x</option>
                        <option value="21">21x</option>
                        <option value="22">22x</option>
                        <option value="23">23x</option>
                        <option value="24">24x</option>
                        <option value="25">25x</option>
                        <option value="26">26x</option>
                        <option value="27">27x</option>
                        <option value="28">28x</option>
                        <option value="29">29x</option>
                        <option value="30">20x</option>
                        <option value="31">31x</option>
                        <option value="32">32x</option>
                        <option value="33">33x</option>
                        <option value="34">34x</option>
                        <option value="35">35x</option>
                        <option value="36">36x</option>
                        <option value="37">37x</option>
                        <option value="38">38x</option>
                        <option value="39">39x</option>
                        <option value="40">40x</option>
                        <option value="41">41x</option>
                        <option value="42">42x</option>
                    </select>
                </div>
                <div class="span4" style="margin-left: 0">
                    <label for="formaPgto_parc">Forma Pgto</label>
                    <select name="formaPgto_parc" id="formaPgto_parc" class="span12" style="margin-left: 0">
                        <option value="Boleto">Boleto</option>
                        <option value="Cartão de Crédito">Cartão de Crédito</option>
                        <option value="Cartão de Débito">Cartão de Débito</option>
                        <option value="Cheque">Cheque</option>
                        <option value="Cheque Pré-datado">Cheque Pré-datado</option>
                        <option value="Depósito">Depósito</option>
                        <option value="Transferência DOC">Transferência DOC</option>
                        <option value="Transferência TED">Transferência TED</option>
                    </select>
                </div>
                <div class="span5" style="margin-left: 0">

                </div>
                <div class="span3" style="margin-left: 0">
                    <label for="multiplica_parc">Parcelas</label>
                    <select name="multiplica_parc" id="multiplica_parc" class="span12" style="margin-left: 0">
                        <option value="1">MULTIPLICA</option>
                        <option value="0">DIVIDE</option>
                    </select>
                </div>
            </div>

            <div class="span12" style="margin-left: 0;">
                <div class="span4">
                    <label for="entrada">Entrada <i class="icon-info-sign tip-right" title="O valor da entrada será lançado como pago no dia atual (Hoje)"></i></label>
                    <input class="span12 money" id="entrada" type="text" name="entrada" value="0" />
                </div>

                <div class="span4" style="margin-left: 1">
                    <label for="dia_pgto">Data da Entrada*</label>
                    <input class="span12 datepicker" id="dia_pgto" type="text" name="dia_pgto" value="<?php echo date('d/m/Y'); ?>" autocomplete="off" required />
                </div>
                <?php
                $myDateTime = new DateTime();
                date_modify($myDateTime, "+ 1 months");
                ?>

                <div class="span4" style="margin-left: 1">
                    <label for="dia_base_pgto">Data 1º de Pgto* <i class="icon-info-sign tip-left" title="Dia do mês que serão lançadas as parcelas restantes, parcela inicial na data selecionada."></i></label>
                    <input class="span12 datepicker" id="dia_base_pgto" type="text" autocomplete="off" name="dia_base_pgto" required />
                </div>

                <div class="span12" style="background:#f5f5f5;border-radius:4px;margin: 0;border:1px solid #ddd;">
                    <input id="valorparcelas" type="hidden" name="valorparcelas" readonly />
                    <div class="span12" style="margin: 14px 0 0 0;float:right;text-align: center; color:#b94a48">
                        <label id="string_parc" style="font-weight: bold;"></label>
                    </div>
                </div>

            </div>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true"><span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span></button>
            <button class="button btn btn-success" id="submitReceita"><span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar Registro</span></button>
        </div>
    </form>
</div>


<!-- Modal editar-->
<div id="modalEditar" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <!--  Tabs -->
    <ul class="nav nav-tabs">
        <li class="active" id="tabEditLancamentos"><a href="#tab11" data-toggle="tab">Edit Lancamentos</a></li>
        <li id="tabAnexos"><a href="#tab22" data-toggle="tab">Anexos</a></li>
        <li id="tabAnexos"><a href="#tab33" data-toggle="tab">Casamento de Pagamentos</a></li>
    </ul>
    <div class="tab-content">

        <!--editar-->
        <div class="tab-pane active" id="tab11">
            <form id="formEditar" action="<?php echo base_url() ?>index.php/financeiro/editar" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 id="myModalLabel">Editar Lançamento</h3>
                </div>
                <div class="modal-body">
                    <div class="span12 alert alert-info" style="margin-left: 0"> Obrigatório o preenchimento dos campos com
                        asterisco.
                    </div>
                    <div class="span12" style="margin-left: 0">
                        <label for="descricao">Descrição/Referência*</label>
                        <input class="span12" id="descricaoEditar" type="text" name="descricao" required />
                        <input id="urlAtualEditar" type="hidden" name="urlAtual" value="" />
                    </div>

                    <div class="span5" style="margin-left: 0">
                        <label for="centGast" style="margin-left: 0">Centro de gastos</label>
                        <select name="centGast" id="centro_de_gastosEditar" class="span10">
                            <option value="SERVICOS">SERVICOS</option>
                            <option value="VENDAS">VENDAS</option>
                            <option value="OPERACIONAIS">OPERACIONAIS</option>
                            <option value="MARKETING">MARKETING</option>
                            <option value="RH">RH</option>
                            <option value="ADMINISTRATIVO">ADMINISTRATIVO</option>
                            <option value="GASTOS FINANCEIROS">GASTOS FINANCEIROS</option>
                            <option value="INVESTISTIMENTOS">INVESTISTIMENTOS</option>
                        </select>
                    </div>

                    <div class="span7" style="margin-left: 0">
                        <label for="clasFin" style="margin-left: 0">Clas. finaceira</label>
                        <select name="clasFin" id="classificacao_finEditar" class="span12">
                            <option value="">Selecione</option>
                            <?php foreach ($classificacao_financeira as $f) {
                                echo '<option value="' . $f->nomeClassFin . '">' . $f->nomeClassFin . '</option>';
                            } ?>
                        </select>
                    </div>

                    <div class="span5" style="margin-left: 0">
                        <label id="label_cliente_r_editar"><input type="radio" value="option1" name="campo-radio" id="cliente_r_editar" checked />&nbspCliente/Fornecedor*</label>
                        <label><input type="radio" value="option2" name="campo-radio" id="usuario_r_editar" />&nbspUsuario*</label>
                    </div>
                    <div class="span7" style="margin-left: 0">
                        <input class="span12" id="fornecedorEditar" type="text" name="fornecedor" />
                        <select class="span12" id="usuario_editar" name="usuario">
                            <option value="">Selecione</option>
                            <?php foreach ($usuarios as $u) {
                                echo '<option value="' . $u->nome . '">' . $u->nome . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="span12" style="margin-left: 0">
                        <label for="observacoes">Observações</label>
                        <textarea class="span12" id="observacoes_edit" name="observacoes"></textarea>
                    </div>

                    <div class="span12" style="margin-left: 0">
                        <div class="span4" style="margin-left: 0">
                            <label for="valor">Valor*</label>
                            <input type="hidden" id="idEditar" name="id" value="" />
                            <input class="span12 money" type="text" name="valor" id="valorEditar" value="<?php echo number_format("0.00", 2, ',', '.') ?>" required />
                        </div>

                        <div class="span4">
                            <label for="descontos">Desconto</label>
                            <input class="span6 money" id="descontos_editar" type="text" name="descontos_editar" value="" placeholder="em R$" style="float: left;" />
                            <input class="btn btn-inverse" onclick="mostrarValoresEditar();" type="button" name="valor_descontos_editar" value="Aplicar" placeholder="R$" style="width: 70px; margin-left:3px;" />
                        </div>

                        <div class="span2">
                            <label for="valorEditar_sem_desconto">Val. S/desc</label>
                            <input class="span12 money" id="valorEditar_sem_desconto" name="valorEditar_sem_desconto" type="text" value="<?php echo number_format("0.00", 2, ',', '.') ?>" />
                        </div>

                        <div class="span4" style="margin-left: 0">
                            <label for="vencimento">Data Vencimento*</label>
                            <input class="span12 datepicker2" type="text" name="vencimento" id="vencimentoEditar" autocomplete="off" required />
                        </div>
                        <div class="span4">
                            <label for="vencimento">Tipo*</label>
                            <select class="span12" name="tipo" id="tipoEditar">
                                <option value="receita">Receita</option>
                                <option value="despesa">Despesa</option>
                            </select>
                        </div>

                    </div>
                    <div class="span12" style="margin-left: 0">
                        <div class="span4" style="margin-left: 0">
                            <label for="pago">Foi Pago?</label>
                            &nbsp &nbsp &nbsp &nbsp<input id="pagoEditar" type="checkbox" name="pago" value="1" />
                        </div>
                        <div id="divPagamentoEditar" class="span8" style=" display: none">
                            <div class="span4">
                                <label for="pagamento">Data Pagamento</label>
                                <input class="span12 datepicker2" id="pagamentoEditar" type="text" name="pagamento" autocomplete="off" />
                            </div>
                            <div class="span4">
                                <label for="formaPgto">Forma Pgto</label>
                                <select name="formaPgto" class="span12">
                                    <option value="Dinheiro">Dinheiro</option>
                                    <option value="Pix">Pix</option>
                                    <option value="Depósito">Depósito</option>
                                </select>
                            </div>
                            <div class="span4">
                                <label for="conta">Conta Pgto</label>
                                <select name="conta" id="conta" class="span12">
                                    <?php foreach ($contas as $u) {
                                        echo '<option value="' . $u->idContas . '">' . $u->conta . '</option>';
                                    } ?>

                                </select>
                            </div>

                        </div>

                    </div>

                </div>
                <div class="modal-footer" style="display:flex;justify-content: center">
                    <label for="documento" class="control-label">Modificado por: </label>
                    <div class="controls span4">
                        <input disabled id="usuarioEditar" value="" style="background-color: #f5f5f5; border-color: transparent; height: 10px">
                    </div>
                    <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true" id="btnCancelarEditar" style="min-width: 110px">
                        <span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span></button>
                    <button class="button btn btn-primary" style="min-width: 110px">
                        <span class="button__icon"><i class='bx bx-save'></i></span><span class="button__text2">Salvar</span></button>
                </div>
            </form>
        </div>

        <!--Anexos-->
        <div class="tab-pane" id="tab22">
            <div class="span12" style="padding: 1%; margin-left: 0">
                <div class="span12 well" style="padding: 1%; margin-left: 0" id="form-anexos">
                    <form id="formAnexos" enctype="multipart/form-data" action="javascript:;" accept-charset="utf-8" method="post">
                        <div class="span6">
                            <input type="hidden" name="idLancamento" id="idLancamentoAnexo" value="" />
                            <label for="">Anexo</label>
                            <input type="file" class="span12" name="userfile[]" multiple="multiple" size="20" />
                        </div>

                        <div class="span6" style="margin-left: 0; display: flex; gap: 10px;">
                            <div style="flex: 1;">
                                <label for="">.</label>
                                <button class="button btn btn-success" style="width: 100%;">
                                    <span class="button__icon"><i class='bx bx-paperclip'></i></span><span class="button__text2">Anexar</span>
                                </button>
                            </div>

                            <div style="flex: 1;">
                                <label for="">.</label>
                                <input type="hidden" name="img" />
                                <label class="button btn btn-success" style="cursor: pointer; width: 100%; margin-bottom: 0;">
                                    <span class="button__icon"><i class="fas fa-camera"></i></span>
                                    <span class="button__text2"> Câmera Mobile</span>
                                    <input type="file" name="userfile[]" accept="image/*" capture="environment" multiple="multiple" style="display: none;" onchange="$('#formAnexos').submit();" />
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="span12 pull-left" id="divAnexos" style="margin-left: 0"></div>
            </div>
        </div>


        <!--casamentos-->
        <div class="tab-pane" id="tab33">
            <div class="span12" style="padding: 1%; margin-left: 0">
            </div>
        </div>
    </div>
</div>

<!-- Modal Excluir lançamento-->
<div id="modalExcluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Excluir Lançamento</h3>
    </div>
    <div class="modal-body">
        <h5 style="text-align: center">Deseja realmente excluir esse lançamento?</h5>
        <input name="id" id="idExcluir" type="hidden" value="" />
    </div>
    <div class="modal-footer" style="display:flex;justify-content:center;">
        <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true" id="btnCancelExcluir"><span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span></button>
        <button class="button btn btn-danger" id="btnExcluir"><span class="button__icon"><i class='bx bx-trash'></i></span> <span class="button__text2">Excluir</span></button>
    </div>
</div>

<!-- Modal Status OS Calendar -->
<div id="calendarModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Fatura Detalhada</h3>
    </div>
    <div class="modal-body">
        <div class="span5" id="divFormStatusOS" style="margin-left: 0"></div>
        <h4><b>Fautura:</b> <span id="modalId" class="modal-id"></span></h4>
        <h5 id="modalCliente" class="modal-cliente"></h5>
        <div id="modalEndereco" class="modal-Endereco"></a></div>
        <div id="modalDataInicial" class="modal-DataInicial"></div>
        <div id="modalDataFinal" class="modal-DataFinal"></div>
        <div id="modalGarantia" class="modal-Garantia"></div>
        <div id="modalStatus" class="modal-Status"></div>
        <div id="modalDescription" class="modal-Description"></div>
        <div id="modalDefeito" class="modal-Defeito"></div>
        <div id="modalObservacoes" class="modal-Observacoes"></div>
        <div id="modalTotal" class="modal-Total"></div>
        <div id="modalDesconto" class="modal-Total"></div>
        <div id="modalValorFaturado" class="modal-ValorFaturado"></div>
    </div>
    <div class="modal-footer">
        <?php
        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento')) {
            echo '<a id="modalIdVisualizar" style="margin-right: 1%" href="" class="btn tip-top" title="Ver mais detalhes"><i class="fas fa-eye"></i></a>';
        }
        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eLancamento')) {
            echo '<a id="modalIdEditar" style="margin-right: 1%" href="" class="btn btn-info tip-top" title="Editar OS"><i class="fas fa-edit"></i></a>';
        }

        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dLancamento')) {
            echo '<a id="linkExcluir" href="#modal-excluir-os" role="button" data-toggle="modal" os="" class="btn btn-danger tip-top" title="Excluir OS"><i class="fas fa-trash-alt"></i></a>  ';
        }
        ?>
        <a id="modalIdEditar" style="margin-right: 1%" href="" class="btn btn-info tip-top" title="Editar OS"><i class="fas fa-edit"></i></a>
    </div>
</div>

<!-- Modal visualizar anexo -->
<div id="modal-anexo" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Visualizar Anexo</h3>
    </div>
    <div class="modal-body">
        <div class="span12" id="div-visualizar-anexo" style="text-align: center">
            <div class='progress progress-info progress-striped active'>
                <div class='bar' style='width: 100%'></div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Fechar</button>
        <a href="" id-imagem="" class="btn btn-inverse" id="download">Download</a>
        <a href="" link="" class="btn btn-danger" id="excluir-anexo">Excluir Anexo</a>
    </div>
</div>


<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>


<script type="text/javascript">
    /********************LANÇAMENTOS************************************ */
    let valor_Parcela = $('#multiplica_parc').val();

    function mostrarValor() {

        if (document.getElementById('valor').value == "" || document.getElementById('desconto').value == "") {

        } else {

            var valor = parseFloat(document.getElementById('valor').value);
            var desconto = parseInt(document.getElementById('desconto').value);
            var valor_desconto = parseFloat(document.getElementById('valor_desconto').value);
            var resultado, total;
            resultado = valor / 100;
            total = valor - (desconto * resultado);

            resultdesc = total;
            totaldesc = valor - (resultdesc);

            document.getElementById('valor').value = total.toFixed(2);
            document.getElementById('valor_desconto').value = totaldesc.toFixed(2);
            document.getElementById('valor_sem_desconto').value = valor.toFixed(2);
        }
    }

    function mostrarValores() {

        if (document.getElementById('valor').value == "" || document.getElementById('descontos').value == "" || document.getElementById('valor_desconto').value == "") {


        } else {
            var valor = parseFloat(document.getElementById('valor').value);
            var desconto = parseFloat(document.getElementById('descontos').value);
            var valor_desconto = parseFloat(document.getElementById('valor_desconto').value);
            var resultado, total;
            resultado = valor;
            total = valor - desconto;

            resultdesc = total;
            totaldesc = valor - (resultdesc);

            document.getElementById('valor').value = total.toFixed(2);
            document.getElementById('valor_desconto').value = totaldesc.toFixed(2);
            document.getElementById('valor_sem_desconto').value = valor.toFixed(2);
        }
    }

    function mostrarValoresEditar() {
        if (document.getElementById('valorEditar').value == "" || document.getElementById('descontos_editar').value == "" || document.getElementById('descontoEditar').value == "" || document.getElementById('valorEditar_sem_desconto').value == "") {

        } else {
            var valor = parseFloat(document.getElementById('valorEditar').value);
            var desconto = parseFloat(document.getElementById('descontos_editar').value);
            var valor_desconto = parseFloat(document.getElementById('descontos_editar').value);
            var resultado, total;
            resultado = valor;
            total = valor - desconto;

            resultdesc = total;
            totaldesc = valor - (resultdesc);

            document.getElementById('valorEditar').value = total.toFixed(2);
            document.getElementById('descontos_editar').value = totaldesc.toFixed(2);
            document.getElementById('valorEditar_sem_desconto').value = valor.toFixed(2);
        }
    }

    function mostrarValoresParc() {
        if (document.getElementById('valor_parc').value == "" || document.getElementById('descontos_parc').value == "" || document.getElementById('desconto_parc').value == "") {

        } else {
            var valor = parseFloat(document.getElementById('valor_parc').value);
            var desconto = parseFloat(document.getElementById('descontos_parc').value);
            var valor_desconto = parseFloat(document.getElementById('desconto_parc').value);
            var resultado, total;
            resultado = valor;
            total = valor - desconto;

            resultdesc = total;
            totaldesc = valor - (resultdesc);

            document.getElementById('valor_parc').value = total.toFixed(2);
            document.getElementById('desconto_parc').value = totaldesc.toFixed(2);
            document.getElementById('valor_parc_sem_desconto').value = valor.toFixed(2);
        }
    }

    $(document).ready(function() {
        $('#usuario_editar').hide();
        $('#usuario').hide();
        //$('#fornecedorEditar').remove();

        $('#recebido').click(function(event) {
            var flag = $(this).is(':checked');
            if (flag == true) {
                $('#divRecebimento').show();
            } else {
                $('#divRecebimento').hide();
            }
        });

        $('#parcelado').click(function(event) {
            /* $('#qtdparcelas_div').show();
            $('#parcelado_label_div').hide(); */

            var parcelas = $("#qtdparcelas").val();
            if (parcelas > 1) {
                $('#cancelar_nova_receita').trigger('click');
                $('#abrirmodalreceitaparcelada').trigger('click');
                $("#descricao_parc").val($("#descricao").val());
                $("#centGast_parc").val($("#centGast").val());
                $("#clasFin_parc").val($("#clasFin").val());
                $("#usuario_parc").val($("#usuario").val());
                $("#cliente_parc").val($("#cliente").val());
                $("#idCliente_parc").val($("#idCliente").val());
                $("#tipo_parc").val($("#tipo").val());
                $("#formaPgto_parc").val($("#formaPgto").val());
                $("#pcontas_parc").val($("#pcontas").val());
                $("#categoria_parc").val($("#categoria").val());
                $("#observacoes_parc").val($("#observacoes").val());
                $("#valor_parc").val($("#valor").val());
                $("#desconto_parc").val($("#valor_desconto").val());
                $("#qtdparcelas_parc").val($("#qtdparcelas").val());
                if (valor_Parcela == 1) {
                    valorParcelas_multiplica_parc();
                } else {
                    valorParcelas();
                }
            } else {
                if (parcelas == 1) {
                    $('#cancelar_nova_receita').trigger('click');
                    $('#abrirmodalreceitaparcelada').trigger('click');
                    $("#descricao_parc").val($("#descricao").val());
                    $("#centGast_parc").val($("#centGast").val());
                    $("#clasFin_parc").val($("#clasFin").val());
                    $("#usuario_parc").val($("#usuario").val());
                    $("#cliente_parc").val($("#cliente").val());
                    $("#idCliente_parc").val($("#idCliente").val());
                    $("#tipo_parc").val($("#tipo").val());
                    $("#formaPgto_parc").val($("#formaPgto").val());
                    $("#pcontas_parc").val($("#pcontas").val());
                    $("#categoria_parc").val($("#categoria").val());
                    $("#observacoes_parc").val($("#observacoes").val());
                    $("#desconto_parc").val($("#valor_desconto").val());
                    $("#valor_parc").val($("#valor").val());
                    $("#qtdparcelas_parc").val(1);
                    if (valor_Parcela == 1) {
                        valorParcelas_multiplica_parc();
                    } else {
                        valorParcelas();
                    }
                }
            }
        });


        $('#cliente_label_par').click(function(event) {
            $('#cliente_parc').show();
            $('#usuario_parc').hide();
            //$('#cliente_label').hide();
        });
        $('#usuario_label_par').click(function(event) {
            $('#usuario_parc').show();
            $('#cliente_parc').hide();
            //$('#cliente_label').hide();
        });

        $('#cliente_label').click(function(event) {
            $('#clienteAvista').show();
            $('#usuario').hide();
            //$('#cliente_label').hide();
        });
        $('#usuario_label').click(function(event) {
            $('#usuario').show();
            $('#clienteAvista').hide();
            //$('#cliente_label').hide();
        });

        $('#usuario_r_editar').click(function(event) {
            $('#usuario_editar').show();
            $('#fornecedorEditar').hide();
            $('#label_cliente_r_editar').hide();
        });

        $("#formFaturar").validate({
            rules: {
                descricao: {
                    required: true
                },
                cliente: {
                    required: true
                },
                valor: {
                    required: true
                },
                vencimento: {
                    required: true
                }

            },
            messages: {
                descricao: {
                    required: 'Campo Requerido.'
                },
                cliente: {
                    required: 'Campo Requerido.'
                },
                valor: {
                    required: 'Campo Requerido.'
                },
                vencimento: {
                    required: 'Campo Requerido.'
                }
            },
            submitHandler: function(form) {
                var dados = $(form).serialize();
                var qtdProdutos = $('#tblProdutos >tbody >tr').length;
                var qtdServicos = $('#tblServicos >tbody >tr').length;
                var qtdTotalProdutosServicos = qtdProdutos + qtdServicos;

                $('#btn-cancelar-faturar').trigger('click');

                if (qtdTotalProdutosServicos <= 0) {
                    Swal.fire({
                        type: "error",
                        title: "Atenção",
                        text: "Não é possível faturar uma OS sem serviços e/ou produtos"
                    });
                } else if (qtdTotalProdutosServicos > 0) {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url(); ?>index.php/os/faturar",
                        data: dados,
                        dataType: 'json',
                        success: function(data) {
                            if (data.result == true) {
                                window.location.reload(true);
                            } else {
                                Swal.fire({
                                    type: "error",
                                    title: "Atenção",
                                    text: "Ocorreu um erro ao tentar faturar OS."
                                });
                                $('#progress-fatura').hide();
                            }
                        }
                    });

                    return false;
                }
            }
        });

        $('#formDesconto').submit(function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                beforeSend: function() {
                    Swal.fire({
                        title: 'Processando',
                        text: 'Registrando desconto...',
                        icon: 'info',
                        showCloseButton: false,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                },
                success: function(response) {
                    if (response.result) {
                        Swal.fire({
                            type: "success",
                            title: "Sucesso",
                            text: response.messages
                        });
                        setTimeout(function() {
                            window.location.href = window.BaseUrl + 'index.php/os/editar/';
                        }, 2000);

                    } else {
                        Swal.fire({
                            type: "error",
                            title: "Atenção",
                            text: response.messages
                        });
                    }

                },
                error: function(response) {
                    Swal.fire({
                        type: "error",
                        title: "Atenção",
                        text: response.responseJSON.messages
                    });
                }
            });
        });

        function carregarAnexosLancamento(id) {
            if (!id) return;
            $("#divAnexos").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");
            $.get("<?php echo base_url(); ?>index.php/financeiro/listarAnexos/" + id, function(data) {
                $("#divAnexos").html(data);
            });
        }

        $(document).on('click', '.anexo', function(event) {
            event.preventDefault();
            var link = $(this).attr('link');
            var id = $(this).attr('imagem');
            var url = '<?php echo base_url(); ?>index.php/financeiro/excluirAnexo/';
            var ext = link.split('.').pop().toLowerCase();
            
            if (ext === 'pdf') {
                $("#div-visualizar-anexo").html('<iframe src="' + link + '" width="100%" height="400px" style="border: none;"></iframe>');
            } else if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'heic'].includes(ext)) {
                $("#div-visualizar-anexo").html('<img src="' + link + '" alt="">');
            } else {
                $("#div-visualizar-anexo").html('<div><i class="fas fa-file-alt" style="font-size: 100px; color: #555;"></i><br><br>Documento</div>');
            }
            $("#excluir-anexo").attr('link', url + id);
            $("#download").attr('href', "<?php echo base_url(); ?>index.php/financeiro/downloadanexo/" + id);
        });

        $(document).on('click', '#excluir-anexo', function(event) {
            event.preventDefault();
            var link = $(this).attr('link');
            var idLanc = $("#idLancamentoAnexo").val();
            $('#modal-anexo').modal('hide');
            $("#divAnexos").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");

            $.ajax({
                type: "POST",
                url: link,
                dataType: 'json',
                data: "idLancamento=" + idLanc,
                success: function(data) {
                    if (data.result == true) {
                        carregarAnexosLancamento(idLanc);
                    } else {
                        Swal.fire({
                            type: "error",
                            title: "Atenção",
                            text: data.mensagem
                        });
                    }
                }
            });
        });

        $("#formAnexos").validate({
            submitHandler: function(form) {
                var dados = new FormData(form);
                var idLanc = $("#idLancamentoAnexo").val();
                $("#form-anexos").hide('1000');
                $("#divAnexos").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>index.php/financeiro/anexar",
                    data: dados,
                    mimeType: "multipart/form-data",
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: 'json',
                    success: function(data) {
                        if (data.result == true) {
                            carregarAnexosLancamento(idLanc);
                            $("#formAnexos")[0].reset();
                        } else {
                            var msg = data.mensagem;
                            if (data.errors && data.errors.upload) {
                                msg += " " + data.errors.upload.join(', ');
                            }
                            $("#divAnexos").html('<div class="alert fade in"><button type="button" class="close" data-dismiss="alert">×</button><strong>Atenção!</strong> ' + msg + '</div>');
                            setTimeout(function() { carregarAnexosLancamento(idLanc); }, 4000);
                        }
                    },
                    error: function() {
                        $("#divAnexos").html('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert">×</button><strong>Atenção!</strong> Ocorreu um erro no servidor. Verifique se você anexou o(s) arquivo(s).</div>');
                        setTimeout(function() { carregarAnexosLancamento(idLanc); }, 4000);
                    }
                });
                $("#form-anexos").show('1000');
                return false;
            }
        });

        jQuery(document).ready(function($) {

            $(".money").maskMoney();

            $('#pago').click(function(event) {
                var flag = $(this).is(':checked');
                if (flag == true) {
                    $('#divPagamento').show();
                } else {
                    $('#divPagamento').hide();
                }
            });

            $('#recebido').click(function(event) {
                var flag = $(this).is(':checked');
                if (flag == true) {
                    $('#divRecebimento').show();
                } else {
                    $('#divRecebimento').hide();
                }
            });

            $('#pagoEditar').click(function(event) {
                var flag = $(this).is(':checked');
                if (flag == true) {
                    $('#divPagamentoEditar').show();
                } else {
                    $('#divPagamentoEditar').hide();
                }
            });


            $("#formReceita").validate({
                rules: {
                    descricao: {
                        required: true
                    },
                    cliente: {
                        required: true
                    },
                    valor: {
                        required: true
                    },
                    vencimento: {
                        required: true
                    }

                },
                messages: {
                    descricao: {
                        required: 'Campo Requerido.'
                    },
                    cliente: {
                        required: 'Campo Requerido.'
                    },
                    valor: {
                        required: 'Campo Requerido.'
                    },
                    vencimento: {
                        required: 'Campo Requerido.'
                    }
                },
                submitHandler: function(form) {
                    $("#submitReceita").attr("disabled", true);
                    form.submit();
                }
            });


            $("#formDespesa").validate({
                rules: {
                    descricao: {
                        required: true
                    },
                    fornecedor: {
                        required: true
                    },
                    valor: {
                        required: true
                    },
                    vencimento: {
                        required: true
                    }

                },
                messages: {
                    descricao: {
                        required: 'Campo Requerido.'
                    },
                    fornecedor: {
                        required: 'Campo Requerido.'
                    },
                    valor: {
                        required: 'Campo Requerido.'
                    },
                    vencimento: {
                        required: 'Campo Requerido.'
                    }
                },
                submitHandler: function(form) {
                    $("#submitDespesa").attr("disabled", true);
                    form.submit();
                }
            });


            $(document).on('click', '.excluir', function(event) {
                $("#idExcluir").val($(this).attr('idLancamento'));
            });


            $(document).on('click', '.editar', function(event) {
                $("#idEditar").val($(this).attr('idLancamento'));
                $("#descricaoEditar").val($(this).attr('descricao'));
                $("#usuarioEditar").val($(this).attr('usuario'));
                $("#fornecedorEditar").val($(this).attr('cliente'));
                $("#observacoes_edit").val($(this).attr('observacoes'));
                $("#valorEditar").val($(this).attr('valor'));
                $("#valorEditar_sem_desconto").val($(this).attr('valorEditar_sem_desconto'));
                $("#vencimentoEditar").val($(this).attr('vencimento'));
                $("#pagamentoEditar").val($(this).attr('pagamento'));
                $("#formaPgtoEditar").val($(this).attr('formaPgto'));
                $("#tipoEditar").val($(this).attr('tipo'));
                $("#descontos_editar").val($(this).attr('descontos_editar'));
                //$("#descontoEditar").val($(this).attr('valor_desconto_editar'));
                $("#centro_de_gastosEditar").val($(this).attr('centro_de_gastos'));
                $("#classificacao_finEditar").val($(this).attr('classificacao_fin'));
                $("#grupo_finaceiroEditar").val($(this).attr('grupo_finaceiro'));
                $("#urlAtualEditar").val($(location).attr('href'));
                var baixado = $(this).attr('baixado');
                if (baixado == 1) {
                    $("#pagoEditar").prop('checked', true);
                    $("#divPagamentoEditar").show();
                } else {
                    $("#pagoEditar").prop('checked', false);
                    $("#divPagamentoEditar").hide();
                }

                var idLanc = $(this).attr('idLancamento');
                $("#idLancamentoAnexo").val(idLanc);
                carregarAnexosLancamento(idLanc);
            });

            $(document).on('click', '#btnExcluir', function(event) {
                var id = $("#idExcluir").val();

                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>index.php/financeiro/excluirLancamento",
                    data: "id=" + id,
                    dataType: 'json',
                    success: function(data) {
                        if (data.result == true) {
                            $("#btnCancelExcluir").trigger('click');
                            $("#divLancamentos").html('<div class="progress progress-striped active"><div class="bar" style="width: 100%;"></div></div>');
                            $("#divLancamentos").load($(location).attr('href') + " #divLancamentos");

                        } else {
                            $("#btnCancelExcluir").trigger('click');
                            Swal.fire({
                                type: "error",
                                title: "Atenção",
                                text: "Ocorreu um erro ao tentar excluir lançamento."
                            });
                        }
                    }
                });
                return false;
            });

            let controlBaixa = "<?php echo $configuration['control_baixa']; ?>";
            let datePickerOptions = {
                dateFormat: 'dd/mm/yy',
            };
            if (controlBaixa === '1') {
                datePickerOptions.minDate = 0;
                datePickerOptions.maxDate = 0;
            }
            $(".datepicker2").datepicker(
                datePickerOptions
            );
            $(".datepicker").datepicker();
            $('#periodo').on('change', function(event) {
                const period = $('#periodo').val();

                switch (period) {
                    case 'dia':
                        $('#vencimento_de').val(dayjs().locale('pt-br').format('DD/MM/YYYY'));
                        $('#vencimento_ate').val(dayjs().locale('pt-br').format('DD/MM/YYYY'));
                        break;
                    case 'semana':
                        $('#vencimento_de').val(dayjs().startOf('week').locale('pt-br').format('DD/MM/YYYY'));
                        $('#vencimento_ate').val(dayjs().endOf('week').locale('pt-br').format('DD/MM/YYYY'));
                        break;
                    case 'mes':
                        $('#vencimento_de').val(dayjs().startOf('month').locale('pt-br').format('DD/MM/YYYY'));
                        $('#vencimento_ate').val(dayjs().endOf('month').locale('pt-br').format('DD/MM/YYYY'));
                        break;
                    case 'ano':
                        $('#vencimento_de').val(dayjs().startOf('year').locale('pt-br').format('DD/MM/YYYY'));
                        $('#vencimento_ate').val(dayjs().endOf('year').locale('pt-br').format('DD/MM/YYYY'));
                        break;
                }
            });

            $("#fornecedorEditar").autocomplete({
                source: "<?php echo base_url(); ?>index.php/financeiro/autoCompleteClienteAddReceita",
                minLength: 1,
                select: function(event, ui) {
                    $("#fornecedorEditar").val(ui.item.label);
                }
            });

            $("#clienteAvista").autocomplete({
                source: "<?php echo base_url(); ?>index.php/financeiro/autoCompleteClienteAddReceita",
                minLength: 1,
                select: function(event, ui) {
                    $("#clienteAvista").val(ui.item.label);
                    $("#idClienteAvista").val(ui.item.id);
                }
            });

            $("#cliente_busca").autocomplete({
                source: "<?php echo base_url(); ?>index.php/financeiro/autoCompleteClienteAddReceita",
                minLength: 1,
                select: function(event, ui) {
                    $("#cliente_busca").val(ui.item.label);
                }
            });


            $("#cliente_lanc").autocomplete({
                source: "<?php echo base_url(); ?>index.php/financeiro/autoCompleteClienteAddReceita",
                minLength: 1,
                select: function(event, ui) {
                    $("#cliente_lanc").val(ui.item.label);
                }
            });

            $("#cliente_parc").autocomplete({
                source: "<?php echo base_url(); ?>index.php/financeiro/autoCompleteClienteAddReceita",
                minLength: 1,
                select: function(event, ui) {
                    $("#cliente_parc").val(ui.item.label);
                    $("#idCliente_parc").val(ui.item.id);
                }
            });

            $("#fornecedor").autocomplete({
                source: "<?php echo base_url(); ?>index.php/financeiro/autoCompleteClienteAddReceita",
                minLength: 1,
                select: function(event, ui) {
                    $("#fornecedor").val(ui.item.label);
                    $("#idFornecedor").val(ui.item.id);
                }
            });

            function valorParcelas() {
                var valor_parc = $("#valor_parc").val();
                var qtdparc = $("#qtdparcelas_parc").val();
                var entrada = $("#entrada").val();
                var result = (valor_parc - entrada) / qtdparc;

                if (qtdparc > 1) {
                    if (entrada > 0) {
                        $("#string_parc").text('R$ ' + entrada + ' de entrada mais ' + qtdparc + ' parcelas de R$ ' + parseFloat(Math.round(result * 100) / 100).toFixed(2));
                        $("#valorparcelas").val(parseFloat(Math.round(result * 100) / 100).toFixed(2));
                    } else {
                        $("#string_parc").text(qtdparc + ' parcelas de R$ ' + parseFloat(Math.round(result * 100) / 100).toFixed(2));
                        $("#valorparcelas").val(parseFloat(Math.round(result * 100) / 100).toFixed(2));
                    }
                } else {
                    if (entrada > 0) {
                        $("#string_parc").text('R$ ' + entrada + ' de entrada mais ' + qtdparc + ' parcela de R$ ' + parseFloat(Math.round(result * 100) / 100).toFixed(2));
                        $("#valorparcelas").val(parseFloat(Math.round(result * 100) / 100).toFixed(2));
                    } else {
                        $("#string_parc").text(qtdparc + ' parcela de R$ ' + parseFloat(Math.round(result * 100) / 100).toFixed(2));
                        $("#valorparcelas").val(parseFloat(Math.round(result * 100) / 100).toFixed(2));
                    }
                }
            }

            function valorParcelas_multiplica_parc() {
                var valor_parc = $("#valor_parc").val();
                var qtdparc = $("#qtdparcelas_parc").val();

                var result = valor_parc;

                if (qtdparc > 1) {
                    $("#string_parc").text(qtdparc + ' parcelas de R$ ' + parseFloat(Math.round(result * 100) / 100).toFixed(2));
                    $("#valorparcelas").val(parseFloat(Math.round(result * 100) / 100).toFixed(2));
                } else {
                    $("#string_parc").text(qtdparc + ' parcela de R$ ' + parseFloat(Math.round(result * 100) / 100).toFixed(2));
                    $("#valorparcelas").val(parseFloat(Math.round(result * 100) / 100).toFixed(2));
                }
            }

            $('#multiplica_parc').change(function(event) {
                valor_Parcela = $('#multiplica_parc').val();
                valorParcelas_multiplica_parc();

            });

            /* troca modal avista para parcelado */
            $('#qtdparcelas').change(function(event) {
                var parcelas = $("#qtdparcelas").val();
                if (parcelas > 1) {
                    $('#cancelar_nova_receita').trigger('click');
                    $('#abrirmodalreceitaparcelada').trigger('click');
                    $("#descricao_parc").val($("#descricao").val());
                    $("#centGast_parc").val($("#centGast").val());
                    $("#clasFin_parc").val($("#clasFin").val());
                    $("#usuario_parc").val($("#usuario").val());
                    $("#cliente_parc").val($("#cliente").val());
                    $("#idCliente_parc").val($("#idCliente").val());
                    $("#tipo_parc").val($("#tipo").val());
                    $("#formaPgto_parc").val($("#formaPgto").val());
                    $("#pcontas_parc").val($("#pcontas").val());
                    $("#categoria_parc").val($("#categoria").val());
                    $("#observacoes_parc").val($("#observacoes").val());
                    $("#valor_parc").val($("#valor").val());
                    $("#desconto_parc").val($("#valor_desconto").val());
                    $("#qtdparcelas_parc").val($("#qtdparcelas").val());
                    if (valor_Parcela == 1) {
                        valorParcelas_multiplica_parc();
                    } else {
                        valorParcelas();
                    }
                } else {
                    if (parcelas == 1) {
                        $('#cancelar_nova_receita').trigger('click');
                        $('#abrirmodalreceitaparcelada').trigger('click');
                        $("#descricao_parc").val($("#descricao").val());
                        $("#centGast_parc").val($("#centGast").val());
                        $("#clasFin_parc").val($("#clasFin").val());
                        $("#usuario_parc").val($("#usuario").val());
                        $("#cliente_parc").val($("#cliente").val());
                        $("#idCliente_parc").val($("#idCliente").val());
                        $("#tipo_parc").val($("#tipo").val());
                        $("#formaPgto_parc").val($("#formaPgto").val());
                        $("#pcontas_parc").val($("#pcontas").val());
                        $("#categoria_parc").val($("#categoria").val());
                        $("#observacoes_parc").val($("#observacoes").val());
                        $("#desconto_parc").val($("#valor_desconto").val());
                        $("#valor_parc").val($("#valor").val());
                        $("#qtdparcelas_parc").val(1);
                        if (valor_Parcela == 1) {
                            valorParcelas_multiplica_parc();
                        } else {
                            valorParcelas();
                        }
                    }
                }
            });

            $('#valor_parc').keypress(function(event) {
                if (valor_Parcela == 1) {
                    valorParcelas_multiplica_parc();
                } else {
                    valorParcelas();
                }
            });

            $('#qtdparcelas_parc').change(function(event) {
                if (valor_Parcela == 1) {
                    valorParcelas_multiplica_parc();
                } else {
                    valorParcelas();
                }
            });

            $('#entrada').keypress(function(event) {
                valorParcelas();
                var entrada = $("#entrada").val();
                if (entrada > 0) {
                    $('#dia_pgto').css("color", "#444444");
                } else {
                    $('#dia_pgto').css("color", "#eeeeee");
                }
            });

            $('#valor_parc, #qtdparcelas_parc, #formaPgto_parc, #entrada, #dia_pgto, #dia_base_pgto').click(function(event) {
                if (valor_Parcela == 1) {
                    valorParcelas_multiplica_parc();
                } else {
                    valorParcelas();
                }
            });

            $('#add_receita').mouseover(function(event) {
                if (valor_Parcela == 1) {
                    valorParcelas_multiplica_parc();
                } else {
                    valorParcelas();
                }
            });

            $('#entrada').keypress(function(event) {
                if (valor_Parcela == 1) {
                    valorParcelas_multiplica_parc();
                } else {
                    valorParcelas();
                }
                var entrada = $("#entrada").val();
                if (entrada > 0) {
                    $('#dia_pgto').css("color", "#444444");
                } else {
                    $('#dia_pgto').css("color", "#eeeeee");
                }
            });
            $('#valor_parc, #qtdparcela_parc, #formaPgto_parc, #entrada, #dia_pgto, #dia_base_pgto').click(function(event) {
                if (valor_Parcela == 1) {
                    valorParcelas_multiplica_parc();
                } else {
                    valorParcelas();
                }
            });

            $('#add_receita').mouseover(function(event) {
                if (valor_Parcela == 1) {
                    valorParcelas_multiplica_parc();
                } else {
                    valorParcelas();
                }
            });
        });
        jQuery(document).ready(function($) {
            $('#alerta').hide();

        })

        /******************************************************************** */
        /******************** CALENDAR ************************************** */
        //recebe o seletor do atributo id
        var srcCalendarEl = document.getElementById('source-calendar');

        //instaciando calendar 
        var srcCalendar = new FullCalendar.Calendar(srcCalendarEl, {
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },

            locale: 'pt-br',
            height: 500,
            editable: false,
            selectable: false,
            businessHours: true,
            dayMaxEvents: true, // allow "more" link when too many events
            displayEventTime: false,
            events: {
                url: "<?= base_url() . "index.php/financeiro/calendario"; ?>",
                method: 'GET',
                extraParams: function() { // a function that returns an object
                    return {
                        status: $("#status").val(),
                        statusOsGet: $("#statusOsGet").val(),
                        vencimento_de_cal: $("#vencimento_de_cal").val(),
                        vencimento_ate_cal: $("#vencimento_ate_cal").val(),
                        centro_de_gastos_bsca: $("#centro_de_gastos_bsca_cal").val(),
                        classificacao_fin_bsca: $("#classificacao_fin_bsca_cal").val(),
                        grupo_finaceiro_bsca: $("#grupo_finaceiro_bsca_cal").val(),
                        forma_pgto_bsca: $("#forma_pgto_bsca_cal").val(),
                        cliente: $("#cliente_busca").val(),
                    };

                },
                success: function(info) {
                    console.log(info);


                    var evento = info;
                    var ultimo = $(evento).get(-1);
                    var totReceitas = parseFloat(ultimo.receitas || 0);
                    var totDespesas = parseFloat(ultimo.despesas || 0);
                    var tot = totReceitas - totDespesas;

                    $('#despesasTot').html(totDespesas.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    $('#receitasTot').html(totReceitas.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    if (tot >= 0) {
                        $('#tot').html(
                            '<strong style="text-align: right; color: green" id="tot">R$ ' + tot.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</strong>'
                        );
                    } else {
                        $('#tot').html(
                            '<strong style="text-align: right; color: red" id="tot">R$ ' + tot.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</strong>'
                        );
                    }
                },
                failure: function() {
                    $('#alerta').show();
                    //alert('Falha ao buscar OS de calendário!');
                },
            },
            eventClick: function(info) {
                //console.log(info);

                var eventObj = info.event.extendedProps;
                console.log(eventObj);

                $('#modalId').html(eventObj.id);
                /* $('#modalIdVisualizar').attr("href", "<?php echo base_url(); ?>index.php/financeiro/visualizar/" + eventObj.id); */
                $('#modalIdVisualizar').attr("href", "<?php echo base_url(); ?>index.php/financeiro/visualizar/" + eventObj.id);
                if (eventObj.editar) {
                    $('#modalIdEditar').show();
                    $('#linkExcluir').show();
                    /* $('#modalIdEditar').attr("href", "<?php echo base_url(); ?>index.php/os/editar/" + eventObj.id); */
                    $('#modalIdEditar').attr("href", "<?php echo base_url(); ?>index.php/os/editar/" + eventObj.id);
                    $('#modalIdExcluir').val(eventObj.id);
                } else {
                    $('#modalIdEditar').hide();
                    $('#linkExcluir').hide();
                }
                $('#modalCliente').html(eventObj.cliente);
                //$('#modalEndereco').html(eventObj.endereco);
                $('#modalDataInicial').html(eventObj.data_pagamento);
                $('#modalDataFinal').html(eventObj.data_vencimento);
                $('#modalGarantia').html(eventObj.forma_pgto);
                $('#modalStatus').html(eventObj.tipo);
                $('#modalDescription').html(eventObj.description);
                $('#modalDefeito').html(eventObj.defeito);
                $('#modalObservacoes').html(eventObj.observacoes);
                $('#modalTotal').html(eventObj.valor);
                console.log(eventObj.tipo)
                //$('#modalDesconto').html(eventObj.desconto);
                //$('#modalValorFaturado').html(eventObj.valorFaturado);

                $('#eventUrl').attr('href', event.url);
                $('#calendarModal').modal();
            },
        });

        <?php if ($lancamento == "0") {
            echo 'srcCalendar.render();';
        } ?>

        //srcCalendar.render();
        //var teste = srcCalendar.getEvents();
        //console.log(teste.start);

        $('#btn-calendar').on('click', function() {
            var dataDe = $('#vencimento_de_cal').val();
            if (dataDe) {
                var partes = dataDe.split('/');
                if (partes.length === 3) {
                    srcCalendar.gotoDate(partes[2] + '-' + partes[1] + '-' + partes[0]);
                }
            }
            srcCalendar.refetchEvents();
        });
        /********************************************************* */

        $('#periodo').on('change', function() {
            var val = $(this).val();
            var hoje = new Date();
            var dd = String(hoje.getDate()).padStart(2, '0');
            var mm = String(hoje.getMonth() + 1).padStart(2, '0');
            var yyyy = hoje.getFullYear();
            
            if (val === 'dia') {
                $('#vencimento_de').val(dd + '/' + mm + '/' + yyyy);
                $('#vencimento_ate').val(dd + '/' + mm + '/' + yyyy);
            } else if (val === 'mes') {
                var ultimoDia = new Date(yyyy, hoje.getMonth() + 1, 0).getDate();
                $('#vencimento_de').val('01/' + mm + '/' + yyyy);
                $('#vencimento_ate').val(String(ultimoDia).padStart(2, '0') + '/' + mm + '/' + yyyy);
            } else if (val === 'ano') {
                $('#vencimento_de').val('01/01/' + yyyy);
                $('#vencimento_ate').val('31/12/' + yyyy);
            } else if (val === 'semana') {
                var curr = new Date();
                var primeiro = new Date(curr.setDate(curr.getDate() - curr.getDay()));
                var ultimo = new Date(curr.setDate(curr.getDate() - curr.getDay() + 6));
                var dP = String(primeiro.getDate()).padStart(2, '0');
                var mP = String(primeiro.getMonth() + 1).padStart(2, '0');
                var yP = primeiro.getFullYear();
                var dU = String(ultimo.getDate()).padStart(2, '0');
                var mU = String(ultimo.getMonth() + 1).padStart(2, '0');
                var yU = ultimo.getFullYear();
                $('#vencimento_de').val(dP + '/' + mP + '/' + yP);
                $('#vencimento_ate').val(dU + '/' + mU + '/' + yU);
            }
        });

    });
</script>