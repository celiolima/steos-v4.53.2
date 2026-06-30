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

        /* Adding a light border */
        box-shadow: inset 0px 0px 5px;
        /* Taking the difference out of the padding */
    }

    .badgebox:checked+.badge {
        /* Move the check mark back when checked */
        text-indent: 0;
    }
</style>
<?php
/* echo "<pre>";
print_r($marcas);
exit; */
?>
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
                        <div class="span6">
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
                            <div class="control-group">
                                <label for="marcas" class="control-label">Marcas</label>
                                <div class="controls">
                                    <input id="marcas" type="text" name="marcas" value="<?php echo set_value('marcas'); ?>" placeholder="Digite a marca" />
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="serial" class="control-label">Serial<span class="required">*</span></label>
                                <div class="controls">
                                    <input id="serial" type="text" name="serial" value="<?php echo set_value('serial'); ?>" />
                                </div>
                            </div>
                        </div>

                        <div class="span6">
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
                                        <option value="110v">110v</option>
                                        <option value="220v">220v</option>
                                        <option value="Trifasica">Trifasica</option>
                                        <option value="bivolt">bivolt</option>
                                        <option value="Automática">Automática</option>
                                        <option value="bivolt">bivolt</option>
                                        <option value="48v">48v</option>
                                        <option value="24v">24v</option>
                                        <option value="12v">12v</option>
                                        <option value="9v">9v</option>
                                        <option value="5v">5v</option>
                                        <option value="3,3v">3,3v</option>

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

<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#formEquipamentos').validate({
            rules: {
                tipoEquipamento: { required: true },
                descricao: { required: true },
                modelo: { required: true },
                serial: { required: true },
                cor: { required: true },
                potencia: { required: true }
            },
            messages: {
                tipoEquipamento: { required: 'Campo Requerido.' },
                descricao: { required: 'Campo Requerido.' },
                modelo: { required: 'Campo Requerido.' },
                serial: { required: 'Campo Requerido.' },
                cor: { required: 'Campo Requerido.' },
                potencia: { required: 'Campo Requerido.' }
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