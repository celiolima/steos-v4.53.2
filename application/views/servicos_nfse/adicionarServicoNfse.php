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
                        <label for="aliquota" class="control-label">Alíquota do ISS (%)<span class="required">*</span></label>
                        <div class="controls">
                            <input id="aliquota" class="money span4" data-affixes-stay="true" data-thousands="" data-decimal="," type="text" name="aliquota" placeholder="Ex: 3,50" value="<?php echo set_value('aliquota', '3,50'); ?>" required />
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
