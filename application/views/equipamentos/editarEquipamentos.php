<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-shopping-bag"></i>
                </span>
                <h5>Editar Equipamento</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <?php echo $custom_error; ?>
                <form action="<?php echo current_url(); ?>" id="formEquipamentos" method="post" class="form-horizontal">
                    <?php echo form_hidden('idEquipamentos', $result->idEquipamentos) ?>
                    <div class="widget-content nopadding tab-content">
                        <div class="span6">
                            <div class="control-group">
                                <label for="tipoEquipamento" class="control-label">Tipo Equipamento<span class=""></span></label>
                                <div class="controls">
                                    <input id="tipoEquipamento" type="text" name="tipoEquipamento" value="<?php echo $result->equipamento; ?>" />
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="descricao" class="control-label">Descrição<span class="required">*</span></label>
                                <div class="controls">
                                    <input id="descricao" type="text" name="descricao" value="<?php echo $result->descricao; ?>" />
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="modelo" class="control-label">Modelo<span class="required">*</span></label>
                                <div class="controls">
                                    <input id="modelo" type="text" name="modelo" value="<?php echo $result->modelo; ?>" />
                                </div>
                            </div>
                            <div class="control-group" class="control-label">
                                <label for="marcas" class="control-label">Marcas</label>
                                <div class="controls">
                                    <select id="marcas" name="marcas">
                                        <option value="">Selecione...</option>
                                        <?php
                                        foreach ($marcas as $m) {
                                            $selected = ($result->marcas == $m->marca) ? 'selected' : '';
                                            echo '<option value="' . $m->marca . '" ' . $selected . '>' . $m->marca . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="serial" class="control-label">Serial<span class="required">*</span></label>
                                <div class="controls">
                                    <input id="serial" type="text" name="serial" value="<?php echo $result->num_serie; ?>" />
                                </div>
                            </div>
                        </div>

                        <div class="span6">
                            <div class="control-group">
                                <label for="cor" class="control-label">Cor<span class="required">*</span></label>
                                <div class="controls">
                                    <input id="cor" type="text" name="cor" value="<?php echo $result->cor; ?>" />
                                </div>
                            </div>
                            <div class="control-group" class="control-label">
                                <label for="voltagem" class="control-label">Voltagem</label>
                                <div class="controls">
                                    <select id="voltagem" name="voltagem">
                                        <option value="">Selecione...</option>
                                        <?php 
                                        $voltagens = ['110v', '220v', 'Trifasica', 'bivolt', 'Automática', '48v', '24v', '12v', '9v', '5v', '3,3v'];
                                        foreach($voltagens as $v) {
                                            $selected = ($result->voltagem == $v) ? 'selected' : '';
                                            echo '<option value="' . $v . '" ' . $selected . '>' . $v . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="potencia" class="control-label">Potência<span class="required">*</span></label>
                                <div class="controls">
                                    <input id="potencia" type="text" name="potencia" value="<?php echo $result->potencia; ?>" />
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset3" style="display: flex;justify-content: center">
                                <button type="submit" class="button btn btn-primary" style="max-width: 160px">
                                  <span class="button__icon"><i class="bx bx-sync"></i></span><span class="button__text2">Atualizar</span></button>
                                <a href="<?php echo base_url() ?>index.php/equipamentos" id="" class="button btn btn-mini btn-warning">
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
