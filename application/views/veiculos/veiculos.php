<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>

<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url() ?>assets/trumbowyg/ui/trumbowyg.css">
<script type="text/javascript" src="<?php echo base_url() ?>assets/trumbowyg/trumbowyg.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/trumbowyg/langs/pt_br.js"></script>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css" />



<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-diagnoses"></i>
                </span>
                <h5>Veiculos</h5>
                <!-- Botões -->
                <div class="buttons">

                    <a href="#" id="btn-faturar" role="button" data-toggle="modal" class="button btn btn-mini btn-success">
                        <span class="button__icon"><i class='bx bx-plus'></i></span> <span class="button__text">Veículos</span></a>
                </div>
            </div>
            <div class="widget-content nopadding tab-content">
                <div class="span12" id="divProdutosServicos" style=" margin-left: 0">
                    <!--  Tabs -->
                    <ul class="nav nav-tabs">
                        <li <?php if ($tab == "1") {
                                echo 'class="active" ';
                            } ?> id="tabDetalhes"><a href="#tab1" data-toggle="tab">Detalhes dos Veiculos</a></li>
                        <?php if ($tab == "2" || $tab == "3") { ?>
                            <li <?php if ($tab == "2") {
                                    echo 'class="active" ';
                                } ?> id="tabDesconto"><a href="#tab2" data-toggle="tab">Veiculos</a></li>
                            <li <?php if ($tab == "3") {
                                    echo 'class="active" ';
                                } ?> id="tabProdutos"><a href="#tab3" data-toggle="tab">Laçamentos Gasolina</a></li>
                        <?php } ?>

                    </ul>
                    <div class="tab-content">

                        <!--veiculos  | tab01 -->
                        <div class="tab-pane  <?php if ($tab == "1") {
                                                    echo 'active';
                                                } ?>" id="tab1">
                            <div class="span12 well" style="padding: 1%; margin-left: 0">

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
                                                            echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/veiculos/veiculo/' . $r->idVeiculos . '" class="btn-nwe3" title="Veiculo"><i class="bx bxs-car" style="color:#2945bb" ></i></a>';
                                                            echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/veiculos/gasolina/' . $r->idVeiculos . '" class="btn-nwe3" title="lançar gasolina"><i class="bx bxs-gas-pump" style="color:#4a9e58"  ></i></a>';

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
                        <!--tab02 e tab03-->
                        <?php if ($tab == "2" || $tab == "3") { ?>

                            <!--veiculo detalhes  | tab02-->
                            <div class="tab-pane <?php if ($tab == "2") {
                                                        echo 'active';
                                                    } ?>" id="tab2">
                                <div class="span12 well" style="padding: 1%; margin-left: 0">

                                    <?php if (!empty($veiculo)) { ?>
                                        <div class="widget-title" style="margin:-15px -10px 0">
                                            <h5><?php echo '<strong>' . $veiculo->nomeVeiculo . '</strong>';  ?> | Veiculo</h5>
                                        </div>
                                        <table class="table table-bordered" style="margin-left: 0;margin-top: 1rem; margin-bottom:20px">
                                            <thead>
                                                <tr>
                                                    <th><!-- coluna01   -->
                                                    </th>
                                                    <th>
                                                        <!-- coluna02   -->
                                                    </th>
                                                </tr>

                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="7" style="text-align: left; ">Observaçoes:<a style="color: green">&nbsp<?php echo $veiculo->observacoes; ?></a></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="7" style="text-align: left; ">Autonomia:<a style="color: green">&nbsp&nbsp<?php echo $veiculo->autonomia; ?></td>
                                                    <td colspan="7" style="text-align: left; ">Saldo Atual: &nbsp&nbsp<a style="color: green">&nbsp&nbsp<?php echo $veiculo->saldoAtualVeic . "Km"; ?></a></td>

                                                </tr>
                                                <tr>
                                                    <td colspan="7" style="text-align: left; ">Último abastecimento:<a style="color: green">&nbsp<?php echo $veiculo->abastecimentoKm . "Km"; ?></a>
                                                        &nbsp&nbsp no dia &nbsp&nbsp<a style="color: green"><?php echo $veiculo->ultimoAbastecimentoData; ?></a></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="7" style="text-align: left; ">Último Troca de óleo:<a style="color: green">&nbsp<?php echo $veiculo->ultimaTrocaOleoVeloc . "KmVeloc"; ?></a>
                                                        &nbsp&nbsp no dia &nbsp&nbsp<a style="color: green"><?php echo $veiculo->ultimaTrocaDeOleoData; ?></a></td>
                                                    <td colspan="7" style="text-align: left; ">Póxima Troca de óleo:<a style="color: green">&nbsp<?php echo $veiculo->oleoKmVeloc . "KmVeloc"; ?></a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    <?php } ?>
                                    <div class="widget-box">
                                        <div class="widget-title" style="margin: -20px 0 0">
                                            <span class="icon">
                                                <i class="fas fa-cash-register"></i>
                                            </span>
                                            <h5 style="padding: 3px 0"></h5>
                                        </div>
                                        <div class="widget-content nopadding tab-content">
                                            <table id="tabela2" class="table table-bordered ">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Data Abaste.</th>
                                                        <th>Data Óleo</th>
                                                        <th>Veloc. Óleo</th>
                                                        <th>Próx.Troca Óleo</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!--    <?php
                                                            if (!$gasolina) {
                                                                echo '<tr>   <td colspan="5">Nenhum Usuário Cadastrado</td>  </tr>';
                                                            }
                                                            foreach ($gasolina as $r) {

                                                                echo '<tr>';
                                                                echo '<td>' . $r->idGasolina . '</td>';
                                                                echo '<td>' . $r->dataLancamento . '</td>';
                                                                echo '<td><a style="color: green">' . $r->velocimetroEntrada . '</a></td>';
                                                                echo '<td><a style="color: green">' . $r->velocimetroSaida . '</a></td>';
                                                                echo '<td><a style="color: green">' . $r->saldoAtual . '</a></td>';
                                                                echo '</tr>';
                                                            } ?> -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>


                                </div>
                            </div>

                            <!--Lançamentos Gasolina  | tab03-->
                            <div class="tab-pane <?php if ($tab == "3") {
                                                        echo 'active';
                                                    } ?>" id="tab3">
                                <div class="span12 well" style="padding: 1%; margin-left: 0">

                                    <div class="widget-title" style="margin:-15px -10px 0">
                                        <h5> <?php echo '<strong>' . $veiculo->nomeVeiculo . '</strong>';  ?> | Lançamentos</h5>
                                    </div>

                                    <!-- modal1 -->
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#exampleModalCenter1">
                                        Entrada
                                    </button>
                                    <div class="modal fade" id="exampleModalCenter1" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLongTitle1">Entrada</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="<?php echo current_url(); ?>" id="formEntrada" method="post" class="form-horizontal">
                                                        <?php echo form_hidden('idveiculos', $veiculo->idVeiculos) ?>
                                                        <?php echo form_hidden('nomeVeiculo', $veiculo->nomeVeiculo) ?>
                                                        <?php echo form_hidden('oleoProxTroc', $veiculo->oleoKmVeloc) ?>
                                                        <?php echo form_hidden('saldoAtualVeic', $veiculo->saldoAtualVeic) ?>
                                                        <!-- <?php //echo form_hidden('dataLancamento', date('Y/m/d H:i:s')) 
                                                                ?> -->
                                                        <div class="control-group">
                                                            <label for="entrada" class="control-label">Entrada<span class="required">*</span></label>
                                                            <div class="controls">
                                                                <input id="entrada" type="text" name="entrada" placeholder="km atual do velocimetro" value="" />
                                                            </div>
                                                        </div>
                                                        <div class="form-actions">
                                                            <div class="span12">
                                                                <div class="span6 offset3" style="display:flex">
                                                                    <button type="submit" class="button btn btn-success">
                                                                        <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar</span></button>

                                                                </div>
                                                            </div>
                                                        </div>


                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">fechar</button>
                                                    <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- fim modal -->
                                    <!-- modal2 -->
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#exampleModalCenter2">
                                        Saida
                                    </button>
                                    <div class="modal fade" id="exampleModalCenter2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle2" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">

                                                    <h5 class="modal-title" id="exampleModalLongTitle2">Saida</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="<?php echo current_url(); ?>" id="formSaida" method="post" class="form-horizontal">
                                                        <?php echo form_hidden('idveiculos', $veiculo->idVeiculos) ?>
                                                        <?php echo form_hidden('nomeVeiculo', $veiculo->nomeVeiculo) ?>
                                                        <?php echo form_hidden('oleoProxTroc', $veiculo->oleoKmVeloc) ?>
                                                        <?php echo form_hidden('saldoAtualVeic', $veiculo->saldoAtualVeic) ?>
                                                        <!-- <?php //echo form_hidden('dataLancamento', date('Y/m/d')) 
                                                                ?> -->
                                                        <div class="control-group">
                                                            <label for="saida" class="control-label">Saida<span class="required">*</span></label>
                                                            <div class="controls">
                                                                <input id="saida" type="text" name="saida" placeholder="km atual do velocimetro" value="" />
                                                            </div>
                                                        </div>
                                                        <div class="form-actions">
                                                            <div class="span12">
                                                                <div class="span6 offset3" style="display:flex">
                                                                    <button type="submit" class="button btn btn-success">
                                                                        <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar</span></button>

                                                                </div>
                                                            </div>
                                                        </div>


                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                                    <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- fim modal -->
                                    <?php if ($veiculo->abastecer == 1) {    ?>
                                        <!-- modal3 -->
                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#exampleModalCenter3">
                                            Gasolina
                                        </button>
                                        <div class="modal fade" id="exampleModalCenter3" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">

                                                        <h5 class="modal-title" id="exampleModalLongTitle3">Gasolina</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="<?php echo current_url(); ?>" id="formGasolina" method="post" class="form-horizontal">
                                                            <?php echo form_hidden('idveiculos', $veiculo->idVeiculos) ?>
                                                            <?php echo form_hidden('nomeVeiculo', $veiculo->nomeVeiculo) ?>
                                                            <?php echo form_hidden('autonomia', $veiculo->autonomia) ?>
                                                            <?php echo form_hidden('saldoAtualVeic', $veiculo->saldoAtualVeic) ?>
                                                            <?php echo form_hidden('ultimoAbastecimentoData', date('Y/m/d')) ?>
                                                            <div class="control-group">
                                                                <label for="gasolina" class="control-label">Gasolina<span class="required">*</span></label>
                                                                <div class="controls">
                                                                    <input id="gasolina" type="text" name="gasolina" placeholder="gasolina em litros" value="" />
                                                                </div>
                                                            </div>
                                                            <div class="form-actions">
                                                                <div class="span12">
                                                                    <div class="span6 offset3" style="display:flex">
                                                                        <button type="submit" class="button btn btn-success">
                                                                            <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar</span></button>

                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </form>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- fim modal -->
                                    <?php }  ?>
                                    <?php if ($veiculo->trocarOleo == 1) {    ?>
                                        <!-- modal3 -->
                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#exampleModalCenter4">
                                            Óleo
                                        </button>
                                        <div class="modal fade" id="exampleModalCenter4" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">

                                                        <h5 class="modal-title" id="exampleModalLongTitle4">Óleo</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="<?php echo current_url(); ?>" id="formOleo" method="post" class="form-horizontal">
                                                            <?php echo form_hidden('idveiculos', $veiculo->idVeiculos) ?>
                                                            <?php echo form_hidden('nomeVeiculo', $veiculo->nomeVeiculo) ?>
                                                            <?php echo form_hidden('oleoKmVeloc', $veiculo->oleoKmVeloc) ?>
                                                            <?php echo form_hidden('ultimaTrocaDeOleoData', date('Y/m/d')) ?>
                                                            <div class="control-group">
                                                                <label for="oleo" class="control-label">Velocimetro<span class="required">*</span></label>
                                                                <div class="controls">
                                                                    <input id="oleo" type="text" name="oleo" placeholder="km atual do velocimetro" value="" />
                                                                </div>
                                                            </div>
                                                            <div class="control-group">
                                                                <label for="oleoKm" class="control-label">Óleo Km<span class="required">*</span></label>
                                                                <div class="controls">
                                                                    <input id="oleoKm" type="text" name="oleoKm" placeholder="km do óleo" value="" />
                                                                </div>
                                                            </div>
                                                            <div class="form-actions">
                                                                <div class="span12">
                                                                    <div class="span6 offset3" style="display:flex">
                                                                        <button type="submit" class="button btn btn-success">
                                                                            <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar</span></button>

                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </form>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- fim modal -->
                                    <?php }  ?>

                                    <div class="widget-box">
                                        <div class="widget-title" style="margin: -20px 0 0">
                                            <span class="icon">
                                                <i class="fas fa-cash-register"></i>
                                            </span>
                                            <h5 style="padding: 3px 0"></h5>
                                        </div>
                                        <div class="widget-content nopadding tab-content">
                                            <table id="tabela" class="table table-bordered ">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Data</th>
                                                        <th>Entrada</th>
                                                        <th>Saida</th>
                                                        <th>Saldo Atual</th>
                                                        <th>Km Rodado</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if (!$gasolina) {
                                                        echo '<tr>   <td colspan="5">Nenhum Usuário Cadastrado</td>  </tr>';
                                                    }
                                                    foreach ($gasolina as $r) {

                                                        echo '<tr>';
                                                        echo '<td>' . $r->idGasolina . '</td>';
                                                        echo '<td>' . $r->dataLancamento . '</td>';
                                                        echo '<td><a style="color: green">' . $r->velocimetroEntrada . '</a></td>';
                                                        echo '<td><a style="color: green">' . $r->velocimetroSaida . '</a></td>';
                                                        echo '<td><a style="color: green">' . $r->saldoAtual . '</a></td>';
                                                        echo '<td><a style="color: red">' . (int)$r->velocimetroSaida - (int)$r->velocimetroEntrada . '</a></td>';
                                                        echo '</tr>';
                                                    } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        <?php } ?>

                    </div>
                </div>
            </div>
            &nbsp
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>

