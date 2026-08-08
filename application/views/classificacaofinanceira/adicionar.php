<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-plus"></i>
                </span>
                <h5>Adicionar Classificação Financeira</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <?php echo $custom_error; ?>
                <form action="<?php echo current_url(); ?>" id="formClassificacaoFinanceira" method="post" class="form-horizontal">
                        <div class="control-group">
                            <label for="nomeClassFin" class="control-label">Nome da Classificação<span class="required">*</span></label>
                            <div class="controls">
                                <input id="nomeClassFin" type="text" name="nomeClassFin" value="<?php echo set_value('nomeClassFin'); ?>" required />
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="grupoFinaceiro" class="control-label">Grupo Financeiro<span class="required">*</span></label>
                            <div class="controls">
                                <input id="grupoFinaceiro" type="text" name="grupoFinaceiro" value="<?php echo set_value('grupoFinaceiro'); ?>" required />
                            </div>
                        </div>

                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset3" style="display:flex;justify-content: center">
                                <button type="submit" class="button btn btn-mini btn-success" style="max-width: 160px"><span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar</span></button>
                                <a href="<?php echo base_url() ?>index.php/classificacaofinanceira" class="button btn btn-mini btn-warning" style="max-width: 160px"><span class="button__icon"><i class="bx bx-undo"></i></span><span class="button__text2">Voltar</span></a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
