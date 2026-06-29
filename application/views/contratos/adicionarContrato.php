<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/maskmoney.js"></script>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-file-signature"></i>
                </span>
                <h5>Adicionar Contrato</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <div class="span12" id="divProdutosServicos" style=" margin-left: 0">
                    <ul class="nav nav-tabs">
                        <li class="active" id="tabDetalhes"><a href="#tab1" data-toggle="tab">Detalhes do Contrato</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab1">
                            <div class="span12" id="divAdicionarContrato">
                                <?php if ($custom_error == true) { ?>
                                    <div class="span12 alert alert-danger" id="divInfo" style="padding: 1%;">Dados incompletos, verifique os campos com asterisco ou se selecionou corretamente cliente.</div>
                                <?php } ?>
                                <form action="<?php echo current_url(); ?>" method="post" id="formContrato">
                                    <div class="span12" style="padding: 1%">
                                        <div class="span4">
                                            <label for="cliente">Cliente<span class="required">*</span></label>
                                            <input id="cliente" class="span12" type="text" name="cliente" value="" placeholder="Digite o nome do cliente" />
                                            <input id="clientes_id" class="span12" type="hidden" name="clientes_id" value="" />
                                        </div>
                                        <div class="span3">
                                            <label for="tipoContrato">Tipo de Contrato<span class="required">*</span></label>
                                            <select class="span12" name="tipoContrato" id="tipoContrato" required>
                                                <option value="">Selecione...</option>
                                                <option value="Manutenção Preventiva s/ Peças">Manutenção Preventiva s/ Peças</option>
                                                <option value="Manutenção Preventiva c/ Peças">Manutenção Preventiva c/ Peças</option>
                                                <option value="Locação">Locação</option>
                                            </select>
                                        </div>
                                        <div class="span3">
                                            <label for="tecnico_id">Técnico Responsável</label>
                                            <select class="span12" name="tecnico_id" id="tecnico_id">
                                                <option value="">Selecione...</option>
                                                <?php if (isset($tecnicos)) { foreach ($tecnicos as $t) { if ($t->ativo == '1') {
                                                    echo '<option value="' . $t->idTecnicos . '">' . $t->nome . '</option>';
                                                } } } ?>
                                            </select>
                                        </div>
                                        <div class="span2">
                                            <label for="status">Status<span class="required">*</span></label>
                                            <select class="span12" name="status" id="status" required>
                                                <option value="Negociação">Negociação</option>
                                                <option value="Ativo">Ativo</option>
                                                <option value="Inativo">Inativo</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        <div class="span6">
                                            <label for="nomeContratos">Nome / Identificação do Contrato<span class="required">*</span></label>
                                            <input id="nomeContratos" class="span12" type="text" name="nomeContratos" value="" required placeholder="Ex: Contrato Manutenção Ar-Condicionado"/>
                                        </div>
                                        <div class="span3">
                                            <label for="dataInicial">Data Inicial<span class="required">*</span></label>
                                            <input id="dataInicial" autocomplete="off" class="span12 datepicker" type="text" name="dataInicial" value="<?php echo date('d/m/Y'); ?>" required />
                                        </div>
                                        <div class="span3">
                                            <label for="dataFinal">Data Final</label>
                                            <input id="dataFinal" autocomplete="off" class="span12 datepicker" type="text" name="dataFinal" value="" />
                                        </div>
                                    </div>

                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        <label for="descricaoContratos">Descrição / Termos</label>
                                        <textarea class="span12 editor" name="descricaoContratos" id="descricaoContratos" cols="30" rows="5"></textarea>
                                    </div>
                                    <div class="span12" style="padding: 1%; margin-left: 0; display: flex; justify-content: center; align-items: center;">
                                        <button class="button btn btn-success" id="btnContinuar" style="min-width: 150px; margin: 0 5px"><span class="button__icon"><i class='bx bx-chevrons-right'></i></span><span class="button__text2">Continuar</span></button>
                                        <a href="<?php echo base_url() ?>index.php/contratos" class="button btn btn-warning" style="min-width: 150px; margin: 0 5px"><span class="button__icon"><i class="bx bx-undo"></i></span><span class="button__text2">Voltar</span></a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('.money').maskMoney({prefix:'R$ ', allowNegative: false, thousands:'.', decimal:',', affixesStay: false});
        $('.datepicker').datepicker({
            dateFormat: 'dd/mm/yy'
        });

        $("#cliente").autocomplete({
            source: "<?php echo base_url(); ?>index.php/contratos/auto_complete_cliente",
            minLength: 1,
            select: function(event, ui) {
                $("#clientes_id").val(ui.item.id);
            }
        });

        $("#formContrato").validate({
            rules: {
                cliente: { required: true },
                tipoContrato: { required: true },
                nomeContratos: { required: true },
                dataInicial: { required: true },
                status: { required: true }
            },
            messages: {
                cliente: { required: 'Campo Requerido.' },
                tipoContrato: { required: 'Campo Requerido.' },
                nomeContratos: { required: 'Campo Requerido.' },
                dataInicial: { required: 'Campo Requerido.' },
                status: { required: 'Campo Requerido.' }
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
    });
</script>
