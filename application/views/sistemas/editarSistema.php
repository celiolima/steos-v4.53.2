<style>
    .ui-datepicker {
        z-index: 9999 !important;
    }
    .input-padrao {
        width: 100%;
        max-width: 500px;
        box-sizing: border-box;
    }
    /* ── Responsividade Mobile ── */
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
        #formChecklist .flex-container {
            flex-direction: column !important;
            align-items: stretch !important;
        }
        #formChecklist .flex-btn {
            width: 100% !important;
        }
        #formChecklist .flex-btn button {
            width: 100% !important;
            justify-content: center;
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
                <h5>Editar Sistema</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <?php echo $custom_error; ?>
                <ul class="nav nav-tabs">
                    <li class="active" id="tabDetalhes"><a href="#tab1" data-toggle="tab">Detalhes do Sistema</a></li>
                    <li id="tabChecklist"><a href="#tab2" data-toggle="tab">Checks</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab1" style="padding-top: 15px;">
                        <?php echo form_open(current_url(), ['id' => 'formSistema', 'class' => 'form-horizontal']); ?>
                            <?php echo form_hidden('idSistemas', $result->idSistemas) ?>
                            <div class="control-group">
                                <label for="nome" class="control-label">Nome<span class="required">*</span></label>
                                <div class="controls">
                                    <input id="nome" class="input-padrao" type="text" name="nome" value="<?php echo $result->nome; ?>" />
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="descricao" class="control-label">Descrição</label>
                                <div class="controls">
                                    <textarea id="descricao" class="input-padrao" name="descricao" rows="4"><?php echo $result->descricao; ?></textarea>
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="preco" class="control-label">Preço Base<span class="required">*</span></label>
                                <div class="controls">
                                    <input id="preco" class="money input-padrao" type="text" name="preco" value="<?php echo number_format($result->preco, 2, '.', ''); ?>" />
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="situacao" class="control-label">Situação<span class="required">*</span></label>
                                <div class="controls">
                                    <select name="situacao" id="situacao" class="input-padrao">
                                        <option value="1" <?php if ($result->situacao == 1) echo 'selected'; ?>>Ativo</option>
                                        <option value="0" <?php if ($result->situacao == 0) echo 'selected'; ?>>Inativo</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-actions">
                                <div class="span12">
                                    <div class="span6 offset3" style="display:flex; justify-content: center; gap: 10px;">
                                        <button type="submit" class="button btn btn-primary"><span class="button__icon"><i class="bx bx-sync"></i></span><span class="button__text2">Atualizar</span></button>
                                        <a href="<?php echo base_url() ?>index.php/sistemas" class="button btn btn-mini btn-warning"><span class="button__icon"><i class="bx bx-undo"></i></span> <span class="button__text2">Voltar</span></a>
                                    </div>
                                </div>
                            </div>
                        <?php echo form_close(); ?>
                    </div>

                    <div class="tab-pane" id="tab2" style="padding-top: 15px;">
                        <div class="span12 well" style="padding: 15px; margin-left: 0; box-sizing: border-box;">
                            <form id="formChecklist" method="post" style="margin: 0;">
                                <input type="hidden" name="sistemas_id" id="sistemas_id" value="<?php echo $result->idSistemas; ?>" />
                                <label for="descricao_check" style="margin-bottom: 6px; display: block; font-weight: bold;">Descrição do Check</label>
                                <div class="flex-container" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                    <div style="flex: 1; min-width: 200px;">
                                        <input type="text" name="descricao" id="descricao_check" placeholder="Ex: Verificar tensão da central, limpar conectores..." style="width: 100%; height: 32px; margin: 0; box-sizing: border-box;" />
                                    </div>
                                    <div class="flex-btn" style="flex: 0 0 auto;">
                                        <button type="submit" class="button btn btn-success" id="btnAdicionarChecklist" style="height: 32px; margin: 0; white-space: nowrap; display: inline-flex; align-items: center;">
                                            <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="span12" id="divChecklist" style="margin-left: 0; margin-top: 15px;">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: left; vertical-align: middle;">Descrição do Check</th>
                                            <th style="text-align: center; vertical-align: middle; width: 100px;">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!$checks) {
                                            echo '<tr><td colspan="2" style="text-align: center; padding: 15px;">Nenhum check cadastrado para este sistema.</td></tr>';
                                        } else {
                                            foreach ($checks as $c) {
                                                echo '<tr>';
                                                echo '<td style="vertical-align: middle;">' . htmlspecialchars($c->descricao) . '</td>';
                                                echo '<td style="text-align: center; vertical-align: middle;"><a href="" idAcao="' . $c->idSistemas_checks . '" title="Excluir" class="btn-nwe4 btnExcluirCheck"><i class="bx bx-trash-alt"></i></a></td>';
                                                echo '</tr>';
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
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
        $(".money").maskMoney();
        $('#formSistema').validate({
            rules: {
                nome: { required: true },
                preco: { required: true }
            },
            messages: {
                nome: { required: 'Campo Requerido.' },
                preco: { required: 'Campo Requerido.' }
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

        $('#formChecklist').submit(function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: '<?php echo base_url(); ?>index.php/sistemas/adicionarCheck',
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.result) {
                        $("#divChecklist").load("<?php echo current_url(); ?> #divChecklist > *");
                        $("#descricao_check").val('');
                    } else {
                        Swal.fire({ type: "error", title: "Atenção", text: response.messages });
                    }
                }
            });
        });

        $(document).on('click', '.btnExcluirCheck', function(e) {
            e.preventDefault();
            var id = $(this).attr('idAcao');
            $.ajax({
                url: '<?php echo base_url(); ?>index.php/sistemas/excluirCheck',
                type: 'POST',
                data: { idSistemas_checks: id },
                dataType: 'json',
                success: function(response) {
                    if (response.result) {
                        $("#divChecklist").load("<?php echo current_url(); ?> #divChecklist > *");
                    } else {
                        Swal.fire({ type: "error", title: "Atenção", text: response.messages });
                    }
                }
            });
        });
    });
</script>
