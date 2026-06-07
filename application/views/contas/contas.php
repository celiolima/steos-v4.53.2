<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>

<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url() ?>assets/trumbowyg/ui/trumbowyg.css">
<script type="text/javascript" src="<?php echo base_url() ?>assets/trumbowyg/trumbowyg.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/trumbowyg/langs/pt_br.js"></script>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css" />

<?php
if (!empty($reload)) {
    // echo "reload";
    // exit;
    header('Refresh:0');
}
?>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-diagnoses"></i>
                </span>
                <h5>Contas</h5>
                <!-- Botões -->
                <div class="buttons">

                    <a href="#modal-faturar" id="btn-faturar" role="button" data-toggle="modal" class="button btn btn-mini btn-success">
                        <span class="button__icon"><i class='bx bx-plus'></i></span> <span class="button__text">Contas</span></a>
                </div>
            </div>
            <div class="widget-content nopadding tab-content">
                <div class="span12" id="divProdutosServicos" style=" margin-left: 0">
                    <!--  Tabs -->
                    <ul class="nav nav-tabs">
                        <li <?php if ($tab == "1") {
                                echo 'class="active" ';
                            } ?> id="tabDetalhes"><a href="#tab1" data-toggle="tab">Todas as Contas</a></li>
                        <?php if ($tab == "2" || $tab == "3") { ?>
                            <li <?php if ($tab == "2") {
                                    echo 'class="active" ';
                                } ?> id="tabDesconto"><a href="#tab2" data-toggle="tab">Conta</a></li>
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
                                                    <th>Conta</th>
                                                    <th>Banco</th>
                                                    <th>agencia</th>
                                                    <th>numero</th>
                                                    <th>tipo</th>
                                                    <th>Saldo</th>
                                                    <th>Vencimento Cartão</th>
                                                    <th>Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php

                                                if (!empty($contas)) {
                                                    foreach ($contas as $r) {
                                                        /* echo "<pre>";
                                                        print_r($r->idContas);
                                                        exit; */
                                                        if ($r->status) {
                                                            echo '<tr>';
                                                            echo '<input type="hidden">' . $r->idContas . '</input>';
                                                            echo '<td>' . $r->conta . '</td>';
                                                            echo '<td>' . $r->banco . '</td>';
                                                            echo '<td>' . $r->agencia . '</td>';
                                                            echo '<td>' . $r->numero . '</td>';
                                                            echo '<td>' . $r->tipo . '</td>';
                                                            echo '<td>' . $r->saldo . '</td>';
                                                            echo '<td>' . $r->vencimento_cartao . '</td>';
                                                            echo '<td>';
                                                            /*  if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto')) {
                                                                echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/veiculos/visualizar/' . $r->idVeiculos . '" class="btn-nwe" title="Visualizar veiculo"><i class="bx bx-show bx-xs"></i></a>  ';
                                                            } */
                                                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eProduto')) {
                                                                echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/contas/conta/' . $r->idContas . '" class="btn-nwe3" title="Ir para Conta"><i class="bx bxs-dollar-circle" style="color:#2945bb" ></i></a>';
                                                            }
                                                            /*  if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eServico')) {
                                                                echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/contas/editar/' . $r->idContas . '" class="btn-nwe3" title="Editar Conta"><i class="bx bx-edit bx-xs"></i></a>';
                                                            } */
                                                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dServico')) {
                                                                echo '<a href="#modal-excluir" role="button" data-toggle="modal" Conta="' . $r->idContas . '" class="btn-nwe4" title="Excluir Conta"><i class="bx bx-trash-alt bx-xs"></i></a>  ';
                                                            }
                                                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eProduto')) {
                                                                echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/contas/conta/' . $r->idContas . '" class="btn-nwe3" title="lançamento na Conta"><i class="bx bx-transfer" style="color:#4a9e58"  ></i></a>';
                                                            }
                                                            /*  if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dProduto')) {
                                                            echo '<a style="margin-right: 1%" href="#modal-excluir" role="button" data-toggle="modal" produto="' . $r->idVeiculos . '" class="btn-nwe4" title="Excluir Produto"><i class="bx bx-trash-alt bx-xs"></i></a>';
                                                        } */

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

                        <?php if ($tab == "2" || $tab == "3") { ?>
                            <!--veiculo detalhes  | tab02-->
                            <div class="tab-pane <?php if ($tab == "2") {
                                                        echo 'active';
                                                    } ?>" id="tab2">
                                <div class="span12 well" style="padding: 1%; margin-left: 0">
                                    <?php if (!empty($conta)) { ?>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th><!-- coluna01   -->
                                                        <h5><?php echo '<strong>' . $conta->conta . '</strong>';  ?> | conta</h5>
                                                    </th>
                                                    <th>
                                                        <!-- coluna02   -->
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="7" style="text-align: left; ">TIPO DE CONTA:<a style="color: green">&nbsp<?php echo $conta->tipo; ?></a></td>
                                                    <td colspan="7" style="text-align: left; ">BANCO:<a style="color: green">&nbsp<?php echo $conta->banco; ?></a></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="7" style="text-align: left; ">AGENCIA:<a style="color: green">&nbsp&nbsp<?php echo $conta->agencia; ?></td>
                                                    <td colspan="7" style="text-align: left; ">NUMERO: &nbsp&nbsp<a style="color: green">&nbsp&nbsp<?php echo $conta->numero; ?></a></td>

                                                </tr>
                                                <tr>
                                                    <td colspan="7" style="text-align: left; ">SALDO:<a style="color: green">&nbsp<?php echo "R$" . $conta->saldo; ?></a>
                                                    <td colspan="7" style="text-align: left; ">VENCIMENTO CARTÃO:<a style="color: green">&nbsp<?php echo $conta->vencimento_cartao; ?></a>
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
                                            <div class="widget-title" style="margin:-15px -10px 0">
                                                <h5>Lançamentos</h5>
                                            </div>
                                            <!-- botão entrada-->
                                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#exampleModalCenter1">
                                                Entrada
                                            </button>
                                            <!-- botão saida-->
                                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#exampleModalCenter2">
                                                Saida
                                            </button>
                                            <!-- botão transferecia-->
                                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#exampleModalCenter3">
                                                Trasferência
                                            </button>
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
                                                                <th>DATA</th>
                                                                <th>TIPO</th>
                                                                <th>LANÇAMENTO</th>
                                                                <th>ULTIMO SALDO</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            if (!$lancamentos_conta) {
                                                                echo '<tr>   <td colspan="5">Nenhum Usuário Cadastrado</td>  </tr>';
                                                            }
                                                            foreach ($lancamentos_conta as $r) {

                                                                echo '<tr>';
                                                                echo '<td>' . $r->idlancamentos_contas . '</td>';
                                                                echo '<td>' . $r->dataLancamento . '</td>';

                                                                if ($r->tipo_lacamento == 'ENTRADA') {
                                                                    echo '<td><a style="color: green">' . $r->tipo_lacamento . '</td>';
                                                                    echo '<td><a style="color: green">' . $r->lancamento . '</a></td>';
                                                                } else if ($r->tipo_lacamento == 'VEND/SER') {
                                                                    echo '<td><a style="color: green">' . $r->tipo_lacamento . '</td>';
                                                                    echo '<td><a style="color: green">' . $r->lancamento . '</a></td>';
                                                                } else if ($r->tipo_lacamento == 'DEPOSITO') {
                                                                    echo '<td><a style="color: green">' . $r->tipo_lacamento . '</td>';
                                                                    echo '<td><a style="color: green">' . $r->lancamento . '</a></td>';
                                                                } else if ($r->tipo_lacamento == 'SAIDA') {
                                                                    echo '<td><a style="color: red">' . $r->tipo_lacamento . '</td>';
                                                                    echo '<td><a style="color: red">' . $r->lancamento . '</a></td>';
                                                                } else if ($r->tipo_lacamento == 'PAGAMENTO') {
                                                                    echo '<td><a style="color: red">' . $r->tipo_lacamento . '</td>';
                                                                    echo '<td><a style="color: red">' . $r->lancamento . '</a></td>';
                                                                } else if ($r->tipo_lacamento == 'TRANSFERENCIA') {
                                                                    echo '<td><a style="color: blue">' . $r->tipo_lacamento . '</td>';
                                                                    echo '<td><a style="color: blue">' . $r->lancamento . '</a></td>';
                                                                }
                                                                if ((int)$r->saldo > 0) {
                                                                    echo '<td><a style="color: green">' . $r->saldo . '</a></td>';
                                                                } else {
                                                                    echo '<td><a style="color: red">' . $r->saldo . '</a></td>';
                                                                }
                                                                echo '</tr>';
                                                            } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

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

<!-- Modal adicionar Conta-->
<div id="modal-faturar" class="modal hide fade " tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <form id="formContasAdicionar" action="<?php echo current_url() ?>" method="post">
        <!-- <form id="formContasAdicionar" action="<?php //echo base_url(); 
                                                    ?>index.php/contas/adicionar" method="post"> -->
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">Adicionar Conta</h3>
        </div>
        <div class="modal-body">
            <!-- <div class="span12 alert alert-info" style="margin-left: 0"> Obrigatório o preenchimento dos campos com asterisco.</div> -->
            <div class="span12" style="margin-left: 0">
                <div class="span6" style="margin-left: 0">
                    <label for="conta">Conta Nome</label>
                    <input class="span12" id="conta" type="text" name="conta" value="" />
                </div>
                <div class="span6" style="margin-left: 2">
                    <label for="banco">Banco</label>
                    <select class="span12" name="banco" id="banco" value="">
                        <?php foreach ($bancos as $r) {
                            echo '<option value="' . $r->nome . '">' . $r->nome . '</option>';
                        } ?>
                    </select>
                </div>
            </div>
            <div class="span12" style="margin-left: 0">
                <div class="span4" style="margin-left: 0">
                    <label for="tipo">Tipo</label>
                    <select class="span12" name="tipo" id="tipo" value="">
                        <option value="caixa">CAIXA</option>
                        <option value="cartao">CARTÃO</option>
                        <option value="banco">BANCO</option>
                    </select>
                </div>
                <div class="span2" style="margin-left: 1">
                    <label>Vencimento</label>
                    <input class="span12" id="vencimento_cartao" type="text" name="vencimento_cartao" value="" />
                </div>
                <div class="span6" style="margin-left: 2">
                    <label>Agência</label>
                    <input class="span12" id="agencia" type="text" name="agencia" value="" />
                </div>
            </div>

            <div class="span12" style="margin-left: 0">
                <div class="span6" style="margin-left: 0">
                    <label for="saldo">Saldo*</label>
                    <!-- <input type="hidden" id="tipo" name="tipo" value="receita" /> -->
                    <input class="span12 money" id="saldo" type="text" data-affixes-stay="true" data-thousands="" data-decimal="." name="saldo" value="" />
                </div>
                <div class="span3" style="margin-left: 2;">
                    <label>Agêcia Número</label>
                    <input class="span12" id="numero" type="text" name="numero" value="" />

                </div>
            </div>

        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true" id="btn-cancelar-faturar"><span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span></button>
            <button class="button btn btn-success"><span class="button__icon"><i class='bx bx-dollar'></i></span> <span class="button__text2">Cadastrar</span></button>
        </div>
    </form>
</div>
<!-- fim modal -->

<!-- Modal excluir Conta-->
<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="<?php echo base_url() ?>index.php/contas/excluir" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel">Excluir Conta</h5>
        </div>
        <div class="modal-body">
            <input type="hidden" id="idConta" name="id" value="" />
            <h5 style="text-align: center">Deseja realmente excluir esta conta?</h5>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true"><span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span></button>
            <button class="button btn btn-danger"><span class="button__icon"><i class='bx bx-trash'></i></span> <span class="button__text2">Excluir</span></button>
        </div>
    </form>
</div>
<!-- fim modal -->

<!-- modal saida -->
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
                    <?php if (isset($conta->idContas)) {
                        echo form_hidden('idContas', $conta->idContas);
                    } ?>
                    <?php if (isset($conta->conta)) {
                        echo form_hidden('conta', $conta->conta);
                    } ?>
                    <?php if (isset($conta->saldo)) {
                        echo form_hidden('saldo', $conta->saldo);
                    } ?>
                    <?php //echo form_hidden('dataLancamento', date('Y/m/d')) 
                    ?>
                    <div class="control-group">
                        <label for="saida" class="control-label">Saida<span class="required">*</span></label>
                        <div class="controls">
                            <input class="span12 money" id="saida" type="text" placeholder="valor da saída" data-affixes-stay="true" data-thousands="" data-decimal="." name="saida" value="" />
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

<!-- modal1 -->
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
                    <?php if (isset($conta->idContas)) {
                        echo form_hidden('idContas', $conta->idContas);
                    } ?>
                    <?php if (isset($conta->conta)) {
                        echo form_hidden('conta', $conta->conta);
                    } ?>
                    <?php if (isset($conta->saldo)) {
                        echo form_hidden('saldo', $conta->saldo);
                    } ?>
                    <?php //echo form_hidden('dataLancamento', date('Y/m/d H:i:s')) 
                    ?>
                    <div class="control-group">
                        <label for="entrada" class="control-label">Entrada<span class="required">*</span></label>
                        <div class="controls">
                            <input class="span12 money" id="entrada" type="text" placeholder="valor da entrada" data-affixes-stay="true" data-thousands="" data-decimal="." name="entrada" value="" />
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

<!-- modal trasferencia -->
<div class="modal fade" id="exampleModalCenter3" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">

                <h5 class="modal-title" id="exampleModalLongTitle3">Trasferência</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?php echo current_url(); ?>" id="formTrasferencia" method="post" class="form-horizontal">
                    <?php if (isset($conta->idContas)) {
                        echo form_hidden('idContas', $conta->idContas);
                    } ?>
                    <?php if (isset($conta->conta)) {
                        echo form_hidden('conta', $conta->conta);
                    } ?>
                    <?php if (isset($conta->saldo)) {
                        echo form_hidden('saldo', $conta->saldo);
                    } ?>

                    <div class="span12" style="margin: 10px 10px 10px 10px">
                        <div class="span6">
                            <label for="trasferencia">Valor a Trasferir<span class="required">*</span></label>
                            <input class="span12 money" id="trasferencia" type="text" placeholder="valor a tranferir" data-affixes-stay="true" data-thousands="" data-decimal="." name="trasferencia" value="" />
                        </div>
                        <div class="span6">
                            <label>Contas</label>
                            <select name="contasTrans" id="contasTrans" value="">
                                <!--   <option value="">Todas as Contas</option> -->
                                <?php foreach ($contas as $r) {
                                    echo '<option value="' . $r->idContas . '">' . $r->conta . '</option>';
                                } ?>
                            </select>
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




<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', 'a', function(event) {
            var conta = $(this).attr('Conta');
            $('#idConta').val(conta);
        });
    });

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

        $("#formContasAdicionar").validate({
            rules: {
                conta: {
                    required: true
                },
                banco: {
                    required: true
                },
                tipo: {
                    required: true
                },
                saldo: {
                    required: true
                }
            },
            messages: {
                conta: {
                    required: 'Campo Requerido.'
                },
                banco: {
                    required: 'Campo Requerido.'
                },
                tipo: {
                    required: 'Campo Requerido.'
                },
                saldo: {
                    required: 'Campo Requerido.'
                }
            },
            submitHandler: function(form) {
                var dados = $(form).serialize();
                $('#btn-cancelar-faturar').trigger('click');
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>index.php/contas/adicionar",
                    data: dados,
                    dataType: 'json',
                    success: function(data) {
                        //alert('ok')
                        if (data.result == true) {
                            window.location.reload(true);
                        } else {
                            Swal.fire({
                                type: "error",
                                title: "Atenção",
                                text: "Ocorreu um erro ao adicionar Conta."
                            });
                            $('#progress-fatura').hide();
                        }
                    }
                });

                //return false;

            }
        });


    });
</script>