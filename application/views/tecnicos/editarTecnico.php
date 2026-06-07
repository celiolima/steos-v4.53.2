<script src="<?php echo base_url() ?>assets/js/jquery.mask.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/funcoes.js"></script>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-user"></i>
                </span>
                <h5>Editar Tecnicos</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <?php if ($custom_error != '') {
                    echo '<div class="alert alert-danger">' . $custom_error . '</div>';
                } ?>
                <form action="<?php echo current_url(); ?>" id="formTecnicos" method="post" class="form-horizontal">
                    <div class="control-group">
                        <?php echo form_hidden('idTecnico', $result->idTecnicos) ?>
                        <label for="nome" class="control-label">Nome<span class="required">*</span></label>
                        <div class="controls">
                            <input id="nome" type="text" name="nome" value="<?php echo $result->nome; ?>" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="rg" class="control-label">Comissão de serviços<span class="required">*</span></label>
                        <div class="controls">
                            <input id="comissao_servico" type="text" name="comissao_servico" value="<?php echo $result->comissao_servico; ?>" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="cpf" class="control-label">Comissão de Produto<span class="required">*</span></label>
                        <div class="controls">
                            <input class="comissao_produto" type="text" name="comissao_produto" value="<?php echo $result->comissao_produto; ?>" />
                        </div>

                        <!--DATA-->
                        <div class="control-group">
                            <label for="dataExpiracao" class="control-label">Expira em<span class="required">*</span></label>
                            <div class="controls">
                                <input id="dataExpiracao" type="date" name="dataExpiracao" value="<?php echo $result->dataExpiracao; ?>" />
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Situação*</label>
                            <div class="controls">
                                <select name="situacao" id="situacao">
                                    <?php if ($result->ativo == 1) {
                                        $ativo = 'selected';
                                        $inativo = '';
                                    } else {
                                        $ativo = '';
                                        $inativo = 'selected';
                                    } ?>
                                    <option value="1" <?php echo $ativo; ?>>Ativo</option>
                                    <option value="0" <?php echo $inativo; ?>>Inativo</option>
                                </select>
                            </div>
                        </div>


                        <div class="form-actions">
                            <div class="span12">
                                <div class="span6 offset3" style="display:flex">
                                    <button type="submit" class="button btn btn-primary">
                                        <span class="button__icon"><i class="bx bx-sync"></i></span><span class="button__text2">Atualizar</span></button>
                                    <a href="<?php echo base_url() ?>index.php/tecnicos" id="" class="button btn btn-mini btn-warning">
                                        <span class="button__icon"><i class="bx bx-undo"></i></span> <span class="button__text">Voltar</span></a>
                                </div>
                            </div>
                        </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script type="text/javascript">
    $(document).ready(function() {

        $('#formTecnicos').validate({
            rules: {
                nome: {
                    required: true
                },
                dataExpiracao: {
                    required: true
                },
                comissao_servico: {
                    required: true
                },
                comissao_produto: {
                    required: true
                },
            },
            messages: {
                nome: {
                    required: 'Campo Requerido.'
                },
                dataExpiracao: {
                    required: 'Campo Requerido.'
                },
                comissao_servico: {
                    required: 'Campo Requerido.'
                },
                comissao_produto: {
                    required: 'Campo Requerido.'
                },
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