<script type="text/javascript">
    function calcDesconto(valor, desconto, tipoDesconto) {
        var resultado = 0;
        if (tipoDesconto == 'real') {
            resultado = valor - desconto;
        }
        if (tipoDesconto == 'porcento') {
            resultado = (valor - desconto * valor / 100).toFixed(2);
        }
        return resultado;
    }

    function validarDesconto(resultado, valor) {
        if (resultado == valor) {
            return resultado = "";
        } else {
            return resultado.toFixed(2);
        }
    }
    var valorBackup = $("#valorTotal").val();

    $("#quantidade").keyup(function() {
        this.value = this.value.replace(/[^0-9.]/g, '');
    });

    $("#quantidade_servico").keyup(function() {
        this.value = this.value.replace(/[^0-9.]/g, '');
    });
    $('#tipoDesconto').on('change', function() {
        if (Number($("#desconto").val()) >= 0) {
            $('#resultado').val(calcDesconto(Number($("#valorTotal").val()), Number($("#desconto").val()), $("#tipoDesconto").val()));
            $('#resultado').val(validarDesconto(Number($('#resultado').val()), Number($("#valorTotal").val())));
        }
    });
    $("#desconto").keyup(function() {
        this.value = this.value.replace(/[^0-9.]/g, '');
        if ($("#valorTotal").val() == null || $("#valorTotal").val() == '') {
            $('#errorAlert').text('Valor não pode ser apagado.').css("display", "inline").fadeOut(5000);
            $('#desconto').val('');
            $('#resultado').val('');
            $("#valorTotal").val(valorBackup);
            $("#desconto").focus();

        } else if (Number($("#desconto").val()) >= 0) {
            $('#resultado').val(calcDesconto(Number($("#valorTotal").val()), Number($("#desconto").val()), $("#tipoDesconto").val()));
            $('#resultado').val(validarDesconto(Number($('#resultado').val()), Number($("#valorTotal").val())));
        } else {
            $('#errorAlert').text('Erro desconhecido.').css("display", "inline").fadeOut(5000);
            $('#desconto').val('');
            $('#resultado').val('');
        }
    });

    $("#valorTotal").focusout(function() {
        $("#valorTotal").val(valorBackup);
        if ($("#valorTotal").val() == '0.00' && $('#resultado').val() != '') {
            $('#errorAlert').text('Você não pode apagar o valor.').css("display", "inline").fadeOut(6000);
            $('#resultado').val('');
            $("#valorTotal").val(valorBackup);
            $('#resultado').val(calcDesconto(Number($("#valorTotal").val()), Number($("#desconto").val())));
            $('#resultado').val(validarDesconto(Number($('#resultado').val()), Number($("#valorTotal").val())));
            $("#desconto").focus();
        } else {
            $('#resultado').val(calcDesconto(Number($("#valorTotal").val()), Number($("#desconto").val())));
            $('#resultado').val(validarDesconto(Number($('#resultado').val()), Number($("#valorTotal").val())));
        }
    });

    $('#resultado').focusout(function() {
        if (Number($('#resultado').val()) > Number($("#valorTotal").val())) {
            $('#errorAlert').text('Desconto não pode ser maior que o Valor.').css("display", "inline").fadeOut(6000);
            $('#resultado').val('');
        }
        if ($("#desconto").val() != "" || $("#desconto").val() != null) {
            $('#resultado').val(calcDesconto(Number($("#valorTotal").val()), Number($("#desconto").val())));
            $('#resultado').val(validarDesconto(Number($('#resultado').val()), Number($("#valorTotal").val())));
        }
    });

    $(document).ready(function() {

        $(".money").maskMoney();

        $('#recebido').click(function(event) {
            var flag = $(this).is(':checked');
            if (flag == true) {
                $('#divRecebimento').show();
            } else {
                $('#divRecebimento').hide();
            }
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
                            window.location.href = window.BaseUrl + 'index.php/os/editar/' + <?php //echo $result->idOs 
                                                                                                ?>;
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

        $("#formwhatsapp").validate({
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
                $('#btn-cancelar-faturar').trigger('click');
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
                                text: "Ocorreu um erro ao tentar  OS."
                            });
                            $('#progress-fatura').hide();
                        }
                    }
                });

                return false;
            }
        });

        $("#produto").autocomplete({
            source: "<?php echo base_url(); ?>index.php/os/autoCompleteProduto",
            minLength: 2,
            select: function(event, ui) {
                $("#codDeBarra").val(ui.item.codbar);
                $("#idProduto").val(ui.item.id);
                $("#estoque").val(ui.item.estoque);
                $("#preco").val(ui.item.preco);
                $("#quantidade").focus();
            }
        });

        $("#servico").autocomplete({
            source: "<?php echo base_url(); ?>index.php/os/autoCompleteServico",
            minLength: 2,
            select: function(event, ui) {
                $("#idServico").val(ui.item.id);
                $("#preco_servico").val(ui.item.preco);
                $("#quantidade_servico").focus();
            }
        });


        $("#cliente").autocomplete({
            source: "<?php echo base_url(); ?>index.php/os/autoCompleteCliente",
            minLength: 2,
            select: function(event, ui) {
                $("#clientes_id").val(ui.item.id);
            }
        });

        $("#tecnico").autocomplete({
            source: "<?php echo base_url(); ?>index.php/os/autoCompleteUsuario",
            minLength: 2,
            select: function(event, ui) {
                $("#usuarios_id").val(ui.item.id);
            }
        });

        $("#termoGarantia").autocomplete({
            source: "<?php echo base_url(); ?>index.php/os/autoCompleteTermoGarantia",
            minLength: 1,
            select: function(event, ui) {
                if (ui.item.id) {
                    $("#garantias_id").val(ui.item.id);
                }
            }
        });
        $("#equipamentos").autocomplete({
            source: "<?php echo base_url(); ?>index.php/os/autoCompleteEquipamentos",
            minLength: 1,
            select: function(event, ui) {

                addEquipamentosAutocomplete(i, ui.item);

            },
            open: function(event, ui) {
                //$('.ui-autocomplete').append('<li><a href="javascript:alert(\'redirecting...\')">See All Result</a></li>'); //See all results
                $('.ui-autocomplete').append('<li><a href="javascript:abreModal()" ><button type="button" class="btn btn-primary"><span class="button__icon"><i class="bx bx-plus-circle"></i></span><span class="button__text2">Equipamentos</button></a></li > ');
            }
        });

        $('#termoGarantia').on('change', function() {
            if (!$(this).val() && $("#garantias_id").val()) {
                $("#garantias_id").val('');
                Swal.fire({
                    type: "success",
                    title: "Sucesso",
                    text: "Termo de garantia removido"
                });
            }
        });

        $("#formOs").validate({
            rules: {
                cliente: {
                    required: true
                },
                tecnico: {
                    required: true
                },
                dataInicial: {
                    required: true
                }
            },
            messages: {
                cliente: {
                    required: 'Campo Requerido.'
                },
                tecnico: {
                    required: 'Campo Requerido.'
                },
                dataInicial: {
                    required: 'Campo Requerido.'
                }
            },
            errorClass: "help-inline",
            errorElement: "span",
            highlight: function(element, errorClass, validClass) {
                $(element).parents('.control-group').addClass('error');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).parents('.control-group').removeClass('error');
                $(element).parents('.control-group').addClass('success');
            }
        });

        $("#formProdutos").validate({
            rules: {
                preco: {
                    required: true
                },
                quantidade: {
                    required: true
                }
            },
            messages: {
                preco: {
                    required: 'Inserir o preço'
                },
                quantidade: {
                    required: 'Insira a quantidade'
                }
            },
            submitHandler: function(form) {
                var quantidade = parseInt($("#quantidade").val());
                var estoque = parseInt($("#estoque").val());

                <?php if (!$configuration['control_estoque']) {
                    echo 'estoque = 1000000';
                }; ?>

                if (estoque < quantidade) {
                    Swal.fire({
                        type: "error",
                        title: "Atenção",
                        text: "Você não possui estoque suficiente."
                    });
                } else {
                    var dados = $(form).serialize();
                    $("#divProdutos").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");
                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url(); ?>index.php/os/adicionarProduto",
                        data: dados,
                        dataType: 'json',
                        success: function(data) {
                            if (data.result == true) {
                                $("#divProdutos").load("<?php echo current_url(); ?> #divProdutos");
                                $("#quantidade").val('');
                                $("#preco").val('');
                                $("#resultado").val('');
                                $("#desconto").val('');
                                $("#divValorTotal").load("<?php echo current_url(); ?> #divValorTotal");
                                $("#produto").val('').focus();
                            } else {
                                Swal.fire({
                                    type: "error",
                                    title: "Atenção",
                                    text: "Ocorreu um erro ao tentar adicionar produto."
                                });
                            }
                        }
                    });
                    return false;
                }
            }
        });

        $("#formServicos").validate({
            rules: {
                servico: {
                    required: true
                },
                preco: {
                    required: true
                },
                quantidade: {
                    required: true
                },
            },
            messages: {
                servico: {
                    required: 'Insira um serviço'
                },
                preco: {
                    required: 'Insira o preço'
                },
                quantidade: {
                    required: 'Insira a quantidade'
                },
            },
            submitHandler: function(form) {
                var dados = $(form).serialize();

                $("#divServicos").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>index.php/os/adicionarServico",
                    data: dados,
                    dataType: 'json',
                    success: function(data) {
                        if (data.result == true) {
                            $("#divServicos").load("<?php echo current_url(); ?> #divServicos");
                            $("#quantidade_servico").val('');
                            $("#preco_servico").val('');
                            $("#resultado").val('');
                            $("#desconto").val('');
                            $("#divValorTotal").load("<?php echo current_url(); ?> #divValorTotal");
                            $("#servico").val('').focus();
                        } else {
                            Swal.fire({
                                type: "error",
                                title: "Atenção",
                                text: "Ocorreu um erro ao tentar adicionar serviço."
                            });
                        }
                    }
                });
                return false;
            }
        });

        $("#formAnotacao").validate({
            rules: {
                anotacao: {
                    required: true
                }
            },
            messages: {
                anotacao: {
                    required: 'Insira a anotação'
                }
            },
            submitHandler: function(form) {
                var dados = $(form).serialize();
                $("#divFormAnotacoes").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");

                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>index.php/os/adicionarAnotacao",
                    data: dados,
                    dataType: 'json',
                    success: function(data) {
                        if (data.result == true) {
                            $("#divAnotacoes").load("<?php echo current_url(); ?> #divAnotacoes");
                            $("#anotacao").val('');
                            $('#btn-close-anotacao').trigger('click');
                            $("#divFormAnotacoes").html('');
                        } else {
                            Swal.fire({
                                type: "error",
                                title: "Atenção",
                                text: "Ocorreu um erro ao tentar adicionar anotação."
                            });
                        }
                    }
                });
                return false;
            }
        });

        $("#formAnexos").validate({
            submitHandler: function(form) {
                //var dados = $( form ).serialize();
                var dados = new FormData(form);
                $("#form-anexos").hide('1000');
                $("#divAnexos").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>index.php/os/anexar",
                    data: dados,
                    mimeType: "multipart/form-data",
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: 'json',
                    success: function(data) {
                        if (data.result == true) {
                            $("#divAnexos").load("<?php echo current_url(); ?> #divAnexos");
                            $("#userfile").val('');

                        } else {
                            $("#divAnexos").html('<div class="alert fade in"><button type="button" class="close" data-dismiss="alert">×</button><strong>Atenção!</strong> ' + data.mensagem + '</div>');
                        }
                    },
                    error: function() {
                        $("#divAnexos").html('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert">×</button><strong>Atenção!</strong> Ocorreu um erro. Verifique se você anexou o(s) arquivo(s).</div>');
                    }
                });
                $("#form-anexos").show('1000');
                return false;
            }
        });

        $(document).on('click', 'a', function(event) {
            var idProduto = $(this).attr('idAcao');
            var quantidade = $(this).attr('quantAcao');
            var produto = $(this).attr('prodAcao');
            var idOS = "<?php //echo $result->idOs 
                        ?>"
            if ((idProduto % 1) == 0) {
                $("#divProdutos").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>index.php/os/excluirProduto",
                    data: "idProduto=" + idProduto + "&quantidade=" + quantidade + "&produto=" + produto + "&idOs=" + idOS,
                    dataType: 'json',
                    success: function(data) {
                        if (data.result == true) {
                            $("#divProdutos").load("<?php echo current_url(); ?> #divProdutos");
                            $("#divValorTotal").load("<?php echo current_url(); ?> #divValorTotal");
                            $("#resultado").val('');
                            $("#desconto").val('');

                        } else {
                            Swal.fire({
                                type: "error",
                                title: "Atenção",
                                text: "Ocorreu um erro ao tentar excluir produto."
                            });
                        }
                    }
                });
                return false;
            }

        });

        $(document).on('click', '.servico', function(event) {
            var idServico = $(this).attr('idAcao');
            var idOS = "<?php //echo $result->idOs 
                        ?>"
            if ((idServico % 1) == 0) {
                $("#divServicos").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>index.php/os/excluirServico",
                    data: "idServico=" + idServico + "&idOs=" + idOS,
                    data: "idServico=" + idServico,
                    dataType: 'json',
                    success: function(data) {
                        if (data.result == true) {
                            $("#divServicos").load("<?php echo current_url(); ?> #divServicos");
                            $("#divValorTotal").load("<?php echo current_url(); ?> #divValorTotal");
                            $("#resultado").val('');
                            $("#desconto").val('');

                        } else {
                            Swal.fire({
                                type: "error",
                                title: "Atenção",
                                text: "Ocorreu um erro ao tentar excluir serviço."
                            });
                        }
                    }
                });
                return false;
            }
        });

        $(document).on('click', '.anexo', function(event) {
            event.preventDefault();
            var link = $(this).attr('link');
            var id = $(this).attr('imagem');
            var url = '<?php echo base_url(); ?>index.php/os/excluirAnexo/';
            $("#div-visualizar-anexo").html('<img src="' + link + '" alt="">');
            $("#excluir-anexo").attr('link', url + id);

            $("#download").attr('href', "<?php echo base_url(); ?>index.php/os/downloadanexo/" + id);

        });

        $(document).on('click', '#excluir-anexo', function(event) {
            event.preventDefault();
            var link = $(this).attr('link');
            var idOS = "<?php //echo $result->idOs 
                        ?>"
            $('#modal-anexo').modal('hide');
            $("#divAnexos").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");

            $.ajax({
                type: "POST",
                url: link,
                dataType: 'json',
                data: "idOs=" + idOS,
                success: function(data) {
                    if (data.result == true) {
                        $("#divAnexos").load("<?php echo current_url(); ?> #divAnexos");
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

        $(document).on('click', '.anotacao', function(event) {
            var idAnotacao = $(this).attr('idAcao');
            var idOS = "<?php //echo $result->idOs 
                        ?>"
            if ((idAnotacao % 1) == 0) {
                $("#divAnotacoes").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>index.php/os/excluirAnotacao",
                    data: "idAnotacao=" + idAnotacao + "&idOs=" + idOS,
                    dataType: 'json',
                    success: function(data) {
                        if (data.result == true) {
                            $("#divAnotacoes").load("<?php echo current_url(); ?> #divAnotacoes");

                        } else {
                            Swal.fire({
                                type: "error",
                                title: "Atenção",
                                text: "Ocorreu um erro ao tentar excluir Anotação."
                            });
                        }
                    }
                });
                return false;
            }
        });

        $(".datepicker").datepicker({
            dateFormat: 'dd/mm/yy'
        });

        $('.editor').trumbowyg({
            lang: 'pt_br'
        });
    });

    //limpa formulario entrada
    /*  $(document).on('submit', '#formEntrada', function() {
         $("input").val("");
         $("textarea").val("");
     }); */


    $(document).ready(function() {
        $('#addEquipamentos').hide();
        $('#assinarPadNameCLIENTE').hide();
        $('#conteudotd').hide();

    });

    function abreModal() {
        $("#modal-adicionaEquipamentos").modal({
            show: true
        });
    }
</script>