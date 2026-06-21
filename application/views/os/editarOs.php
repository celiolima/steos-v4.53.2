<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/datetimepicker/jquery.datetimepicker.css" />
<script src="<?php echo base_url() ?>assets/datetimepicker/jquery.js"></script>
<script src="<?php echo base_url() ?>assets/datetimepicker/build/jquery.datetimepicker.full.min.js"></script>


<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>

<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url() ?>assets/trumbowyg/ui/trumbowyg.css">
<script type="text/javascript" src="<?php echo base_url() ?>assets/trumbowyg/trumbowyg.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/trumbowyg/langs/pt_br.js"></script>
<!-- jQuery UI Signature core CSS -->
<link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/south-street/jquery-ui.css" rel="stylesheet">
<link href="<?php echo base_url() ?>assets/assinaturas/css/jquery.signature.css" rel="stylesheet">

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css" />
<style>
    .kbw-signature {
        width: 450px;
        height: 90px;
    }
</style>
<?php
$despesa = 0;
if (!empty($lancamentos)) {
    foreach ($lancamentos as $r) {

        if ($r->tipo == 'despesa') {
            $despesa = 1;
        }
    }
}
?>
<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <!--  Botões -->
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-diagnoses"></i>
                </span>
                <h5>Editar Ordem de Serviço</h5>
                <!-- Botões -->
                <div class="buttons">
                    <?php if ($result->faturado == 0) { ?>
                        <a href="#modal-faturar" id="btn-faturar" role="button" data-toggle="modal" class="button btn btn-mini btn-danger">
                            <span class="button__icon"><i class='bx bx-dollar'></i></span> <span class="button__text">Faturar</span></a>
                    <?php
                    } ?>
                    <a title="Visualizar OS" class="button btn btn-primary" href="<?php echo site_url() ?>/os/visualizar/<?php echo $result->idOs; ?>">
                        <span class="button__icon"><i class="bx bx-show"></i></span><span class="button__text">Visualizar OS</span></a>
                    <a target="_blank" title="Imprimir OS Papel A4" class="button btn btn-mini btn-inverse" href="<?php echo site_url() ?>/os/imprimir/<?php echo $result->idOs; ?>">
                        <span class="button__icon"><i class="bx bx-printer"></i></span> <span class="button__text">Papel A4</span></a>

                    <a target="_blank" title="Imprimir OS Cupom Não Fiscal" class="button btn btn-mini btn-inverse" href="<?php echo site_url() ?>/os/imprimirTermica/<?php echo $result->idOs; ?>">
                        <span class="button__icon"><i class="bx bx-printer"></i></span> <span class="button__text">CP Não Fiscal</span></a>
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) {
                        $this->load->model('os_model');
                        $zapnumber = preg_replace("/[^0-9]/", "", $result->celular_cliente);
                        $troca = [$result->nomeCliente, $result->idOs, $result->status, 'R$ ' . ($result->desconto != 0 && $result->valor_desconto != 0 ? number_format($result->valor_desconto, 2, ',', '.') : number_format($totalProdutos + $totalServico, 2, ',', '.')), strip_tags($result->descricaoProduto), ($emitente ? $emitente->nome : ''), ($emitente ? $emitente->telefone : ''), strip_tags($result->observacoes), strip_tags($result->defeito), strip_tags($result->laudoTecnico), date('d/m/Y', strtotime($result->dataFinal)), date('d/m/Y', strtotime($result->dataInicial)), $result->garantia . ' dias'];
                        $texto_de_notificacao = $this->os_model->criarTextoWhats($texto_de_notificacao, $troca);
                        if (!empty($zapnumber)) {
                            echo '<a title="Via WhatsApp" class="button btn btn-mini btn-success" id="enviarWhatsApp" target="_blank" href="https://wa.me/send?phone=55' . $zapnumber . '&text=' . $texto_de_notificacao . '" ' . ($zapnumber == '' ? 'disabled' : '') . '>
                            <span class="button__icon"><i class="bx bxl-whatsapp"></i></span> <span class="button__text">WhatsApp</span></a>';
                        }
                    } ?>

                    <a title="Enviar por E-mail" class="button btn btn-mini btn-warning" href="<?php echo site_url() ?>/os/enviar_email/<?php echo $result->idOs; ?>">
                        <span class="button__icon"><i class="bx bx-envelope"></i></span> <span class="button__text">Via E-mail</span></a>
                    <?php if ($result->garantias_id) { ?> <a target="_blank" title="Imprimir Garantia" class="button btn btn-mini btn-inverse" href="<?php echo site_url() ?>/garantias/imprimirGarantiaOs/<?php echo $result->garantias_id; ?>">
                            <span class="button__icon"><i class="bx bx-printer"></i></span> <span class="button__text">Garantia</span></a> <?php } ?>
                    <a href="<?php echo base_url(); ?>index.php/os/adicionar" class="button btn btn-mini btn-success" style="max-width: 160px">
                        <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Ordem de Serviço</span></a>

                </div>
            </div>
            <div class="widget-content nopadding tab-content">
                <div class="span12" id="divProdutosServicos" style=" margin-left: 0">
                    <!--  Tabs -->
                    <ul class="nav nav-tabs">
                        <li class="active" id="tabDetalhes"><a href="#tab1" data-toggle="tab">Detalhes da OS</a></li>
                        <li id="tabDesconto"><a href="#tab2" data-toggle="tab">Desconto</a></li>
                        <li id="tabProdutos"><a href="#tab3" data-toggle="tab">Produtos</a></li>
                        <li id="tabServicos"><a href="#tab4" data-toggle="tab">Serviços</a></li>
                        <li id="tabAnexos"><a href="#tab5" data-toggle="tab">Anexos</a></li>
                        <li id="tabAnotacoes"><a href="#tab6" data-toggle="tab">Anotações</a></li>
                        <li id="tabAnotacoes"><a href="#tab7" data-toggle="tab">Assinatura</a></li>
                        <?php if ($result->faturado == 1 || $despesa == 1) { ?>
                            <li id="tabFaturas"><a href="#tab8" data-toggle="tab">Faturas</a></li>
                            <li id="tabNotas"><a href="#tab9" data-toggle="tab">Notas</a></li>
                        <?php } ?>
                    </ul>
                    <?php
                    /* echo "<pre>";
                    print_r($lancamentos);
                    exit; */
                    ?>
                    <!--  TABS -->
                    <div class="tab-content">

                        <!--  TAB DETALHE DA OS -->
                        <div class="tab-pane active" id="tab1">
                            <div class="span12" id="divCadastrarOs">
                                <form action="<?php echo current_url(); ?>" method="post" id="formOs">
                                    <?php echo form_hidden('idOs', $result->idOs) ?>
                                    <!--  PRIMEIRA LINHA-->
                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        <h3>N° OS:
                                            <?php echo $result->idOs; ?>
                                        </h3>

                                        <!--  CLIENTE -->
                                        <div class="span6" style="padding: 0; margin: 0">
                                            <div class="span12" style="padding: 0; margin: 0">
                                                <div class="span9" style="padding: 0; margin: 0">
                                                    <label for="cliente">Cliente<span class="required">*</span></label>
                                                    <input id="cliente" class="span12" type="text" name="cliente" value="<?php echo $result->nomeCliente ?>" />
                                                    <input id="clientes_id" class="span12" type="hidden" name="clientes_id" value="<?php echo $result->clientes_id ?>" />
                                                    <input id="valor" type="hidden" name="valor" value="" />
                                                </div>
                                                <div class="span3" style="padding: 0; margin: 0">
                                                    <label for="map">Maps<span class="required">*</span></label>
                                                    <a target="_blank" title="abra o endereço no Navegador" class="button btn btn-mini btn-inverse" href=" https://www.google.com/maps/search/<?php echo $result->rua . ',' . $result->numero . ' ' . $result->bairro ?>">
                                                        <span class="button__icon"><i class='bx bx-map-alt'></i></span> <span class="button__text">Maps</span></a>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if (empty($result->vendedor)) { ?>
                                            <!--  OPERADOR -->
                                            <div class="span3 ">
                                                <label for="tecnico">Operador<span class="required">*</span></label>
                                                <input id="tecnico" class="span12" type="text" name="tecnico" value="<?php echo $result->nome ?>" />
                                                <input id="usuarios_id" class="span12" type="hidden" name="usuarios_id" value="<?php echo $result->usuarios_id ?>" />
                                            </div>
                                            <!--  TECNICO -->
                                            <div class="span3">
                                                <div id="dynamic_field_tecnico">
                                                    <label class="control-label">Tecnico</span></label>
                                                    <button style=" margin: 0" class="btn btn-success pull-right span12" type="button" name="add_tecnico" id="add_tecnico">Adiciona Técnico</button>
                                                    <?php $i1 = 10;
                                                    foreach ($tecnicos_os as $tecnico_os) {
                                                        $i1++;  ?>
                                                        <input id="idTecnicos_os_value<?php echo $i1; ?>" class="span12" type="hidden" name="tecnicos[<?php echo $i1; ?>][idTecnicos_os]" value="<?php echo $tecnico_os->idTecnicos_os; ?>" />

                                                        <select class="span10" name="tecnicos[<?php echo $i1; ?>][tecnico_id]" id="tecnico_value<?php echo $i1; ?>" style="padding: 0; margin: 0">
                                                            <option value=" <?php echo $tecnico_os->idTecnicos; ?>"><?php echo $tecnico_os->nome; ?></option>
                                                        </select>

                                                        <button style=" margin: 0" class="btn btn-danger btn_remover_tecnico span2" type="button" name="add_tecnico" id="<?php echo $i1; ?>">x</button>
                                                    <?php } ?>

                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <!--  OPERADOR -->
                                            <div class="span2 ">
                                                <label for="tecnico">Operador<span class="required">*</span></label>
                                                <input id="tecnico" class="span12" type="text" name="tecnico" value="<?php echo $result->nome ?>" />
                                                <input id="usuarios_id" class="span12" type="hidden" name="usuarios_id" value="<?php echo $result->usuarios_id ?>" />
                                            </div>
                                            <!--  TECNICO -->
                                            <div class="span2">
                                                <div id="dynamic_field_tecnico">
                                                    <label class="control-label">Tecnico</span></label>
                                                    <button style=" margin: 0" class="btn btn-success pull-right span12" type="button" name="add_tecnico" id="add_tecnico">Adiciona Técnico</button>
                                                    <?php $i1 = 10;
                                                    foreach ($tecnicos_os as $tecnico_os) {
                                                        $i1++;  ?>
                                                        <input id="idTecnicos_os_value<?php echo $i1; ?>" class="span12" type="hidden" name="tecnicos[<?php echo $i1; ?>][idTecnicos_os]" value="<?php echo $tecnico_os->idTecnicos_os; ?>" />

                                                        <select class="span10" name="tecnicos[<?php echo $i1; ?>][tecnico_id]" id="tecnico_value<?php echo $i1; ?>" style="padding: 0; margin: 0">
                                                            <option value=" <?php echo $tecnico_os->idTecnicos; ?>"><?php echo $tecnico_os->nome; ?></option>
                                                        </select>

                                                        <button style=" margin: 0" class="btn btn-danger btn_remover_tecnico span2" type="button" name="add_tecnico" id="<?php echo $i1; ?>">x</button>
                                                    <?php } ?>

                                                </div>
                                            </div>
                                            <!--  OPERADOR -->
                                            <div class="span2 ">
                                                <label for="vendedor">Vendedor<span class="required">*</span></label>
                                                <input id="vendedor" class="span12" type="text" name="vendedor" value="<?php echo $result->vendedor ?>" />

                                            </div>

                                        <?php } ?>

                                    </div>
                                    <!--  SEGUNDA LINHA-->
                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        <div class="span3">
                                            <!--  STATUS -->
                                            <label for="status">Status<span class="required">*</span></label>
                                            <select class="span12" name="status" id="status" value="">
                                                <option <?php if ($result->status == 'Orçamento') {
                                                            echo 'selected';
                                                        } ?> value="Orçamento">Orçamento
                                                </option>
                                                <option <?php if ($result->status == 'A Sair | Aguard Conclusão') {
                                                            echo 'selected';
                                                        } ?> value="A Sair | Aguard Conclusão">A Sair | Aguard Conclusão
                                                </option>
                                                <option <?php if ($result->status == 'Manutenção Preventiva') {
                                                            echo 'selected';
                                                        } ?> value="Manutenção Preventiva">Manutenção Preventiva
                                                </option>
                                                <option <?php if ($result->status == 'Faturado') {
                                                            echo 'selected';
                                                        } ?> value="Faturado">Faturado
                                                </option>
                                                <option <?php if ($result->status == 'Negociação') {
                                                            echo 'selected';
                                                        } ?> value="Negociação">Negociação
                                                </option>
                                                <option <?php if ($result->status == 'Em Andamento') {
                                                            echo 'selected';
                                                        } ?> value="Em Andamento">Em Andamento
                                                </option>
                                                <option <?php if ($result->status == 'Finalizado') {
                                                            echo 'selected';
                                                        } ?> value="Finalizado">Finalizado
                                                </option>
                                                <option <?php if ($result->status == 'Cancelado') {
                                                            echo 'selected';
                                                        } ?> value="Cancelado">Cancelado
                                                </option>
                                                <option <?php if ($result->status == 'Aguardando Peças') {
                                                            echo 'selected';
                                                        } ?> value="Aguardando Peças">Aguardando Peças
                                                </option>
                                                <option <?php if ($result->status == 'Aprovado') {
                                                            echo 'selected';
                                                        } ?> value="Aprovado">Aprovado
                                                </option>
                                            </select>
                                            <label for="local">Local <span class="required">*</span></label>
                                            <select class="span12" name="local" id="local" value="">
                                                <option value="<?php echo $result->local ?>"><?php echo $result->local ?></option>
                                                <option value="Externo">Externo</option>
                                                <option value="Interno">Interno</option>
                                            </select>
                                        </div>
                                        <!--  DATA INICIAL -->
                                        <div class="span3">
                                            <label for="dataInicial">Data Inicial<span class="required">*</span></label>
                                            <input id="dataInicial" autocomplete="off" class="span12 " type="text" name="dataInicial" value="<?php echo date('d/m/Y H:i:s', strtotime($result->dataInicial)); ?>" />
                                            <label for="tipo">Tipo<span class="required">*</span></label>
                                            <select class="span12" name="tipo" id="tipo" value="">
                                                <option value="<?php echo $result->tipo ?>"><?php echo $result->tipo ?></option>
                                                <option value="Avulso">Avulso</option>
                                                <option value="Contrato">Contrato</option>
                                            </select>
                                        </div>
                                        <!--  DATA FINAL -->
                                        <div class="span3">
                                            <label for="dataFinal">Data Final<span class="required">*</span></label>
                                            <input id="dataFinal" autocomplete="off" class="span12 " type="text" name="dataFinal" value="<?php echo date('d/m/Y H:i:s', strtotime($result->dataFinal)); ?>" />
                                        </div>
                                        <!--  GARANTIA -->
                                        <div class="span3">
                                            <!-- <div class="span6" style="padding: 1%; margin-left: 0">
                                                <label for="tipo">Tipo<span class="required">*</span></label>
                                                <label class="span6" style="padding: 1%; margin-left: 0" for="inlineCheckbox1">avulso</label>
                                                <input class="span6" type="checkbox" id="inlineCheckbox1" value="option1">

                                            </div>
                                            <div class="span6" style="padding: 1%; margin-left: 0">
                                                <label for="local">Local <span class="required">*</span></label>
                                                <label class="span6" style=" margin-left: 0" for="inlineCheckbox2">interno</label>
                                                <input class="span6" style=" margin-left: 0" type="checkbox" id="inlineCheckbox2" value="option2">

                                            </div> -->


                                            <label for="garantia">Garantia (dias)</label>
                                            <input id="garantia" type="number" placeholder="Status s/g inserir nº/0" min="0" max="9999" class="span12" name="garantia" value="<?php echo $result->garantia ?>" />
                                            <?php echo form_error('garantia'); ?>
                                            <label for="termoGarantia">Termo Garantia</label>
                                            <input id="termoGarantia" class="span12" type="text" name="termoGarantia" value="<?php echo $result->refGarantia ?>" />
                                            <input id="garantias_id" class="span12" type="hidden" name="garantias_id" value="<?php echo $result->garantias_id ?>" />

                                        </div>
                                    </div>
                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        <button class="btn btn-success pull-right " type="button" name="add" id="add">Adicione Equipamentos</button>
                                    </div>
                                    <!--  TERCEIRA LINHA -->
                                    <div class="span12" style="padding: 1%; margin-left: 0" id="dynamic_field">
                                        <div id="addEquipamentos">
                                            <div class="span12" style="padding:1%; border: 2px solid #ECF0F1;border-radius: 5px; ">
                                                <!--  BUSCA -->
                                                <div id="busca">
                                                    <input id="equipamentos" class="span12 pull-right" autocomplete="off" style="padding: 1; margin-right: 0" type="text" name="equipamentos" placeholder="Pesquise por Equipamento, Série ou Modelo " value="" />
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <!--  TECEIRA LINHA ADDPHP-->
                                    <?php $i = 10;
                                    foreach ($equipamentos as $equipamento) {
                                        $i++;  ?>
                                        <div class="span12" style="padding: 1%; margin-left: 0" id="equip_Add">

                                            <input id="<?php echo $i; ?>idEquipamentos_os" class="span12" name="equipamentos[<?php echo $i; ?>][idEquipamentos_os]" type="hidden" value="<?php echo $equipamento->idEquipamentos_os; ?>" />

                                            <div id="<?php echo $i; ?>addEquipamentos" name="addequipamentos[<?php echo $i; ?>]">

                                                <div class="span12" style="padding:1%; border: 2px solid #ECF0F1;border-radius: 5px; ">

                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <td>
                                                                    <strong id="<?php echo $i; ?>equipamentos_label"><?php echo $equipamento->equipamento; ?> </strong>
                                                                    <!--  <td colspan="7" style="text-align: left; "><a id="<?php echo $i; ?>equipamentos" name="equipamentos[<?php echo $i; ?>][equipamentos]"></a></td> -->
                                                                    <input id="<?php echo $i; ?>equipamentos_value" class="span12" type="hidden" name="equipamentos[<?php echo $i; ?>][equipamentos_value]" value="<?php echo $equipamento->equipamento; ?>" />
                                                                    <input id="<?php echo $i; ?>equipamento_id" class="span12" name="equipamentos[<?php echo $i; ?>][equipamento_id]" type="hidden" value="<?php echo $equipamento->idEquipamentos_os; ?>" />
                                                                    <input id="<?php echo $i; ?>os_id" class="span12" name="equipamentos[<?php echo $i; ?>][os_id]" type="hidden" value="<?php echo $equipamento->os_id; ?>" />
                                                                    <input id="<?php echo $i; ?>clientes_id" class="span12" name="equipamentos[<?php echo $i; ?>][clientes_id]" type="hidden" value="<?php echo $equipamento->clientes_id; ?>" />
                                                                </td>
                                                                <td>
                                                                </td>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="7" style="text-align: left; ">Numero de Serie:<a id="<?php echo $i; ?>serie" style="color: green">&nbsp<?php echo $equipamento->serie; ?></a></td>
                                                                <input id="<?php echo $i; ?>serie_value" class="span12" type="hidden" name="equipamentos[<?php echo $i; ?>][serie_value]" value="<?php echo $equipamento->serie; ?>" />

                                                                <td colspan="7" style="text-align: left; ">Modelo:<a id="<?php echo $i; ?>modelo" style="color: green">&nbsp<?php echo $equipamento->modelo; ?></a></td>
                                                                <input id="<?php echo $i; ?>modelo_value" class="span12" type="hidden" name="equipamentos[<?php echo $i; ?>][modelo_value]" value="<?php echo $equipamento->modelo; ?>" />
                                                            </tr>
                                                            <tr>
                                                                <td colspan="7" style="text-align: left; ">Cor:<a id="<?php echo $i; ?>cor" style="color: green">&nbsp<?php echo $equipamento->cor; ?></a></td>
                                                                <input id="<?php echo $i; ?>cor_value" class="span12" type="hidden" name="equipamentos[<?php echo $i; ?>][cor_value]" value="<?php echo $equipamento->cor; ?>" />

                                                                <td colspan="7" style="text-align: left; ">Descricao:<a id="<?php echo $i; ?>descricao" style="color: green">&nbsp<?php echo $equipamento->descricao; ?></a></td>
                                                                <input id="<?php echo $i; ?>descricao_value" class="span12" type="hidden" name="equipamentos[<?php echo $i; ?>][descricao_value]" value="<?php echo $equipamento->descricao; ?>" />
                                                            </tr>
                                                            <tr>
                                                                <td colspan="7" style="text-align: left; ">Potência:<a id="<?php echo $i; ?>potencia" style="color: green">&nbsp<?php echo $equipamento->potecia; ?></a></td>
                                                                <input id="<?php echo $i; ?>potencia_value" class="span12" type="hidden" name="equipamentos[<?php echo $i; ?>][potencia_value]" value="<?php echo $equipamento->potecia; ?>" />

                                                                <td colspan="7" style="text-align: left; ">Voltagem:<a id="<?php echo $i; ?>voltagem" style="color: green">&nbsp<?php echo $equipamento->voltagem; ?></a></td>
                                                                <input id="<?php echo $i; ?>voltagem_value" class="span12" type="hidden" name="equipamentos[<?php echo $i; ?>][voltagem_value]" value="<?php echo $equipamento->voltagem; ?>" />
                                                            </tr>
                                                            <tr>
                                                                <td colspan="7" style="text-align: left; ">Marcas:<a id="<?php echo $i; ?>marcas" style="color: green">&nbsp<?php echo $equipamento->marca; ?></a></td>
                                                                <input id="<?php echo $i; ?>marcas_value" class="span12" type="hidden" name="equipamentos[<?php echo $i; ?>][marcas_value]" value="<?php echo $equipamento->marca; ?>" />

                                                                <td colspan="7" style="text-align: left; ">Local:&nbsp<a id="<?php echo $i; ?>local" style="color: green"><input id="<?php echo $i; ?>local_value" name="equipamentos[<?php echo $i; ?>][local_value]" value="<?php echo $equipamento->local; ?>" style="width:90%; border: 1px solid #D2D4DE; border-radius: 5px; " /></a></td>

                                                            </tr>
                                                            <tr>
                                                                <td colspan="7" style="text-align: left; ">
                                                                    <label>Defeito Reclamado</label>
                                                                    <textarea rows="5" cols="20" name="equipamentos[<?php echo $i; ?>][defeito_relatado]" id="<?php echo $i; ?>defeito_relatado" placeholder="insira o defeito reclamado" value="<?php echo $equipamento->defeito_declarado; ?>" style="width:100%"><?php echo $equipamento->defeito_declarado; ?></textarea>
                                                                </td>
                                                                <td colspan="7" style="text-align: left; ">
                                                                    <label> Defeito Encontrado</label>
                                                                    <textarea name="equipamentos[<?php echo $i; ?>][defeito_encontrado]" id="<?php echo $i; ?>defeito_encontardo" placeholder="insira o defeito encontrado" value="<?php echo $equipamento->defeito_encontrado; ?>" cols="20" rows="5" style="width:100%"><?php echo $equipamento->defeito_encontrado; ?></textarea>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <button type="button" name="remover" id=<?php echo $i; ?> class="btn btn-danger btn_remove">X</button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <div class="span6" style="padding: 1%; margin-left: 0">
                                        <label>Defeito Reclamado Atendimento</label>
                                        <textarea rows="5" cols="20" name="defeito_atendimento" id="defeito_atendimento" placeholder="insira o defeito reclamado no Atendimento" value="<?php echo $result->defeito; ?>" style="width:100%"><?php echo $result->defeito; ?></textarea>

                                        <!-- <label for="defeito"> Defeito</label>
                                        <textarea class="span12 editor" name="defeito" id="defeito" cols="30" rows="5"></textarea> -->
                                    </div>
                                    <div class="span6" style="padding: 1%; margin-left: 0">
                                        <label>Defeito Encontrado no Atendimento</label>
                                        <textarea rows="5" cols="20" name="defeito_encontrado_no_atendimento" id="defeito_encontrado_no_atendimento" placeholder="insira o defeito encontrado no Atendimento" value="<?php echo $result->defeito_encontrado; ?>" style="width:100%"><?php echo $result->defeito_encontrado; ?></textarea>

                                        <!-- <label for="defeito"> Defeito</label>
                                        <textarea class="span12 editor" name="defeito" id="defeito" cols="30" rows="5"></textarea> -->
                                    </div>

                                    <div class="span6" style="padding: 1%; margin-left: 0">
                                        <label for="observacoes">
                                            <h4>Observações</h4>
                                        </label>
                                        <textarea class="span12 editor" name="observacoes" id="observacoes" cols="30" rows="5"><?php echo $result->observacoes ?></textarea>
                                    </div>
                                    <div class="span6" style="padding: 1%; margin-left: 0">
                                        <label for="laudoTecnico">
                                            <h4>Laudo Técnico</h4>
                                        </label>
                                        <textarea class="span12 editor" name="laudoTecnico" id="laudoTecnico" cols="30" rows="5"><?php echo $result->laudoTecnico ?></textarea>
                                    </div>
                                    <div class="span12" style="padding: 0; margin-left: 0">
                                        <div class="span6 offset3" style="display:flex;justify-content: center">
                                            <button class="button btn btn-mini btn-inverse" id="btnDuplicar"><span class="button__icon"><i class="bx bx-copy-alt"></i></span><span class="button__text2">Duplicar</span></button>
                                            <button class="button btn btn-primary" id="btnContinuar"><span class="button__icon"><i class="bx bx-sync"></i></span><span class="button__text2">Atualizar</span></button>
                                            <a href="<?php echo base_url() ?>index.php/os" class="button btn btn-mini btn-warning"><span class="button__icon"><i class="bx bx-undo"></i></span> <span class="button__text2">Voltar</span></a>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        </div>
                        <!--Desconto-->
                        <?php
                        $total = 0;
                        foreach ($produtos as $p) {
                            $total = $total + $p->subTotal;
                        }
                        ?>
                        <?php
                        $totals = 0;
                        foreach ($servicos as $s) {
                            $preco = $s->preco ?: $s->precoVenda;
                            $subtotals = $preco * ($s->quantidade ?: 1);
                            $totals = $totals + $subtotals;
                        }
                        ?>
                        <!--  TAB DESCONTOS -->
                        <div class="tab-pane" id="tab2">
                            <div class="span12 well" style="padding: 1%; margin-left: 0">
                                <form id="formDesconto" action="<?php echo base_url(); ?>index.php/os/adicionarDesconto" method="POST">
                                    <div id="divValorTotal">
                                        <div class="span2">
                                            <label for="">Valor Total Da OS:</label>
                                            <input class="span12 money" id="valorTotal" name="valorTotal" type="text" data-affixes-stay="true" data-thousands="" data-decimal="." name="valor" value="<?php echo number_format($totals + $total, 2, '.', ''); ?>" readonly />
                                        </div>
                                    </div>
                                    <div class="span1">
                                        <label for="">Tipo Desc.</label>
                                        <select style="width: 4em;" name="tipoDesconto" id="tipoDesconto">
                                            <option value="real">R$</option>
                                            <option value="porcento" <?= $result->tipo_desconto == "porcento" ? "selected" : "" ?>>%</option>
                                        </select>
                                        <strong><span style="color: red" id="errorAlert"></span></strong>
                                    </div>
                                    <div class="span3">
                                        <input type="hidden" name="idOs" id="idOs" value="<?php echo $result->idOs; ?>" />
                                        <label for="">Desconto</label>
                                        <input style="width: 4em;" id="desconto" name="desconto" type="text" placeholder="" maxlength="6" size="2" value="<?= $result->desconto ?>" />
                                        <strong><span style="color: red" id="errorAlert"></span></strong>
                                    </div>
                                    <div class="span2">
                                        <label for="">Total com Desconto</label>
                                        <input class="span12 money" id="resultado" type="text" data-affixes-stay="true" data-thousands="" data-decimal="." name="resultado" value="<?php echo $result->valor_desconto ?>" readonly />
                                    </div>
                                    <div class="span2">
                                        <label for="">&nbsp;</label>
                                        <button class="button btn btn-success" id="btnAdicionarDesconto">
                                            <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Aplicar</span></button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!--  TAB PRODUTOS -->
                        <div class="tab-pane" id="tab3">
                            <div class="span12 well" style="padding: 1%; margin-left: 0">
                                <form id="formProdutos" action="<?php echo base_url() ?>index.php/os/adicionarProduto" method="post">

                                    <div class="span12" id="divVendedor">
                                        <div class="pull-right " style=" margin-right: 2%">
                                            <!--  STATUS -->
                                            <label for="vendedor">Vendedor</label>
                                            <select class="span12" name="vendedor" id="vendedor" value="">
                                                <?php
                                                /* echo "<pre>";
                                                print_r($usuarios);
                                                exit; */
                                                ?>
                                                <?php if (empty($result->vendedor)) {
                                                    foreach ($usuarios as $usuario) {
                                                        if ($usuario->situacao == 1) { ?>
                                                            <option <?php if ($this->session->userdata('nome_admin') == $usuario->nome) {
                                                                        echo 'selected';
                                                                    } ?> value="<?php echo $usuario->nome; ?>"><?php echo $usuario->nome; ?></option> <?php }
                                                                                                                                                }
                                                                                                                                            } else { ?>
                                                    <option value="<?php echo $result->vendedor; ?>"><?php echo $result->vendedor; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="span5" style=" margin-left: 0">
                                        <input type="hidden" name="idProduto" id="idProduto" />
                                        <input type="hidden" name="idOsProduto" id="idOsProduto" value="<?php echo $result->idOs; ?>" />
                                        <input type="hidden" name="estoque" id="estoque" value="" />
                                        <label for="">Produto</label>
                                        <input type="text" class="span12" name="produto" id="produto" placeholder="Digite o nome do produto" />
                                    </div>
                                    <div class="span2">
                                        <label for="">Preço</label>
                                        <input type="text" placeholder="Preço" id="preco" name="preco" class="span12 money" data-affixes-stay="true" data-thousands="" data-decimal="." />
                                    </div>
                                    <div class="span2">
                                        <label for="">Quantidade</label>
                                        <input type="text" placeholder="Quantidade" id="quantidade" name="quantidade" class="span12" />
                                    </div>
                                    <div class="span2">
                                        <label for="">&nbsp;</label>
                                        <button class="button btn btn-success" id="btnAdicionarProduto">
                                            <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar</span></button>
                                    </div>
                                </form>
                            </div>
                            <div class="widget-box" id="divProdutos">
                                <div class="widget_content nopadding">
                                    <table width="100%" class="table table-bordered" id="tblProdutos">
                                        <thead>
                                            <tr>
                                                <th>Produto</th>
                                                <th width="8%">Quantidade</th>
                                                <th width="10%">Preço unit.</th>
                                                <th width="6%">Ações</th>
                                                <th width="10%">Sub-total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $total = 0;
                                            foreach ($produtos as $p) {
                                                $total = $total + $p->subTotal;
                                                echo '<tr>';
                                                echo '<td>' . $p->descricao . '</td>';
                                                echo '<td><div align="center">' . $p->quantidade . '</td>';
                                                echo '<td><div align="center">R$: ' . ($p->preco ?: $p->precoVenda)  . '</td>';
                                                echo (strtolower($result->status) != "cancelado") ? '<td><div align="center"><a href="" idAcao="' . $p->idProdutos_os . '" prodAcao="' . $p->idProdutos . '" quantAcao="' . $p->quantidade . '" title="Excluir Produto" class="btn-nwe4"><i class="bx bx-trash-alt"></i></a></td>' : '<td></td>';
                                                echo '<td><div align="center">R$: ' . number_format($p->subTotal, 2, ',', '.') . '</td>';
                                                echo '</tr>';
                                            } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4" style="text-align: right"><strong>Total:</strong></td>
                                                <td>
                                                    <div align="center"><strong>R$ <?php echo number_format($total, 2, ',', '.'); ?><input type="hidden" id="total-venda" value="<?php echo number_format($total, 2); ?>"></strong></div>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!--  TAB SERVIÇOS -->
                        <div class="tab-pane" id="tab4">
                            <div class="span12 well" style="padding: 1%; margin-left: 0">
                                <form id="formServicos" action="<?php echo base_url() ?>index.php/os/adicionarServico" method="post">
                                    <div class="span6">
                                        <input type="hidden" name="idServico" id="idServico" />
                                        <input type="hidden" name="idOsServico" id="idOsServico" value="<?php echo $result->idOs; ?>" />
                                        <label for="">Serviço</label>
                                        <input type="text" class="span12" name="servico" id="servico" placeholder="Digite o nome do serviço" />
                                    </div>
                                    <div class="span2">
                                        <label for="">Preço</label>
                                        <input type="text" placeholder="Preço" id="preco_servico" name="preco" class="span12 money" data-affixes-stay="true" data-thousands="" data-decimal="." />
                                    </div>
                                    <div class="span2">
                                        <label for="">Quantidade</label>
                                        <input type="text" placeholder="Quantidade" id="quantidade_servico" name="quantidade" class="span12" />
                                    </div>
                                    <div class="span2">
                                        <label for="">&nbsp;</label>
                                        <button class="button btn btn-success">
                                            <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar</span></button>
                                    </div>
                                </form>
                            </div>
                            <div class="widget-box" id="divServicos">
                                <div class="widget_content nopadding">
                                    <table width="100%" class="table table-bordered" id="tblServicos">
                                        <thead>
                                            <tr>
                                                <th>Serviço</th>
                                                <th width="8%">Quantidade</th>
                                                <th width="10%">Preço</th>
                                                <th width="6%">Ações</th>
                                                <th width="10%">Sub-totals</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $totals = 0;
                                            foreach ($servicos as $s) {
                                                $preco = $s->preco ?: $s->precoVenda;
                                                $subtotals = $preco * ($s->quantidade ?: 1);
                                                $totals = $totals + $subtotals;
                                                echo '<tr>';
                                                echo '<td>' . $s->nome . '</td>';
                                                echo '<td><div align="center">' . ($s->quantidade ?: 1) . '</div></td>';
                                                echo '<td><div align="center">R$ ' . $preco  . '</div></td>';
                                                echo '<td><div align="center"><span idAcao="' . $s->idServicos_os . '" title="Excluir Serviço" class="btn-nwe4 servico"><i class="bx bx-trash-alt"></i></span></div></td>';
                                                echo '<td><div align="center">R$: ' . number_format($subtotals, 2, ',', '.') . '</div></td>';
                                                echo '</tr>';
                                            } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4" style="text-align: right"><strong>Total:</strong></td>
                                                <td>
                                                    <div align="center"><strong>R$ <?php echo number_format($totals, 2, ',', '.'); ?><input type="hidden" id="total-servico" value="<?php echo number_format($totals, 2); ?>"></strong></div>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!--  TAB ANEXOS -->
                        <div class="tab-pane" id="tab5">
                            <div class="span12" style="padding: 1%; margin-left: 0">
                                <div class="span12 well" style="padding: 1%; margin-left: 0" id="form-anexos">
                                    <form id="formAnexos1" enctype="multipart/form-data" action="<?php echo base_url(); ?>index.php/os/anexar" accept-charset="utf-8" s method="post">
                                        <!-- <form id="formAnexos" enctype="multipart/form-data" action="javascript:;" accept-charset="utf-8" s method="post"> -->
                                        <div class="span6">
                                            <input type="hidden" name="idOsServico" id="idOsServico" value="<?php echo $result->idOs; ?>" />
                                            <label for="">Anexo</label>
                                            <input type="file" class="span12" name="userfile[]" multiple="multiple" size="20" />
                                        </div>

                                        <div class="span2">
                                            <label for="">.</label>
                                            <button class="button btn btn-success">
                                                <span class="button__icon"><i class='bx bx-paperclip'></i></span><span class="button__text2">Anexar</span></button>
                                        </div>
                                        <div class="span2">
                                            <img src="" class="img-thumbnail" style="width:150px;height:150px;" />
                                        </div>

                                        <div class="span2" style=" margin-left: 0">
                                            <label for="">.</label>
                                            <input type="hidden" name="img" />
                                            <button type="button" class="button btn btn-success" onclick="openWebCamCapture();"> <span class="button__icon"><i class="fa fa-camera"></i></span><span class="button__text2"> Webcam</span></button>
                                        </div>
                                    </form>
                                </div>
                                <div class="span12 pull-left" id="divAnexos" style="margin-left: 0">

                                    <?php
                                    /* echo "<pre>";
                                    print_r($anexos);
                                    exit; */
                                    ?>
                                    <?php
                                    foreach ($anexos as $a) {
                                        if ($a->thumb == null) {
                                            $thumb = base_url() . 'assets/img/icon-file.png';
                                            $link = base_url() . 'assets/img/icon-file.png';
                                        } else {
                                            $thumb = $a->url . '/thumbs/' . $a->thumb;
                                            $link = $a->url . '/' . $a->anexo;
                                        }
                                        echo '<div class="span3" style="min-height: 150px; margin-left: 0">
                                                    <a style="min-height: 150px;" href="#modal-anexo" imagem="' . $a->idAnexos . '" link="' . $link . '" role="button" class="btn anexo span12" data-toggle="modal">
                                                        <img src="' . $thumb . '" alt="">
                                                    </a>
                                                </div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <!--  TAB ANOTAÇÕES -->
                        <div class="tab-pane" id="tab6">
                            <div class="span12" style="padding: 1%; margin-left: 0">

                                <div class="span12" id="divAnotacoes" style="margin-left: 0">

                                    <a href="#modal-anotacao" id="btn-anotacao" role="button" data-toggle="modal" class="button btn btn-success" style="max-width: 160px">
                                        <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar anotação</span></a>
                                    <hr>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Data/Hora</th>
                                                <th>Anotação</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($anotacoes as $a) {
                                                echo '<tr>';
                                                echo '<td>' . date('d/m/Y H:i:s', strtotime($a->data_hora)) . '</td>';
                                                echo '<td>' . $a->anotacao . '</td>';
                                                echo '<td><span idAcao="' . $a->idAnotacoes . '" title="Excluir Anotação" class="btn-nwe4 anotacao"><i class="bx bx-trash-alt"></i></span></td>';
                                                echo '</tr>';
                                            }
                                            if (!$anotacoes) {
                                                echo '<tr><td colspan="3">Nenhuma anotação cadastrada</td></tr>';
                                            }

                                            ?>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>

                        <!--  TAB ASSINATURAS -->
                        <div class="tab-pane" id="tab7">
                            <div class="span12" style="padding: 1%; margin-left: 0">
                                <div class="span12" id="divAssinatura" style="margin-left: 0">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th id="assinarPadName">Assinatura1 </th>
                                                <th id="assinarPadNameCLIENTE">Assinatura Cliete1</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td id="assinarPad">
                                                    <div class="col-md-6">
                                                        <div id="signatureContainer"></div>
                                                        <form id="signatureForm">
                                                            <input type="hidden" name="idOs" id="idOs" value="<?php echo $result->idOs; ?>" />
                                                            <div class="form-group">
                                                                <label for="inputSignatureName">Name</label>
                                                                <input type="text" class="form-control" id="inputSignatureName" name="signatureName" required="">
                                                                <label for="inputSignatureDoc">Doc</label>
                                                                <input type="text" class="form-control" id="inputSignatureDoc" name="signatureDoc" required="">
                                                            </div>
                                                            <input type="hidden" id="inputSignatureID" name="signatureID">
                                                            <input type="hidden" id="action" name="action">
                                                            <button class="btn btn-outline-success" id="submitSignature">SALVAR</button>
                                                            <button class="btn btn-outline-primary" id="updateSignature">ATUALIZAR</button>
                                                            <button class="btn btn-outline-warning" id="disable">DESATIVAR</button>
                                                            <button class="btn btn-outline-danger" id="clear">APAGAR</button>
                                                        </form>
                                                    </div>
                                                </td>
                                                <td id="conteudotd">
                                                    <div id="conteudo"> </div>
                                                    <div>
                                                        <div>Nome:&nbsp&nbsp <a id="nomeAssinatura" style="color: green"></a>&nbsp&nbspDoc:&nbsp&nbsp<a id="docAssinatura" style="color: green"></a></div>
                                                        <div>Data:&nbsp&nbsp <a id="dateAssinatura" style="color: green"></a></div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!--  TAB FATURAS -->
                        <?php if ($result->faturado == 1 || $despesa == 1) { ?>

                            <div class="tab-pane" id="tab8">
                                <div class="widget-box">
                                    <div class="widget-content nopadding tab-content">
                                        <table class="table table-bordered " id="divLancamentos">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Tipo</th>

                                                    <th>Descrição</th>
                                                    <th>Vencimento</th>
                                                    <th>Status</th>

                                                    <th>Forma de Pagamento</th>
                                                    <th>Valor (+)</th>
                                                    <th>Desconto (-)</th>
                                                    <th>Valor Total (=)</th>
                                                    <!-- <th>Ações</th> -->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if (empty($lancamentos)) {
                                                    echo '<tr>
                                                                <td colspan="9" >Nenhum lançamento encontrado</td>
                                                                </tr>';
                                                } else {

                                                    foreach ($lancamentos as $r) {
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

                                                        echo '<td>' . $r->descricao . '</td>';
                                                        echo '<td>' . $vencimento . '</td>';
                                                        echo '<td>' . $status . '</td>';

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


                                                        /*   if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eLancamento')) {
                                                            if ($r->valor_desconto == 0) {
                                                                echo '<a href="#modalEditar" style="margin-right: 1%" data-toggle="modal" role="button" idLancamento="' . $r->idLancamentos . '" descricao="' . $r->descricao . '" valor="' . $r->valor . '" vencimento="' . date('d/m/Y', strtotime($r->data_vencimento)) . '" pagamento="' . $data_pagamento . '" baixado="' . $r->baixado . '" cliente="' . $r->cliente_fornecedor . '" formaPgto="' . $r->forma_pgto . '" tipo="' . $r->tipo . '" observacoes="' . $r->observacoes . '" descontos_editar="' . $r->desconto . '" valor_desconto_editar="' . $r->valor . '" valorEditar_sem_desconto="' . $r->valor . '" usuario="' . $r->nome . '" centro_de_gastos="' . $r->centro_de_gastos . '" classificacao_fin="' . $r->classificacao_fin . '" grupo_finaceiro="' . $r->grupo_finaceiro . '" class="btn-nwe3 editar" title="Editar OS"><i class="bx bx-edit"></i></a>';
                                                            } else {
                                                                echo '<a href="#modalEditar" style="margin-right: 1%" data-toggle="modal" role="button" idLancamento="' . $r->idLancamentos . '" descricao="' . $r->descricao . '" valor="' . $r->valor_desconto . '" vencimento="' . date('d/m/Y', strtotime($r->data_vencimento)) . '" pagamento="' . $data_pagamento . '" baixado="' . $r->baixado . '" cliente="' . $r->cliente_fornecedor . '" formaPgto="' . $r->forma_pgto . '" tipo="' . $r->tipo . '" observacoes="' . $r->observacoes . '" descontos_editar="' . $r->desconto . '" valor_desconto_editar="' . $r->desconto . '" valorEditar_sem_desconto="' . $r->valor  . '" usuario="' . $r->nome . '" centro_de_gastos="' . $r->centro_de_gastos . '" classificacao_fin="' . $r->classificacao_fin . '" grupo_finaceiro="' . $r->grupo_finaceiro . '" class="btn-nwe3 editar" title="Editar OS"><i class="bx bx-edit"></i></a>';
                                                            }
                                                        }
                                                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dLancamento')) {
                                                            echo '<a href="#modalExcluir" data-toggle="modal" role="button" idLancamento="' . $r->idLancamentos . '" class="btn-nwe4 excluir" title="Excluir OS"><i class="bx bx-trash-alt"></i></a>';
                                                        } */

                                                        echo '</td>';
                                                        echo '</tr>';
                                                    }
                                                } ?>
                                            </tbody>
                                            <!-- <tfoot>
                                                <tr>
                                                    <td colspan="6" style="text-align: right; color: green"><strong>Total Receitas:</strong></td>
                                                    <td colspan="6" style="text-align: left; color: green">
                                                        <strong>R$ <?php // echo number_format($totals['receitas'], 2, ',', '.') 
                                                                    ?></strong>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6" style="text-align: right; color: red"><strong>Total Despesas:</strong></td>
                                                    <td colspan="6" style="text-align: left; color: red">
                                                        <strong>R$ <?php // echo number_format($totals['despesas'], 2, ',', '.') 
                                                                    ?></strong>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6" style="text-align: right"><strong>Saldo:</strong></td>
                                                    <td colspan="6" style="text-align: left;">
                                                        <strong>R$ <?php //echo number_format($totals['receitas'] - $totals['despesas'], 2, ',', '.') 
                                                                    ?></strong>
                                                    </td>
                                                </tr>

                                            </tfoot> -->
                                        </table>
                                    </div>
                                </div>

                            </div>
                            <!--  TAB NOTAS -->
                            <div class="tab-pane" id="tab9">

                            </div>
                        <?php } ?>

                    </div>


                </div>
                &nbsp
            </div>
        </div>
    </div>
</div>


<!-- Modal adicionarProduto-->
<div id="modal-adicionarProduto" class="modal hide fade " tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="widget-box">
        <div class="widget-title" style="margin: -20px 0 0">
            <span class="icon">
                <i class="fas fa-shopping-bag"></i>
            </span>
            <h5>Cadastro de Produto</h5>
        </div>
        <div class="widget-content nopadding tab-content">
            <?php echo $custom_error; ?>
            <!-- <form action="<?php echo base_url() ?>index.php/produtos/adicionar" id="formProduto" method="post" class="form-horizontal"> -->
            <form id="formAdicionaProduto" action="<?php echo base_url() ?>index.php/produtos/adicionarModalOs" method="post" class="form-horizontal">
                <div class="control-group">
                    <label for="codDeBarra" class="control-label">Código de Barra<span class=""></span></label>
                    <div class="controls">
                        <input id="codDeBarra" type="text" name="codDeBarra" value="<?php echo set_value('codDeBarra'); ?>" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="descricao" class="control-label">Descrição<span class="required">*</span></label>
                    <div class="controls">
                        <input id="descricao" type="text" name="descricao" value="<?php echo set_value('descricao'); ?>" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Tipo de Movimento</label>
                    <div class="controls">
                        <label for="entrada" class="btn btn-default" style="margin-top: 5px;">Entrada
                            <input type="checkbox" id="entrada" name="entrada" class="badgebox" value="1" checked>
                            <span class="badge">&check;</span>
                        </label>
                        <label for="saida" class="btn btn-default" style="margin-top: 5px;">Saída
                            <input type="checkbox" id="saida" name="saida" class="badgebox" value="1" checked>
                            <span class="badge">&check;</span>
                        </label>
                    </div>
                </div>
                <div class="control-group">
                    <label for="precoCompra" class="control-label">Preço de Compra<span class="required">*</span></label>
                    <div class="controls">
                        <input style="width: 9em;" id="precoCompra" class="money" data-affixes-stay="true" data-thousands="" data-decimal="." type="text" name="precoCompra" value="<?php echo set_value('precoCompra'); ?>" />
                        Margem <input style="width: 3em;" id="margemLucro" name="margemLucro" type="text" placeholder="%" maxlength="3" size="2" />
                        <strong><span style="color: red" id="errorAlert"></span><strong>
                    </div>
                </div>
                <div class="control-group">
                    <label for="precoVenda" class="control-label">Preço de Venda<span class="required">*</span></label>
                    <div class="controls">
                        <input id="precoVenda" class="money" data-affixes-stay="true" data-thousands="" data-decimal="." type="text" name="precoVenda" value="<?php echo set_value('precoVenda'); ?>" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="unidade" class="control-label">Unidade<span class="required">*</span></label>
                    <div class="controls">
                        <select id="unidade" name="unidade" style="width: 15em;">

                        </select>

                    </div>
                </div>
                <div class="control-group">
                    <label for="estoque" class="control-label">Estoque<span class="required">*</span></label>
                    <div class="controls">
                        <input id="estoque" type="text" name="estoque" value="<?php echo set_value('estoque'); ?>" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="estoqueMinimo" class="control-label">Estoque Mínimo</label>
                    <div class="controls">
                        <input id="estoqueMinimo" type="text" name="estoqueMinimo" value="<?php echo set_value('estoqueMinimo'); ?>" />
                    </div>
                </div>
                <div class="form-actions">
                    <div class="span12">
                        <div class="span6 offset3" style="display: flex;justify-content: center">
                            <button class="button btn btn-success" id="btnAdicionarProdutoModalOs">
                                <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar</span></button>
                            <!-- <button type="submit" class="button btn btn-mini btn-success" style="max-width: 160px"><span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar</span></button> -->
                            <button type="button" class="button btn btn-warning " data-dismiss="modal" style="max-width: 160px"><span class="button__icon"><i class='bx bx-gravityui-delete'></i></span><span class="button__text2">Fechar</span></button>
                        </div>
                        <div class="widget-box" id="divProdutosModalOs"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal adicionarServiços-->
<div id="modal-adicionarServico" class="modal hide fade " tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="widget-box">
        <div class="widget-title" style="margin: -20px 0 0">
            <span class="icon">
                <i class="fas fa-wrench"></i>
            </span>
            <h5>Cadastro de Serviço</h5>
        </div>
        <div class="widget-content nopadding tab-content">
            <?php echo $custom_error; ?>
            <!--   <form action="<?php echo current_url(); ?>" id="formAdicionarServico" method="post" class="form-horizontal"> -->
            <form id="formAdicionaServico" action="<?php echo base_url() ?>index.php/servicos/adicionarModalOs" method="post" class="form-horizontal">
                <div class="control-group">
                    <label for="nome" class="control-label">Nome<span class="required">*</span></label>
                    <div class="controls">
                        <input id="nome" type="text" name="nome" value="<?php echo set_value('nome'); ?>" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="preco" class="control-label"><span class="required">Preço*</span></label>
                    <div class="controls">
                        <input id="preco" class="money" data-affixes-stay="true" data-thousands="" data-decimal="." type="text" name="preco" value="<?php echo set_value('preco'); ?>" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="descricao" class="control-label">Descrição</label>
                    <div class="controls">
                        <input id="descricao" type="text" name="descricao" value="<?php echo set_value('descricao'); ?>" />
                    </div>
                </div>
                <div class="form-actions">
                    <div class="span12">
                        <div class="span6 offset3" style="display:flex;justify-content: center">
                            <button type="submit" class="button btn btn-mini btn-success" style="max-width: 160px">
                                <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar</span></a></button>
                            <button type="button" class="button btn btn-warning " data-dismiss="modal" style="max-width: 160px"><span class="button__icon"><i class='bx bx-gravityui-delete'></i></span><span class="button__text2">Fechar</span></button>
                        </div>
                        <div class="widget-box" id="divServicosModalOs"></div>
                    </div>
                </div>
            </form>
        </div>
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

<!-- Modal cadastro anotações -->
<div id="modal-anotacao" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="#" method="POST" id="formAnotacao">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">Adicionar Anotação</h3>
        </div>
        <div class="modal-body">
            <div class="span12" id="divFormAnotacoes" style="margin-left: 0"></div>
            <div class="span12" style="margin-left: 0">
                <label for="anotacao">Anotação</label>
                <textarea class="span12" name="anotacao" id="anotacao" cols="30" rows="3"></textarea>
                <input type="hidden" name="os_id" value="<?php echo $result->idOs; ?>">
            </div>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button class="btn" data-dismiss="modal" aria-hidden="true" id="btn-close-anotacao">Fechar</button>
            <button class="btn btn-primary">Adicionar</button>
        </div>
    </form>
</div>

<!-- WEB CAM-->
<!-- <div id="modalWebcam" class="modal hide fade " tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="closeLayerWebcam();">×</button>
        <h3 id=" myModalLabel">WEB CAM</h3>
    </div>
    <div class="modal-body">
        <div class="span12 alert alert-info" style="margin-left: 0"> Obrigatório o preenchimento dos campos com asterisco.</div>
        <div class="span12" style="margin-left: 0;display:flex;justify-content: center">
            <div id="webcam_preview"></div>
            <div id="webcam_capture_preview"></div>
        </div>
        <div class="span12" style="margin-left: 0;display:flex;justify-content: center">
            <button class="btn btn-primary btn-capture" onclick="generateCapture();">Capturar</button>
            <button class="btn btn-default btn-webcam" onclick="redefineWebcam();" style="display:none;">Webcam</button>
        </div>
    </div>
    <div class="modal-footer" style="display:flex;justify-content: center">
        <button class="button btn btn-warning" onclick="closeLayerWebcam();" data-dismiss="modal" aria-hidden="true" id="btn-cancelar-faturar"><span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span></button>
        <button class="button btn btn-danger" onclick="saveCoopieCapture();"><span class="button__icon"><i class='bx bx-dollar'></i></span> <span class="button__text2">Salvar</span></button>
    </div>

</div>
 -->
<!-- Modal Faturar-->
<div id="modal-faturar2" class="modal hide fade " tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <!--  <form id="formFaturar" action="<?php echo current_url() ?>" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">Faturar OS</h3>
        </div>
        <div class="modal-body">
            <div class="span12 alert alert-info" style="margin-left: 0"> Obrigatório o preenchimento dos campos com asterisco.</div>
            <div class="span12" style="margin-left: 0">
                <label for="descricao">Descrição</label>
                <input class="span12" id="descricao" type="text" name="descricao" value="Fatura de OS Nº: <?php echo $result->idOs; ?> " />
            </div>
            <div class="span12" style="margin-left: 0">
                <div class="span12" style="margin-left: 0">
                    <label for="cliente">Cliente*</label>
                    <input class="span12" id="cliente" type="text" name="cliente" value="<?php echo $result->nomeCliente ?>" />
                    <input type="hidden" name="clientes_id" id="clientes_id" value="<?php echo $result->clientes_id ?>">
                    <input type="hidden" name="os_id" id="os_id" value="<?php echo $result->idOs; ?>">
                    <input type="hidden" name="tipoDesconto" id="tipoDesconto" value="<?php echo $result->tipo_desconto; ?>">
                </div>
            </div>
            <div class="span12" style="margin-left: 0">
                <div class="span6" style="margin-left: 0">
                    <label for="valor">Valor*</label>
                    <input type="hidden" id="tipo" name="tipo" value="receita" />
                    <input class="span12 money" id="valor" type="text" data-affixes-stay="true" data-thousands="" data-decimal="." name="valor" value="<?php echo number_format($totals + $total, 2, '.', ''); ?>" />
                </div>
                <div class="span6" style="margin-left: 2;">
                    <label for="valor">Valor Com Desconto*</label>
                    <input class="span12 money" id="faturar-desconto" type="text" name="faturar-desconto" value="<?php echo number_format($result->valor_desconto, 2, '.', ''); ?> " />
                    <strong><span style="color: red" id="resultado"></span></strong>
                </div>
            </div>
            <div class="span12" style="margin-left: 0">
                <div class="span4" style="margin-left: 0">
                    <label for="vencimento">Data Entrada*</label>
                    <input class="span12 datepicker" autocomplete="off" id="vencimento" type="text" name="vencimento" />
                </div>
            </div>
            <div class="span12" style="margin-left: 0">
                <div class="span4" style="margin-left: 0">
                    <label for="recebido">Recebido?</label>
                    &nbsp &nbsp &nbsp &nbsp <input id="recebido" type="checkbox" name="recebido" value="1" />
                </div>
                <div id="divRecebimento" class="span8" style=" display: none">
                    <div class="span6">
                        <label for="recebimento">Data Recebimento</label>
                        <input class="span12 datepicker" autocomplete="off" id="recebimento" type="text" name="recebimento" />
                    </div>
                    <div class="span6">
                        <label for="formaPgto">Forma Pgto</label>
                        <select name="formaPgto" id="formaPgto" class="span12">
                            <option value="Dinheiro">Dinheiro</option>
                            <option value="Cartão de Crédito">Cartão de Crédito</option>
                            <option value="Débito">Débito</option>
                            <option value="Boleto">Boleto</option>
                            <option value="Depósito">Depósito</option>
                            <option value="Pix">Pix</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true" id="btn-cancelar-faturar"><span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span></button>
            <button class="button btn btn-danger"><span class="button__icon"><i class='bx bx-dollar'></i></span> <span class="button__text2">Faturar</span></button>
        </div>
    </form> -->
</div>

<!-- Modal nova receita e despesa AVISTA --><!-- id="modalReceita" -->
<div id="modal-faturar" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form id="formReceita" action="<?php echo base_url() ?>index.php/financeiro/adicionarReceita" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">Faturamento Avista</h3>
        </div>
        <div class="modal-body">

            <div class="span12 alert alert-info" style="margin-left: 0"> Obrigatório o preenchimento dos campos com
                asterisco.
            </div>

            <div class="span3" style="margin-left: 0">
                <label for="tipo">Tipo</label>
                <select name="tipo" id="tipo" class="span10">
                    <option value="receita">Receita</option>
                </select>
            </div>

            <div class="span9" style="margin-left: 0">
                <label for="descricao">Descrição/Referência*</label>
                <input class="span12" id="descricao" type="text" name="descricao" value="Fatura de OS Nº: <?php echo $result->idOs; ?> " />
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
            <div class="span7" style="margin-left: 0">
                <label for="clasFin" style="margin-left: 0">Clas. finaceira</label>
                <select name="clasFin" id="clasFin" class="span10" required>
                    <?php foreach ($classificacao_financeira as $f) {
                        if ($f->nomeClassFin == "RECEITA SOBRE SERVIÇO") {
                            echo '<option selected value="' . $f->nomeClassFin . '">' . $f->nomeClassFin . '</option>';
                        } else {
                            echo '<option value="' . $f->nomeClassFin . '">' . $f->nomeClassFin . '</option>';
                        }
                    } ?>
                </select>
            </div>
            <!--  -->

            <div class="span12" style="margin-left: 0">
                <div class="span12" style="margin-left: 0">
                    <label for="cliente">Cliente*</label>
                    <input class="span12" id="cliente" type="text" name="cliente" value="<?php echo $result->nomeCliente ?>" />
                    <input type="hidden" name="clientes_id" id="clientes_id" value="<?php echo $result->clientes_id ?>">
                    <input type="hidden" name="os_id" id="os_id" value="<?php echo $result->idOs; ?>">
                    <input type="hidden" name="tipoDesconto" id="tipoDesconto" value="<?php echo $result->tipo_desconto; ?>">
                </div>
            </div>

            <?php
            /* echo "<pre>";
            print_r($result);
            exit; */
            ?>
            <!--  -->
            <div class="span12" style="margin-left: 0">
                <label for="observacoes">Observações</label>
                <textarea class="span12" id="observacoes" name="observacoes"><?php echo $result->observacoes; ?></textarea>
            </div>

            <div class="span12" style="margin-left: 0">
                <div class="span4" style="margin-left: 0">
                    <label for="valor">Valor*</label>
                    <input class="span12 money" id="valor" type="text" data-affixes-stay="true" data-thousands="" data-decimal="." name="valor" value="<?php if ($result->valor_desconto == 0) {
                                                                                                                                                            echo number_format($totals + $total, 2, '.', '');
                                                                                                                                                        } else {
                                                                                                                                                            echo number_format($result->valor_desconto, 2, '.', '');
                                                                                                                                                        }  ?>" />
                </div>

                <div class="span4">
                    <label for="descontos">Desconto</label>
                    <input class="span6 money" id="descontos" type="text" name="descontos" value="<?php if ($result->valor_desconto == 0) {
                                                                                                        echo number_format(0, 2, '.', '');
                                                                                                    } else {
                                                                                                        echo number_format(($totals + $total) - $result->valor_desconto, 2, '.', '');
                                                                                                    }  ?> " placeholder="em R$" style="float: left;" />

                </div>

                <div class="span3">
                    <label for="valor_desconto">Val s/desc <i class="icon-info-sign tip-left" title="Não altere esta campo, caso clicar nele e sair e ficar vázio, terá que recarregar á pagina e inserir de novo"></i></label>
                    <input type="hidden" class="span12 money" id="valor_desconto" readOnly="true" title="Não altere este campo" type="text" name="valor_desconto" value="<?php echo number_format(($totals + $total) - $result->valor_desconto, 2, '.', ''); ?> " />
                    <input class="span12 money" id="valor_sem_desconto" readOnly="true" title="Não altere este campo" type="text" name="valor_com_desconto" value="<?php echo number_format($totals + $total, 2, '.', ''); ?> " />
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
            <h3 id="myModalLabel">Faturamento Parcelado</h3>
        </div>
        <div class="modal-body">
            <div class="span12 alert alert-info" style="margin-left: 0"> Obrigatório o preenchimento dos campos com asterisco.</div>
            <div class="span3" style="margin-left: 0">
                <label for="tipo_parc" style="margin-left: 0">Tipo</label>
                <select name="tipo_parc" id="tipo_parc" class="span10">
                    <option value="receita">Receita</option>
                </select>
            </div>

            <div class="span9" style="margin-left: 0">
                <label for="descricao_parc">Descrição/Referência*</label>
                <input class="span12" id="descricao_parc" type="text" name="descricao_parc" value="Fatura de OS Nº: <?php echo $result->idOs; ?> " />
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
            <div class="span7" style="margin-left: 0">
                <label for="clasFin" style="margin-left: 0">Clas. finaceira</label>
                <select name="clasFin" id="clasFin_parc" class="span12" required>
                    <option value="">Selecione</option>
                    <?php foreach ($classificacao_financeira as $f) {
                        if ($f->nomeClassFin == "RECEITA SOBRE SERVIÇO") {
                            echo '<option selected value="' . $f->nomeClassFin . '">' . $f->nomeClassFin . '</option>';
                        } else {
                            echo '<option value="' . $f->nomeClassFin . '">' . $f->nomeClassFin . '</option>';
                        }
                    } ?>
                </select>
            </div>
            <!--  -->
            <div class="span12" style="margin-left: 0">
                <div class="span12" style="margin-left: 0">
                    <label for="cliente">Cliente*</label>
                    <input class="span12" id="cliente_par" type="text" name="cliente_parc" value="<?php echo $result->nomeCliente ?>" />
                    <input type="hidden" name="idCliente_parc" id="clientes_id_par" value="<?php echo $result->clientes_id ?>">
                    <input type="hidden" name="os_id" id="os_id" value="<?php echo $result->idOs; ?>">
                    <input type="hidden" name="tipoDesconto" id="tipoDesconto" value="<?php echo $result->tipo_desconto; ?>">
                </div>
            </div>

            <div class="span12" style="margin-left: 0">
                <label for="observacoes_parc">Observações</label>
                <textarea class="span12" id="observacoes_parc" name="observacoes_parc"><?php echo $result->observacoes; ?></textarea>
            </div>

            <div class="span12" style="margin-left: 0">
                <div class="span3" style="margin-left: 0">
                    <label for="valor_parc">Valor*</label>
                    <input class="span12 money" id="valor_parc" type="text" data-affixes-stay="true" data-thousands="" data-decimal="." name="valor_parc" value="<?php if ($result->valor_desconto == 0) {
                                                                                                                                                                        echo number_format($totals + $total, 2, '.', '');
                                                                                                                                                                    } else {
                                                                                                                                                                        echo number_format($result->valor_desconto, 2, '.', '');
                                                                                                                                                                    }  ?>" />
                </div>

                <div class="span3">
                    <label for="descontos_parc">Desconto</label>
                    <input class="span6 money" id="desconto_parc" type="text" name="desconto_parc" value="<?php if ($result->valor_desconto == 0) {
                                                                                                                echo number_format(0, 2, '.', '');
                                                                                                            } else {
                                                                                                                echo number_format(($totals + $total) - $result->valor_desconto, 2, '.', '');
                                                                                                            }  ?> " placeholder="em R$" style="float: left;" />

                </div>

                <div class="span3" style="margin-left: 0">
                    <label for="desconto_parc">Val s/desc<i class="icon-info-sign tip-left" title="Não altere esta campo, caso clicar nele e sair e ficar vázio, terá que recarregar á pagina e inserir de novo"></i></label>
                    <input type="hidden" class="span12 money" id="descontos_parc" readOnly="true" title="Não altere este campo" type="text" name="descontos_parc" value="<?php echo number_format(($totals + $total) - $result->valor_desconto, 2, '.', ''); ?> " style="float: left;" />
                    <input class="span12 money" id="valor_com_desconto" readOnly="true" title="Não altere este campo" type="text" name="valor_com_desconto" value="<?php echo number_format($totals + $total, 2, '.', ''); ?> " />
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
                        <option value="0">DIVIDE</option>
                        <option value="1">MULTIPLICA</option>
                    </select>
                </div>
            </div>

            <div class="span12" style="margin-left: 0;">
                <div class="span4">
                    <label for="entrada">Entrada <i class="icon-info-sign tip-right" title="O valor da entrada será lançado como pago no dia atual (Hoje)"></i></label>
                    <input class="span12 money" id="entrada_par" type="text" name="entrada" value="0" />
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


