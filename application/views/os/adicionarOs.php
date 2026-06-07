 <?php
    //echo "<pre>";
    //print_r($usuarios);
    //exit;
    ?>

 <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/datetimepicker/jquery.datetimepicker.css" />
 <script src="<?php echo base_url() ?>assets/datetimepicker/jquery.js"></script>
 <script src="<?php echo base_url() ?>assets/datetimepicker/build/jquery.datetimepicker.full.min.js"></script>


 <link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
 <script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>
 <script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>

 <link rel="stylesheet" href="<?php echo base_url() ?>assets/trumbowyg/ui/trumbowyg.css">
 <script type="text/javascript" src="<?php echo base_url() ?>assets/trumbowyg/trumbowyg.js"></script>
 <script type="text/javascript" src="<?php echo base_url() ?>assets/trumbowyg/langs/pt_br.js"></script>

 <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css" />
 <style type="text/css">
     /* autocomplete*/
     .ui-autocomplete-row {
         padding: 8px;
         background-color: #f4f4f4;
         border-bottom: 1px solid #ccc;
         font-weight: bold;
     }

     .ui-autocomplete-row:hover {
         background-color: #ddd;
     }

     /* fim autocomplete*/
 </style>

 <div class="row-fluid" style="margin-top:0">
     <div class="span12">
         <div class="widget-box">
             <div class="widget-title">
                 <h5>Cadastro de OS</h5>
             </div>
             <div class="widget-content nopadding tab-content">
                 <div class="span12" id="divProdutosServicos" style=" margin-left: 0">

                     <ul class="nav nav-tabs">
                         <li class="active" id="tabDetalhes"><a href="#tab1" data-toggle="tab">Detalhes da OS</a></li>
                     </ul>
                     <div class="tab-content">
                         <div class="tab-pane active" id="tab1">
                             <div class="span12" id="divCadastrarOs">
                                 <?php if ($custom_error == true) { ?>
                                     <div class="span12 alert alert-danger" id="divInfo">Dados incompletos, verifique os campos com asterisco ou se selecionou corretamente cliente, responsável e garantia.<br />Ou se tem um cliente e um termo de garantia cadastrado.</div>
                                 <?php } ?>
                                 <form action="<?php echo current_url(); ?>" method="post" id="formOs">
                                     <!--  PRIMEIRA LINHA-->
                                     <div class="span12" style="padding: 1%; margin-left: 0">
                                         <!--  CLIENTE -->
                                         <div class="span6" style="margin-right: 0">
                                             <label for="cliente">Cliente<span class="required">*</span></label>
                                             <input id="cliente" class="span12" autocomplete="off" style="padding: 1; margin-right: 0" type="text" name="cliente" value="" />
                                             <input id="clientes_id" class="span12" type="hidden" name="clientes_id" value="" />
                                             <!--  <a class="btn btn-primary span2" style="padding: 0;margin-left: 0" href="#" role="button">Link</a> -->
                                         </div>
                                         <!--  OPERADOR -->
                                         <div class="span3">
                                             <label for="tecnico">Operador<span class="required">*</span></label>
                                             <input id="tecnico" class="span12" type="text" name="tecnico" value="<?= $this->session->userdata('nome_admin'); ?>" />
                                             <input id="usuarios_id" class="span12" type="hidden" name="usuarios_id" value="<?= $this->session->userdata('id_admin'); ?>" />
                                         </div>
                                         <!--  TECNICO -->
                                         <div class="span3">
                                             <div id="dynamic_field_tecnico">
                                                 <label class="control-label" required>Tecnico</span></label>
                                                 <select name="tecnico[]" id="tecnico" style="padding: 0; margin: 0">
                                                     <?php foreach ($tecnicos as $tecnico) {
                                                            if ($tecnico->ativo == "1") {
                                                                echo '<option value="' . $tecnico->idTecnicos . '">' . $tecnico->nome . '</option>';
                                                            }
                                                        } ?>
                                                 </select>
                                                 <button style=" margin: 0" class="btn btn-success " type="button" name="add_tecnico" id="add_tecnico">+</button>
                                             </div>
                                         </div>
                                     </div>
                                     <!--  SEGUNDA LINHA-->
                                     <div class="span12" style="padding: 1%; margin-left: 0">
                                         <!--  STATUS -->
                                         <div class="span3">
                                             <label for="status">Status<span class="required">*</span></label>
                                             <select class="span12" name="status" id="status" value="">
                                                 <option value="A Sair | Aguard Conclusão">A Sair | Aguard Conclusão</option>
                                                 <option value="Manutenção Preventiva">Manutenção Preventiva</option>
                                                 <option value="Orçamento">Orçamento</option>
                                                 <option value="Aprovado">Aprovado</option>
                                                 <option value="Em Andamento">Em Andamento</option>
                                                 <option value="Finalizado">Finalizado</option>
                                                 <option value="Cancelado">Cancelado</option>
                                                 <option value="Aguardando Peças">Aguardando Peças</option>

                                             </select>
                                             <label for="local">Local <span class="required">*</span></label>
                                             <select class="span12" name="local" id="local" value="">
                                                 <option value="Externo">Externo</option>
                                                 <option value="Interno">Interno</option>
                                             </select>
                                         </div>
                                         <!--  DATA INICIAL -->
                                         <div class="span3">
                                             <label for="dataInicial">Data Inicial<span class="required">*</span></label>
                                             <!-- <input id="dataInicial" autocomplete="off" class="span12 datepicker" type="text" name="dataInicial" value="<?php echo date('d/m/Y'); ?>" /> -->
                                             <input id="dataInicial" autocomplete="off" class="span12 " type="text" name="dataInicial" value="<?php echo date('d/m/Y H:i'); ?>">
                                             <label for="tipo">Tipo<span class="required">*</span></label>
                                             <select class="span12" name="tipo" id="tipo" value="">
                                                 <option value="Avulso">Avulso</option>
                                                 <option value="Contrato">Contrato</option>
                                             </select>
                                         </div>
                                         <!--  DATA FINAL -->
                                         <div class="span3">
                                             <label for="dataFinal">Data Final</span></label>
                                             <!-- <input id="dataFinal" autocomplete="off" class="span12 datepicker" type="text" name="dataFinal" value="<?php echo date('d/m/Y'); ?>" /> -->
                                             <input id="dataFinal" autocomplete="off" class="span12 " type="text" name="dataFinal" value="<?php echo date('d/m/Y H:i'); ?>">

                                         </div>
                                         <!--  GARANTIA -->
                                         <div class="span3">
                                             <label for="garantia">Garantia (dias)</label>
                                             <input id="garantia" type="number" placeholder="Status s/g inserir nº/0" min="0" max="9999" class="span12" name="garantia" value="" />
                                             <?php echo form_error('garantia'); ?>
                                             <label for="termoGarantia">Termo Garantia</label>
                                             <input id="termoGarantia" class="span12" type="text" name="termoGarantia" value="" />
                                             <input id="garantias_id" class="span12" type="hidden" name="garantias_id" value="" />
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
                                                 <!--  EQUIPAMENTOS -->
                                                 <!--       <table class="table table-bordered">
                                                     <thead>
                                                         <tr>
                                                             <td>
                                                                 <strong id="equipamentos_label">                                                                    
                                                                     <input id="equipamento_id" class="span12" name="equipamento_id" type="hidden" value="teste" />
                                                                 </strong>
                                                             </td>
                                                             <td>
                                                             </td>
                                                         </tr>
                                                     </thead>
                                                     <tbody>
                                                         <tr>
                                                             <td colspan="7" style="text-align: left; ">Numero de Serie:<a id="serie" style="color: green">&nbsp</a></td>
                                                             <input id="serie_value" class="span12" type="hidden" name="serie_value" value="" />

                                                             <td colspan="7" style="text-align: left; ">Modelo:<a id="modelo" style="color: green">&nbsp</a></td>
                                                             <input id="modelo_value" class="span12" type="hidden" name="modelo_value" value="" />
                                                         </tr>
                                                         <tr>
                                                             <td colspan="7" style="text-align: left; ">Cor:<a id="cor" style="color: green">&nbsp</a></td>
                                                             <input id="cor_value" class="span12" type="hidden" name="cor_value" value="" />
                                                             <td colspan="7" style="text-align: left; ">Descricao:<a id="descricao" style="color: green">&nbsp</a></td>
                                                             <input id="descricaoProduto" class="span12" type="hidden" name="descricaoProduto" value="" />
                                                         </tr>
                                                         <tr>
                                                             <td colspan="7" style="text-align: left; ">Potência:<a id="potencia" style="color: green">&nbsp</a></td>
                                                             <input id="potencia_value" class="span12" type="hidden" name="potencia_value" value="" />
                                                             <td colspan="7" style="text-align: left; ">Voltagem:<a id="voltagem" style="color: green">&nbsp</a></td>
                                                             <input id="voltagem_value" class="span12" type="hidden" name="voltagem_value" value="" />
                                                         </tr>
                                                         <tr>
                                                             <td colspan="7" style="text-align: left; ">Marcas:<a id="marcas" style="color: green">&nbsp</a></td>
                                                             <input id="marcas_value" class="span12" type="hidden" name="marcas_value" value="" />
                                                             <td colspan="7" style="text-align: left; ">Local:<a id="local" style="color: green">&nbsp</a></td>
                                                             <input id="local_value" class="span12" type="hidden" name="local_value" value="" />

                                                         </tr>
                                                         <tr>
                                                             <td colspan="7" style="text-align: left; ">
                                                                 <label>Defeito Reclamado</label>
                                                                 <textarea rows="5" cols="20" name="_equipamento" id="_equipamento" placeholder="insira o defeito reclamado" style="width:100%"></textarea>
                                                             </td>
                                                             <td colspan="7" style="text-align: left; ">
                                                                 <label> Defeito Encontrado</label>
                                                                 <textarea name="defeito_encontrado" id="defeito_encontardo" placeholder="insira o defeito encontrado" cols="20" rows="5" style="width:100%"></textarea>
                                                             </td>
                                                         </tr>
                                                     </tbody>
                                                 </table>
                                                 <button type="button" name="remove" id="01'" class="btn btn-danger btn_remove">X</button>
                                             -->
                                             </div>

                                         </div>
                                     </div>
                                     <!-- 
                                    <a href="#modal-equipamentos" id="btn-faturar" role="button" data-toggle="modal" class="button btn btn-mini btn-danger">
                                        <span class="button__icon"><i class='bx bx-dollar'></i></span> <span class="button__text">Faturar</span></a>
                                     -->
                                     <!-- <div class="span6" style="padding: 1%; margin-left: 0">
                                        <label for="descricaoProduto">
                                            <h4>Descrição Produto/Serviço</h4>
                                        </label>
                                        <textarea class="span12 editor" name="descricaoProduto" id="descricaoProduto" cols="30" rows="5"></textarea>
                                    </div>-->

                                     <div class="span6" style="padding: 1%; margin-left: 0">
                                         <label>Defeito Reclamado Atendimento</label>
                                         <textarea rows="5" cols="20" name="defeito_atendimento" id="defeito_atendimento" placeholder="insira o defeito reclamado no Atendimento" style="width:100%"></textarea>

                                         <!-- <label for="defeito"> Defeito</label>
                                        <textarea class="span12 editor" name="defeito" id="defeito" cols="30" rows="5"></textarea> -->
                                     </div>
                                     <div class="span6" style="padding: 1%; margin-left: 0">
                                         <label>Defeito Encontrado no Atendimento</label>
                                         <textarea rows="5" cols="20" name="defeito_encontrado_no_atendimento" id="defeito_encontrado_no_atendimento" placeholder="insira o defeito encontrado no Atendimento" style="width:100%"></textarea>

                                         <!-- <label for="defeito"> Defeito</label>
                                        <textarea class="span12 editor" name="defeito" id="defeito" cols="30" rows="5"></textarea> -->
                                     </div>

                                     <div class="span6" style="padding: 1%; margin-left: 0">
                                         <label for="observacoes">
                                             <h4>Observações</h4>
                                         </label>
                                         <textarea class="span12 editor" name="observacoes" id="observacoes" cols="30" rows="5"></textarea>
                                     </div>
                                     <div class="span6" style="padding: 1%; margin-left: 0">
                                         <label for="laudoTecnico">
                                             <h4>Laudo Técnico</h4>
                                         </label>
                                         <textarea class="span12 editor" name="laudoTecnico" id="laudoTecnico" cols="30" rows="5"></textarea>
                                     </div>
                                     <div class="span12" style="padding: 1%; margin-left: 0">
                                         <div class="span6 offset3" style="display:flex">
                                             <button class="button btn btn-success" id="btnContinuar">
                                                 <span class="button__icon"><i class='bx bx-chevrons-right'></i></span><span class="button__text2">Continuar</span></button>
                                             <a href="<?php echo base_url() ?>index.php/os" class="button btn btn-mini btn-warning" style="max-width: 160px">
                                                 <span class="button__icon"><i class="bx bx-undo"></i></span><span class="button__text2">Voltar</span></a>
                                         </div>
                                     </div>
                                 </form>
                             </div>
                         </div>
                     </div>
                 </div>
                 .
             </div>
         </div>
     </div>
 </div>
 <!-- Modal adiciona cliete-->
 <div id="modal-adicionaclietes" class="modal hide fade " tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

 </div>
 <!--fim Modal adiciona clietes-->

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
 <!-- Função de adiciona estrutura html-->
 <script type="text/javascript" src="<?php echo base_url() ?>assets/js/adicionaHtml.js"></script>
 <script type="text/javascript">
     var i = 1; //adciciomna conteudo equipamentos
     var i1 = 1; //adciciomna conteudo tecnicos

     $(document).ready(function() {
         $('#addEquipamentos').hide();
     });

     function abreModal() {
         $("#modal-adicionaEquipamentos").modal({
             show: true
         });
     }
     $(document).ready(function() {
         $("#cliente").autocomplete({
             source: "<?php echo base_url(); ?>index.php/os/autoCompleteCliente",
             minLength: 1,
             select: function(event, ui) {
                 $("#clientes_id").val(ui.item.id);
                 console.log(ui.item.id);

             },
             open: function(event, ui) {

                 //$('.ui-autocomplete').append('<li><a href="javascript:alert(\'redirecting...\')">See All Result</a></li>'); //See all results
                 $('.ui-autocomplete').append('<li><a href="<?php echo base_url(); ?>index.php/clientes/adicionar" ><button type="button" class="btn btn-primary"><span class="button__icon"><i class="bx bx-plus-circle"></i></span><span class="button__text2">Cliente / Fornecedor </button></a></li > ');
             }
         });

         $("#tecnico").autocomplete({
             source: "<?php echo base_url(); ?>index.php/os/autoCompleteUsuario",
             minLength: 1,
             select: function(event, ui) {
                 $("#usuarios_id").val(ui.item.id);
             }
         });
         $("#termoGarantia").autocomplete({
             source: "<?php echo base_url(); ?>index.php/os/autoCompleteTermoGarantia",
             minLength: 1,
             select: function(event, ui) {
                 $("#garantias_id").val(ui.item.id);
             }
         });

         $("#equipamentos").autocomplete({
             source: "<?php echo base_url(); ?>index.php/os/autoCompleteEquipamentos",
             minLength: 1,
             select: function(event, ui) {
                 console.log(ui.item);
                 addEquipamentosAutocomplete(i, ui.item);
             },
             open: function(event, ui) {
                 //$('.ui-autocomplete').append('<li><a href="javascript:alert(\'redirecting...\')">See All Result</a></li>'); //See all results
                 $('.ui-autocomplete').append('<li><a href="javascript:abreModal()" ><button type="button" class="btn btn-primary"><span class="button__icon"><i class="bx bx-plus-circle"></i></span><span class="button__text2">Equipamentos</button></a></li > ');
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
                 },
                 dataFinal: {
                     required: true
                 },
                 local: {
                     required: true
                 },
                 tipo: {
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
                 },
                 dataFinal: {
                     required: 'Campo Requerido.'
                 },
                 local: {
                     required: 'Campo Requerido.'
                 },
                 tipo: {
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
         /*  $(".datepicker").datepicker({
              dateFormat: 'dd/mm/yy'
          }); */
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

         //adciciomna conteudo tecnicos
         $('#add_tecnico').click(function() {
             i1++;
             $('#dynamic_field_tecnico').append(
                 '<select name="tecnico[]" id="tecnico' + i1 + '" style="padding: 0; margin: 0"><option value=""></option><?php foreach ($tecnicos as $tecnico) {
                                                                                                                                echo '<option value="' . $tecnico->idTecnicos . '">' . $tecnico->nome . '</option>';
                                                                                                                            } ?></select><button style=" margin: 0" class="btn btn-danger btn_remover_tecnico" type="button" name="add_tecnico" id="' + i1 + '" >x</button>'
                 //'<tr id="row' + i + '"><td><input type="text" name="name[]" placeholder="Enter your Name" class="form-control name_list" /></td><td><button type="button" name="btn_remover_tecnico" id="' + i + '" class="btn btn-danger btn_remove">X</button></td></tr>'
                 //'<div id="row' + i + '"><div class="span12" style="padding:1%; border: 2px solid #ECF0F1;border-radius: 5px; "> 	<div id="busca"> 		<input id="equipamentos' + i + '" class="span12 pull-right" autocomplete="off" style="padding: 1; margin-right: 0" type="text" name="equipamentos" placeholder="Pesquise por Equipamento, Série ou Modelo " value="" /> 		<input id="equipamentos_id' + i + '" class="span12" type="hidden" name="equipamentos_id" value="" /> 	</div> </div><button type="button" name="remove" id="' + i + '" class="btn  btn_remove">X</button></div>'
             );
         });
         $(document).on('click', '.btn_remover_tecnico', function() {
             var button_id = $(this).attr("id");
             $('#' + button_id + '').remove();
             $('#tecnico' + button_id + '').remove();
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
         /*  $('#submit').click(function() {
              $.ajax({
                  url: "name.php",
                  method: "POST",
                  data: $('#add_name').serialize(),
                  success: function(data) {
                      alert(data);
                      $('#add_name')[0].reset();
                  }
              });
          }); */
     });
 </script>