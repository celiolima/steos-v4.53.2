<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-edit"></i>
                </span>
                <h5>Editar Grupo Financeiro</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <?php echo $custom_error; ?>
                <form action="<?php echo current_url(); ?>" id="formGrupoFinanceiro" method="post" class="form-horizontal">
                    <input type="hidden" name="id" value="<?php echo $result->id; ?>" />
                        <div class="control-group">
                            <label for="nome" class="control-label">Nome do Grupo Financeiro<span class="required">*</span></label>
                            <div class="controls">
                                <input id="nome" type="text" name="nome" value="<?php echo $result->nome; ?>" required />
                            </div>
                        </div>

                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset3" style="display:flex;justify-content: center">
                                <button type="submit" class="button btn btn-primary"><span class="button__icon"><i class="bx bx-sync"></i></span><span class="button__text2">Atualizar</span></button>
                                <a href="<?php echo base_url() ?>index.php/grupofinanceiro" class="button btn btn-mini btn-warning" style="max-width: 160px"><span class="button__icon"><i class="bx bx-undo"></i></span><span class="button__text2">Voltar</span></a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
