<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </span>
                <h5>Cadastro de Serviço NFS-e</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <?php echo $custom_error; ?>
                <form action="<?php echo current_url(); ?>" id="formServicoNfse" method="post" class="form-horizontal">
                    <div class="control-group">
                        <label for="nome_servico" class="control-label">Nome do Serviço<span class="required">*</span></label>
                        <div class="controls">
                            <input id="nome_servico" type="text" name="nome_servico" class="span11" placeholder="Ex: Análise e Desenvolvimento de Sistemas (3,5%)" value="<?php echo set_value('nome_servico'); ?>" required />
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="codigo_servico_municipal" class="control-label">Cód. Serviço Municipal (LC 116)<span class="required">*</span></label>
                        <div class="controls">
                            <input id="codigo_servico_municipal" type="text" name="codigo_servico_municipal" class="span11" placeholder="Ex: 6203100 | 0101 - Desenvolvimento e licenciamento..." value="<?php echo set_value('codigo_servico_municipal'); ?>" required />
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="codigo_nbs" class="control-label">Código NBS (Padrão Nacional)</label>
                        <div class="controls">
                            <input id="codigo_nbs" type="text" name="codigo_nbs" class="span11" placeholder="Ex: 1.0101.10.00" value="<?php echo set_value('codigo_nbs'); ?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="codigo_tributacao_nacional" class="control-label">Cód. Tributação Nacional (cTribNac)</label>
                        <div class="controls">
                            <input id="codigo_tributacao_nacional" type="text" name="codigo_tributacao_nacional" class="span6" placeholder="Ex: 140101 para 14.01, 010101 para 0101 (vazio para 99.03)" value="<?php echo set_value('codigo_tributacao_nacional'); ?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="aliquota" class="control-label">Alíquota do ISS (%)<span class="required">*</span></label>
                        <div class="controls">
                            <input id="aliquota" class="money span4" data-affixes-stay="true" data-thousands="" data-decimal="," type="text" name="aliquota" placeholder="Ex: 3,50" value="<?php echo set_value('aliquota', '3,50'); ?>" required />
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="cst_ibs_cbs" class="control-label">CST IBS/CBS (Reforma Tributária)</label>
                        <div class="controls">
                            <input id="cst_ibs_cbs" type="text" name="cst_ibs_cbs" class="span4" placeholder="Ex: 000" value="<?php echo set_value('cst_ibs_cbs', '000'); ?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="class_trib_ibs_cbs" class="control-label">Classificação Tributária (cClassTrib)</label>
                        <div class="controls">
                            <input id="class_trib_ibs_cbs" type="text" name="class_trib_ibs_cbs" class="span4" placeholder="Ex: 000001" value="<?php echo set_value('class_trib_ibs_cbs', '000001'); ?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="ind_operacao" class="control-label">Código Indicador de Operação</label>
                        <div class="controls">
                            <?php $opCodes = isset($operation_indicator_codes) ? $operation_indicator_codes : asaas_operation_indicator_codes(); ?>
                            <?php $indOpSelected = set_value('ind_operacao'); ?>
                            <select id="ind_operacao" name="ind_operacao" class="span11">
                                <option value="">Não exigido / Automático (Serviços 14.01, 01.01, etc.)</option>
                                <?php foreach ($opCodes as $opCode => $opDesc) : ?>
                                    <?php $labelPrefix = ($opCode === '010106') ? '010106 - [Locação 99.03] ' : ($opCode . ' - '); ?>
                                    <option value="<?php echo htmlspecialchars($opCode, ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($indOpSelected === (string) $opCode) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($labelPrefix . $opDesc, ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset3" style="display:flex;justify-content: center">
                                <button type="submit" class="button btn btn-mini btn-success" style="max-width: 160px">
                                  <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar</span></a></button>
                                <a href="<?php echo base_url() ?>index.php/servicos_nfse" id="btnVoltar" class="button btn btn-mini btn-warning" style="max-width: 160px">
                                  <span class="button__icon"><i class="bx bx-undo"></i></span><span class="button__text2">Voltar</span></a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(".money").maskMoney();
        $('#formServicoNfse').validate({
            rules: {
                nome_servico: { required: true },
                codigo_servico_municipal: { required: true },
                aliquota: { required: true }
            },
            messages: {
                nome_servico: { required: 'Campo Requerido.' },
                codigo_servico_municipal: { required: 'Campo Requerido.' },
                aliquota: { required: 'Campo Requerido.' }
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