<!-- Modal Equipamentos-->
<div id="modal-adicionaEquipamentos" class="modal hide fade " tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="row-fluid" style="margin-top:0">
        <div class="span12">
            <div class="widget-box">
                <div class="widget-title" style="margin: -20px 0 0">
                    <span class="icon">
                        <i class="fas fa-shopping-bag"></i>
                    </span>
                    <h5>Cadastro de Equipamentos</h5>
                </div>
                <div class="widget-content nopadding tab-content">
                    <?php echo $custom_error; ?>
                    <form action="<?php echo current_url(); ?>" id="formEquipamentos" method="post" class="form-horizontal">

                        <div class="widget-content nopadding tab-content">
                            <div class="span12">
                                <div class="control-group">
                                    <label for="tipoEquipamento" class="control-label">Tipo Equipamento<span class=""></span></label>
                                    <div class="controls">
                                        <input id="tipoEquipamento" type="text" name="tipoEquipamento" value="<?php echo set_value('tipoEquipamento'); ?>" />
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label for="descricao" class="control-label">Descrição<span class="required">*</span></label>
                                    <div class="controls">
                                        <input id="descricao" type="text" name="descricao" value="<?php echo set_value('descricao'); ?>" />
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label for="modelo" class="control-label">Modelo<span class="required">*</span></label>
                                    <div class="controls">
                                        <input id="modelo" type="text" name="modelo" value="<?php echo set_value('modelo'); ?>" />
                                    </div>
                                </div>
                                <div class="control-group" class="control-label">
                                    <label for="marcas" class="control-label">Marcas</label>
                                    <div class="controls">
                                        <select id="marcas" name="marcas">
                                            <option value="">Selecione...</option>
                                            <?php
                                            /*  foreach ($marcas as $m) {
                                                                                                    echo '<option>' . $m->marca . '</option>';
                                                                                                } */
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label for="serial" class="control-label">Serial<span class="required">*</span></label>
                                    <div class="controls">
                                        <input id="serial" type="text" name="serial" value="<?php echo set_value('serial'); ?>" />
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label for="cor" class="control-label">Cor<span class="required">*</span></label>
                                    <div class="controls">
                                        <input id="cor" type="text" name="cor" value="<?php echo set_value('cor'); ?>" />
                                    </div>
                                </div>
                                <div class="control-group" class="control-label">
                                    <label for="voltagem" class="control-label">Voltagem</label>
                                    <div class="controls">
                                        <select id="voltagem" name="voltagem">
                                            <option value="">Selecione...</option>
                                            <option value="">110v</option>
                                            <option value="">220v</option>
                                            <option value="">bivolt</option>
                                            <option value="">Automática</option>
                                            <option value="">bivolt</option>
                                            <option value="">48v</option>
                                            <option value="">24v</option>
                                            <option value="">12v</option>
                                            <option value="">9v</option>
                                            <option value="">5v</option>
                                            <option value="">3,3v</option>

                                        </select>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label for="potencia" class="control-label">Potência<span class="required">*</span></label>
                                    <div class="controls">
                                        <input id="potencia" type="text" name="potencia" value="<?php echo set_value('potencia'); ?>" />
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="form-actions">
                            <div class="span12">
                                <div class="span6 offset3" style="display: flex;justify-content: center">
                                    <button type="submit" class="button btn btn-mini btn-success" style="max-width: 160px"><span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar</span></button>
                                    <a href="<?php echo base_url() ?>index.php/equipamentos" id="" class="button btn btn-mini btn-warning"><span class="button__icon"><i class="bx bx-undo"></i></span><span class="button__text2">Voltar</span></a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url() ?>assets/assinaturas/js/jquery.signature.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>
