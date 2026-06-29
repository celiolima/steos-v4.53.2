<style>
    .ui-datepicker {
        z-index: 9999 !important;
    }
    .input-padrao {
        width: 100%;
        max-width: 500px;
        box-sizing: border-box;
    }
    @media (max-width: 767px) {
        .form-horizontal .control-group {
            margin-bottom: 15px;
            padding: 0 15px;
        }
        .form-horizontal .control-label {
            float: none;
            width: auto;
            text-align: left;
            margin-bottom: 5px;
            display: block;
            font-weight: bold;
        }
        .form-horizontal .controls {
            margin-left: 0 !important;
        }
        .input-padrao {
            max-width: 100% !important;
        }
        .form-actions .span6 {
            width: 100% !important;
            margin-left: 0 !important;
            display: flex;
            justify-content: center;
            gap: 10px;
        }
    }
</style>
<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-cubes"></i>
                </span>
                <h5>Adicionar Sistema</h5>
            </div>
            <div class="widget-content nopadding tab-content" style="padding-top: 15px;">
                <?php echo $custom_error; ?>
                <?php echo form_open(current_url(), ['id' => 'formSistema', 'class' => 'form-horizontal']); ?>
                    <div class="control-group">
                        <label for="nome" class="control-label">Nome<span class="required">*</span></label>
                        <div class="controls">
                            <input id="nome" class="input-padrao" type="text" name="nome" value="<?php echo set_value('nome'); ?>" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="descricao" class="control-label">Descrição</label>
                        <div class="controls">
                            <textarea id="descricao" class="input-padrao" name="descricao" rows="4"><?php echo set_value('descricao'); ?></textarea>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="preco" class="control-label">Preço Base<span class="required">*</span></label>
                        <div class="controls">
                            <input id="preco" class="money input-padrao" type="text" name="preco" value="<?php echo set_value('preco'); ?>" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="situacao" class="control-label">Situação<span class="required">*</span></label>
                        <div class="controls">
                            <select name="situacao" id="situacao" class="input-padrao">
                                <option value="1">Ativo</option>
                                <option value="0">Inativo</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset3" style="display:flex; justify-content: center; gap: 10px;">
                                <button type="submit" class="button btn btn-mini btn-success"><span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar</span></button>
                                <a href="<?php echo base_url() ?>index.php/sistemas" class="button btn btn-mini btn-warning"><span class="button__icon"><i class="bx bx-undo"></i></span> <span class="button__text2">Voltar</span></a>
                            </div>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $(".money").maskMoney();
        $('#formSistema').validate({
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
