<script src="<?php echo base_url() ?>assets/js/jquery.mask.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/funcoes.js"></script>

<style>
    /* Hiding the checkbox, but allowing it to be focused */
    .badgebox {
        opacity: 0;
    }
    .badgebox+.badge {
        /* Move the check mark away when unchecked */
        text-indent: -999999px;
        /* Makes the badge's width stay the same checked and unchecked */
        width: 27px;
    }
    .badgebox:focus+.badge {
        /* Set something to make the badge looks focused */
        /* This really depends on the application, in my case it was: */
        /* Adding a light boundary */
        box-shadow: inset 0px 0px 5px;
        /* Taking the difference out of the padding */
    }
    .badgebox:checked+.badge {
        /* Move the check mark back when checked */
        text-indent: 0;
    }
</style>
<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-car"></i>
                </span>
                <h5>Cadastro de Veículo</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <?php if ($custom_error != '') {
                    echo '<div class="alert alert-danger">' . $custom_error . '</div>';
                } ?>
                <form action="<?php echo current_url(); ?>" id="formVeiculo" method="post" class="form-horizontal">
                    
                    <div class="control-group">
                        <label for="nomeVeiculo" class="control-label">Nome do Veículo<span class="required">*</span></label>
                        <div class="controls">
                            <input id="nomeVeiculo" class="span8" type="text" name="nomeVeiculo" value="<?php echo set_value('nomeVeiculo'); ?>" placeholder="Ex: Fiat Uno 2012" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="autonomia" class="control-label">Autonomia (km/l)<span class="required">*</span></label>
                        <div class="controls">
                            <input id="autonomia" class="span4" type="number" step="0.1" name="autonomia" value="<?php echo set_value('autonomia'); ?>" placeholder="Ex: 12.5" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="observacoes" class="control-label">Observações</label>
                        <div class="controls">
                            <textarea id="observacoes" class="span8" name="observacoes" rows="4"><?php echo set_value('observacoes'); ?></textarea>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="situacao" class="control-label">Situação</label>
                        <div class="controls">
                            <label for="situacao" class="btn btn-default" style="margin-top: 5px;">Ativo
                                <input type="checkbox" id="situacao" name="situacao" class="badgebox" value="1" checked>
                                <span class="badge">&check;</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset3" style="display:flex">
                                <button type="submit" class="button btn btn-mini btn-success" style="max-width: 160px"><span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar</span></button>
                                <a href="<?php echo base_url() ?>index.php/veiculos" class="button btn btn-mini btn-warning"><span class="button__icon"><i class="bx bx-undo"></i></span><span class="button__text2">Voltar</span></a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#formVeiculo').validate({
            rules: {
                nomeVeiculo: {
                    required: true
                },
                autonomia: {
                    required: true,
                    number: true
                }
            },
            messages: {
                nomeVeiculo: {
                    required: 'Campo Requerido.'
                },
                autonomia: {
                    required: 'Campo Requerido.',
                    number: 'Insira um número válido.'
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