<!-- Função de adiciona estrutura html-->
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/adicionaHtml.js"></script>

<script src="<?= base_url(); ?>assets/js/Croppie-2.6.2/croppie.js"></script>
<!-- <script src="<?= base_url(); ?>assets/js/webcam.min.js"></script> -->

<script type="text/javascript">
    /*
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
                }
            }

            function mostrarValoresEditar() {
                if (document.getElementById('valorEditar').value == "" || document.getElementById('descontos_editar').value == "" || document.getElementById('descontoEditar').value == "") {

                } else {
                    var valor = parseFloat(document.getElementById('valorEditar').value);
                    var desconto = parseFloat(document.getElementById('descontos_editar').value);
                    var valor_desconto = parseFloat(document.getElementById('descontoEditar').value);
                    var resultado, total;
                    resultado = valor;
                    total = valor - desconto;

                    resultdesc = total;
                    totaldesc = valor - (resultdesc);

                    document.getElementById('valorEditar').value = total.toFixed(2);
                    document.getElementById('descontoEditar').value = totaldesc.toFixed(2);
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
                }
            } */



    /***WEB CAM***/
    /*     var webcam = false;
        let uploadCropUp = "";

        function openWebCamCapture() {

            $("#modalWebcam").modal();
            $("#modalWebcam").show();
            $('.btn-webcam').hide();
            $('.btn-capture').show();
            $('.btn-crop').hide();

            $('#webcam_capture_preview').hide();
            $('#webcam_preview').show();

            webcam = Webcam.set({
                width: 332,
                height: 256,
                image_format: 'jpeg',
                jpeg_quality: 90
            });
            Webcam.attach('#webcam_preview');
        }

        function generateCapture() {

            Webcam.snap(function(data_uri) {

                console.log(data_uri)

                $('#webcam_capture_preview').show();
                $('#webcam_preview').hide();
                //$('#webcam_capture_preview').html('fuciona');
                $('#webcam_capture_preview').html('<img src="' + data_uri + '" />');
                $('.btn-capture').hide();
                $('.btn-crop').show();
                $('.btn-webcam').show();
                uploadCropUp = data_uri;

                //$uploadCrop = $('#webcam_capture_preview img').croppie({
                //    viewport: {
                //        width: 200,
                //        height: 200,
                //        type: 'square'
                //    },
                //    enableExif: true,
                //    enableResize: false,
                //    boundary: {
                //        width: 300,
                //        height: 300
                //    }
                //});//

                Webcam.reset();

            });
        }

        function saveCoopieCapture() {

            $('input[name="userfile"]').val(uploadCropUp);
            $('.img-thumbnail').attr('src', uploadCropUp);
            $("#modalWebcam").hide();

             // $uploadCrop.croppie('result', {
             //     type: 'canvas',
             //     size: 'viewport'
             // }).then(function(resp) {
             //     $('input[name="img"]').val(resp);
             //     $('.img-thumbnail').attr('src', resp);
             //     $("#modalWebcam").hide();
             // });
            Webcam.reset();
        }

        function saveCoopieUpload() {

            $uploadCropUp.croppie('result', {
                type: 'canvas',
                size: 'viewport'
            }).then(function(resp) {
                $('input[name="userfile"]').val(resp);
                $('.img-thumbnail').attr('src', resp);
                $('.layer_fileupload').hide();
                $("#modalUpload").hide();
            });

        }

        function redefineWebcam() {

            $('#webcam_capture_preview').hide();
            $('#webcam_preview').show();
            $('.btn-crop').hide();
            $('.btn-capture').show();
            $('.btn-webcam').hide();
        }

        function closeLayerWebcam() {
            $("#modalWebcam").hide();
            Webcam.reset();
        }

        var $uploadCropUp;

        function demoUpload() {

            function readFile(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $uploadCropUp.croppie('bind', {
                            url: e.target.result
                        }).then(function() {

                        });

                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            $uploadCropUp = $('#upload-demo').croppie({
                viewport: {
                    width: 250,
                    height: 250,
                    type: 'square'
                },
                enableExif: true,
                enableResize: true,
                boundary: {
                    width: 300,
                    height: 300
                }
            });

            $('#upload').on('change', function() {
                readFile(this);
            });

        }

        function openUpload() {
            $("#modalUpload").modal();
            $("#modalUpload").show();
        } */



    /***WEB CAM***/


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

        $('#parcelado').click(function(event) {
            /* $('#qtdparcelas_div').show();
            $('#parcelado_label_div').hide(); */

            $('#cancelar_nova_receita').trigger('click');
            $('#abrirmodalreceitaparcelada').trigger('click');

        });

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
                            window.location.href = window.BaseUrl + 'index.php/os/editar/' + <?php echo $result->idOs ?>;
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
            minLength: 1,
            select: function(event, ui) {
                $("#codDeBarra").val(ui.item.codbar);
                $("#idProduto").val(ui.item.id);
                $("#estoque").val(ui.item.estoque);
                $("#preco").val(ui.item.preco);
                $("#quantidade").focus();
            },
            open: function(event, ui) {
                $('.ui-autocomplete').append('<a href="#modal-adicionarProduto" id="btn-adicionarProduto" role="button" data-toggle="modal" class="button btn btn-success" > <span class="button__icon"><i class="bx bx-plus-circle"></i></span><span class="button__text2">Adicionar Produto</span></a>');
            }
        });

        $("#servico").autocomplete({
            source: "<?php echo base_url(); ?>index.php/os/autoCompleteServico",
            minLength: 2,
            select: function(event, ui) {
                $("#idServico").val(ui.item.id);
                $("#preco_servico").val(ui.item.preco);
                $("#quantidade_servico").focus();
            },
            open: function(event, ui) {
                $('.ui-autocomplete').append('<a href="#modal-adicionarServico" id="btn-adicionarServico" role="button" data-toggle="modal" class="button btn btn-success" > <span class="button__icon"><i class="bx bx-plus-circle"></i></span><span class="button__text2">Adicionar Serviço</span></a>');
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
                //$('.ui-autocomplete').append('<li><a href="javascript:abreModal()" ><button type="button" class="btn btn-primary"><span class="button__icon"><i class="bx bx-plus-circle"></i></span><span class="button__text2">Equipamentos</button></a></li > ');

                $('.ui-autocomplete').append('<a href="#modal-adicionaEquipamentos" id="btn-adicionaEquipamentos" role="button" data-toggle="modal" class="button btn btn-success" > <span class="button__icon"><i class="bx bx-plus-circle"></i></span><span class="button__text2">Adicionar Equpamento</span></a>');

                //$('.ui-autocomplete').append('<li><a href="<?php echo site_url() ?>/equipamentos/adicionar" ><button type="button" class="btn btn-primary"><span class="button__icon"><i class="bx bx-plus-circle"></i></span><span class="button__text2">Equipamentos</button></a></li > ');
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

        $("#btnDuplicar").click(function() {
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
                },
                submitHandler: function(form) {
                    var dados = $(form).serialize();
                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url(); ?>index.php/os/duplicarOs",
                        data: dados,
                        dataType: 'json',
                        success: function(data) {
                            //alert(data.os);
                            //lert(data.tecnico);
                            console.log(data.os)
                            console.log("TECNICO:" + data.tecnico)
                            if (data.result == true) {

                                //window.location.replace("http://www.w3schools.com");
                                window.location.href = '<?php echo base_url(); ?>index.php/os/editar/' + data.os;

                            } else {
                                Swal.fire({
                                    type: "error",
                                    title: "Atenção",
                                    text: "Ocorreu um erro ao tentar adicionar serviço."
                                });
                            }
                            /* if (data.result == true) {
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
                            } */
                        }
                    });
                    return false;
                }



            });
        });


        $("#formProdutos").validate({
            rules: {
                preco: {
                    required: true
                },
                quantidade: {
                    required: true
                },
                vendedor: {
                    required: true
                }
            },
            messages: {
                preco: {
                    required: 'Inserir o preço'
                },
                quantidade: {
                    required: 'Insira a quantidade'
                },
                vendedor: {
                    required: 'Insira o vendedor'
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
                                $("#vendedor").val('');
                                $("#divVendedor").load("<?php echo current_url(); ?> #divVendedor");
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
                        //alert('teste');
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
            var idOS = "<?php echo $result->idOs ?>"
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
            var idOS = "<?php echo $result->idOs ?>"
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
            var idOS = "<?php echo $result->idOs ?>"
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
            var idOS = "<?php echo $result->idOs ?>"
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
        jQuery.datetimepicker.setLocale('pt-BR');
        jQuery('#dataInicial').datetimepicker({
            format: 'd/m/Y H:i',
            lang: 'pt-BR'
        });
        jQuery('#dataFinal').datetimepicker({
            format: 'd/m/Y H:i',
            lang: 'pt-BR'
        });

        $('.editor').trumbowyg({
            lang: 'pt_br'
        });

        let valor_Parcela = $('#multiplica_parc').val();
        //chamada em outra funções mostra o parcelamento
        function valorParcelas() {
            var valor_parc = $("#valor_parc").val();
            var qtdparc = $("#qtdparcelas_parc").val();
            var entrada = $("#entrada_par").val();
            var result = (valor_parc - entrada) / qtdparc;

            console.log(entrada);
            if (entrada > 0) {
                $("#string_parc").text('R$ ' + entrada + ' de entrada mais ' + qtdparc + ' parcelas de R$ ' + parseFloat(Math.round(result * 100) / 100).toFixed(2));
                $("#valorparcelas").val(parseFloat(Math.round(result * 100) / 100).toFixed(2));
            } else {
                $("#string_parc").text(qtdparc + ' parcelas de R$ ' + parseFloat(Math.round(result * 100) / 100).toFixed(2));
                $("#valorparcelas").val(parseFloat(Math.round(result * 100) / 100).toFixed(2));
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

    var i = 1; //adciciomna conteudo equipamentos
    var i1 = 1; //adciciomna conteudo tecnicos
    $(document).ready(function() {
        $('#addEquipamentos').hide();
        $('#assinarPadNameCLIENTE').hide();
        $('#conteudotd').hide();

    });

    //adciciomna conteudo tecnicos
    $('#add_tecnico').click(function() {
        i1++;
        $('#dynamic_field_tecnico').append(
            '<select class="span10" name="tecnicos[' + i1 + '][tecnico_id]" id="tecnico_value' + i1 + '" style="padding: 0; margin: 0"><option value=""></option><?php foreach ($tecnicos as $tecnico) {
                                                                                                                                                                        echo '<option value="' . $tecnico->idTecnicos . '">' . $tecnico->nome . '</option>';
                                                                                                                                                                    } ?></select><button style=" margin: 0" class="btn btn-danger btn_remover_tecnico span2" type="button" name="add_tecnico" id="' + i1 + '" >x</button>'

        );
    });

    $(document).on('click', '.btn_remover_tecnico', function() {
        var button_id = $(this).attr("id");
        $('#' + button_id + '').remove();
        $('#tecnico_value' + button_id + '').remove();
        //$('#idTecnicos_os_value' + button_id + '').remove();
    });

    //adciciomna conteudo equipamentos       
    $('#add').click(function() {
        //$("#equipamento1").show();
        i++;
        $("#addEquipamentos").show();
        addEquipamentos(i);
    });
    $(document).on('click', '.btn_remove', function() {
        var button_id = $(this).attr("id");
        $('#' + button_id + 'addEquipamentos').remove();
        //$("#addEquipamentos").hide();
    });

    /************** */
    /* assinatura  */
    /************** */

    $(function() {
        var urlAtual = '<?php echo current_url(); ?>';
        var idOs = '<?php echo $result->idOs; ?>';
        var assFlg = '<?php echo $result->signature; ?>';
        var padAssinatura = '';
        var Assinatura = '';

        var b4sign = {
            dataTable: null,
            //assinatura
            container: $('#signatureContainer').signature({
                color: '#0080FF'
            }),
            //assinatura cliente
            conteudo: $('#conteudo').signature({
                color: '#0080FF'
            }),


            initializeAssinatura: function() {
                //console.log(idOs);
                b4sign.load($(this).attr("idOs"));

            },
            disableContainer: function(selector) {
                var disable = $(selector).text() === 'Disable';
                $(selector).text(disable ? 'Enable' : 'Disable');
                this.container.signature(disable ? 'disable' : 'enable');
            },
            checkContainer: function() {
                if (this.container.signature('isEmpty')) {
                    alert("Signature Canvas Is Empty");
                    return true;
                }
                return false;
            },

            //salva assinatura
            formSave: function(formData) {
                formData.append("signature", b4sign.container.signature('toDataURL', 'image/jpeg'));
                $.ajax({
                    //url: "action.php",
                    //url: "http://signaturemysql.test:8081/sample/action.php",
                    url: "urlAtual",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(data) {
                        //alert(data.data.assinatura);
                        //alert(data.msg);
                        //b4sign.dataTable.ajax.reload();
                        console.log(urlAtual);

                        $("#assinarPadName").html("");
                        $("#assinarPad").html("");
                        $('#assinarPadNameCLIENTE').show();
                        $('#conteudotd').show();
                        $("#nomeAssinatura").html(data.data.nameAssinatura);
                        $("#docAssinatura").html(data.data.doc);
                        $("#dateAssinatura").html(data.data.data);
                        b4sign.conteudo.signature('draw', data.data.assinatura);
                        b4sign.conteudo.signature('disable');
                        location.reload();
                    }
                });
            },
            //carrega a imagem da assinatura
            load: function(person_ID) {
                $('#assinarPadNameCLIENTE').show();
                $('#conteudotd').show();
                $.ajax({
                    //url: "action.php",
                    //url: "http://signaturemysql.test:8081/sample/action.php",
                    url: "urlAtual",
                    method: 'POST',
                    data: {
                        action: "load",
                        id_Os: idOs
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data.data) {
                            //console.log(data.data);
                            $("#assinarPadName").html("");
                            $("#assinarPad").html("");
                            $("#nomeAssinatura").html(data.data.nameAssinatura);
                            $("#docAssinatura").html(data.data.doc);
                            $("#dateAssinatura").html(data.data.data);
                            b4sign.conteudo.signature('draw', data.data.assinatura);
                            b4sign.conteudo.signature('disable');
                        } else {
                            console.log(data.data);
                        }
                    }
                });
            },

        };
        if (assFlg) {
            b4sign.initializeAssinatura();
        }
        b4sign.container;

        $('#disable').click(function() {
            b4sign.disableContainer(this);
        });
        $('#clear').click(function() {
            b4sign.container.signature('clear');
        });

        $(document).on('click', '#loadSignature', function() {
            b4sign.load($(this).attr("data-id"));

        });

        $(document).on('click', '#submitSignature', function() {
            $('#action').val('submit');

        });
        $(document).on('click', '#updateSignature', function() {
            $('#action').val('update');
        });


        $(document).on('submit', '#signatureForm', function(event) {
            event.preventDefault();
            if (b4sign.checkContainer()) return;
            b4sign.formSave(new FormData(this));
        });
        window.addEventListener('scroll', function(e) {
            console.log('scrolling');
        });
    });

    /*****************adiciona produtos********************** */
    function calcLucro(precoCompra, margemLucro) {
        var precoVenda = (precoCompra * margemLucro / 100 + precoCompra).toFixed(2);
        return precoVenda;
    }
    $("#precoCompra").focusout(function() {
        if ($("#precoCompra").val() == '0.00' && $('#precoVenda').val() != '') {
            $('#errorAlert').text('Você não pode preencher valor de compra e depois apagar.').css("display", "inline").fadeOut(6000);
            $('#precoVenda').val('');
            $("#precoCompra").focus();
        } else {
            $('#precoVenda').val(calcLucro(Number($("#precoCompra").val()), Number($("#margemLucro").val())));
        }
    });

    $("#margemLucro").keyup(function() {
        this.value = this.value.replace(/[^0-9.]/g, '');
        if ($("#precoCompra").val() == null || $("#precoCompra").val() == '') {
            $('#errorAlert').text('Preencher valor da compra primeiro.').css("display", "inline").fadeOut(5000);
            $('#margemLucro').val('');
            $('#precoVenda').val('');
            $("#precoCompra").focus();

        } else if (Number($("#margemLucro").val()) >= 0) {
            $('#precoVenda').val(calcLucro(Number($("#precoCompra").val()), Number($("#margemLucro").val())));
        } else {
            $('#errorAlert').text('Não é permitido número negativo.').css("display", "inline").fadeOut(5000);
            $('#margemLucro').val('');
            $('#precoVenda').val('');
        }
    });

    $('#precoVenda').focusout(function() {
        if (Number($('#precoVenda').val()) < Number($("#precoCompra").val())) {
            $('#errorAlert').text('Preço de venda não pode ser menor que o preço de compra.').css("display", "inline").fadeOut(6000);
            $('#precoVenda').val('');
            if ($("#margemLucro").val() != "" || $("#margemLucro").val() != null) {
                $('#precoVenda').val(calcLucro(Number($("#precoCompra").val()), Number($("#margemLucro").val())));
            }
        }

    });

    $(document).ready(function() {
        $(".money").maskMoney();
        $.getJSON('<?php echo base_url() ?>assets/json/tabela_medidas.json', function(data) {
            for (i in data.medidas) {
                $('#unidade').append(new Option(data.medidas[i].descricao, data.medidas[i].sigla));
            }
        });
        $('#formAdicionaProduto').validate({
            rules: {
                descricao: {
                    required: true
                },
                unidade: {
                    required: true
                },
                precoCompra: {
                    required: true
                },
                precoVenda: {
                    required: true
                },
                estoque: {
                    required: true
                }
            },
            messages: {
                descricao: {
                    required: 'Campo Requerido.'
                },
                unidade: {
                    required: 'Campo Requerido.'
                },
                precoCompra: {
                    required: 'Campo Requerido.'
                },
                precoVenda: {
                    required: 'Campo Requerido.'
                },
                estoque: {
                    required: 'Campo Requerido.'
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
                    $("#divProdutosModalOs").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");

                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url(); ?>index.php/produtos/adicionarModalOs",
                        data: dados,
                        dataType: 'json',
                        success: function(data) {
                            if (data) {
                                $("#divProdutosModalOs").load("<?php echo current_url(); ?> #divProdutosModalOs");
                                $('.modal').modal('hide');
                                $("#codDeBarra").val(data.codDeBarra);
                                $("#idProduto").val(data.idProdutos);
                                $("#produto").val(data.descricao);
                                $("#estoque").val(data.estoque);
                                $("#preco").val(data.precoVenda);
                                $("#quantidade").focus();
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
    });
    /***************************************** */
    /************ADICIONAR SERVIÇO************* */
    $('#formAdicionaServico').validate({
        rules: {
            nome: {
                required: true
            },
            preco: {
                required: true
            }
        },
        messages: {
            nome: {
                required: 'Campo Requerido.'
            },
            preco: {
                required: 'Campo Requerido.'
            }
        },
        submitHandler: function(form) {

            var dados = $(form).serialize();
            $("#divServicosModalOs").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");

            $.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>index.php/servicos/adicionarModalOs",
                data: dados,
                dataType: 'json',
                success: function(data) {
                    if (data) {
                        $("#divServicosModalOs").load("<?php echo current_url(); ?> #divServicosModalOs");
                        $('.modal').modal('hide');

                        $("#idServico").val(data.idServico);
                        $("#servico").val(data.servico);
                        $("#preco_servico").val(data.preco_servico);
                        $("#quantidade_servico").focus();
                    } else {
                        Swal.fire({
                            type: "error",
                            title: "Atenção",
                            text: "Ocorreu um erro ao tentar adicionar Serviço."
                        });
                    }
                }
            });
            return false;

        }

    });
    /***************************************** */
    //****** */

    function valorParcelas() {
        if ($("#faturar-desconto").val() == 0) {
            var valor_parc = $("#valor_par").val();
        } else {
            var valor_parc = $("#faturar-desconto").val();
        }
        var qtdparc = $("#qtdparcelas_parc").val();
        var entrada = $("#entrada_pc").val();
        var result = (valor_parc - entrada) / qtdparc;

        if (qtdparc > 1) {

            if (entrada == 0) {
                $("#string_parc").text(qtdparc + ' parcelas de R$ ' + parseFloat(Math.round(result * 100) / 100).toFixed(2));
                $("#valorparcelas").val(parseFloat(Math.round(result * 100) / 100).toFixed(2));
            }

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

    $('#qtdparcelas').change(function(event) {
        var parcelas = $("#qtdparcelas").val();
        if (parcelas > 1) {
            $('#cancelar_nova_receita').trigger('click');
            $('#abrirmodalreceitaparcelada').trigger('click');
            $("#descricao_parc").val($("#descricao").val());
            $("#cliente_parc").val($("#cliente").val());
            $("#idCliente_parc").val($("#clientes_id").val());
            $("#tipo_parc").val($("#tipo").val());
            $("#formaPgto_parc").val($("#formaPgto").val());
            $("#pcontas_parc").val($("#pcontas").val());
            $("#categoria_parc").val($("#categoria").val());
            $("#observacoes_parc").val($("#observacoes").val());
            $("#valor_parc").val($("#valor").val());
            $("#desconto_parc").val($("#valor_desconto").val());
            $("#qtdparcelas_parc").val($("#qtdparcelas").val());
            valorParcelas();
        } else {
            if (parcelas == 1) {
                $('#cancelar_nova_receita').trigger('click');
                $('#abrirmodalreceitaparcelada').trigger('click');
                $("#descricao_parc").val($("#descricao").val());
                $("#cliente_parc").val($("#cliente").val());
                $("#idCliente_parc").val($("#clientes_id").val());
                $("#tipo_parc").val($("#tipo").val());
                $("#formaPgto_parc").val($("#formaPgto").val());
                $("#pcontas_parc").val($("#pcontas").val());
                $("#categoria_parc").val($("#categoria").val());
                $("#observacoes_parc").val($("#observacoes").val());
                $("#desconto_parc").val($("#valor_desconto").val());
                $("#valor_parc").val($("#valor").val());
                $("#qtdparcelas_parc").val(1);
                valorParcelas();
            }
        }
    });

    $('#valor_parc').keypress(function(event) {
        valorParcelas();
    });

    $('#qtdparcelas_parc').change(function(event) {
        valorParcelas();
    });

    $('#entrada_pc').keypress(function(event) {
        valorParcelas();
        var entrada = $("#entrada_pc").val();
        if (entrada > 0) {
            $('#dia_pgto').css("color", "#444444");
        } else {
            $('#dia_pgto').css("color", "#eeeeee");
        }
    });

    $('#valor_parc, #qtdparcelas_parc, #formaPgto_parc, #entrada, #dia_pgto, #dia_base_pgto').click(function(event) {
        valorParcelas();
    });

    $('#add_receita').mouseover(function(event) {
        valorParcelas();
    });

    $('#entrada_pc').keypress(function(event) {
        valorParcelas();
        var entrada = $("#entrada_pc").val();
        if (entrada > 0) {
            $('#dia_pgto').css("color", "#444444");
        } else {
            $('#dia_pgto').css("color", "#eeeeee");
        }
    });
    $('#valor_parc, #qtdparcela_parc, #formaPgto_parc, #entrada_pc, #dia_pgto, #dia_base_pgto').click(function(event) {
        valorParcelas();
    });

    $('#add_receita').mouseover(function(event) {
        valorParcelas();
    });
    //***** */
</script>