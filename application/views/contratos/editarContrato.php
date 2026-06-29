<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/maskmoney.js"></script>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-file-signature"></i>
                </span>
                <h5>Editar Contrato: #<?php echo $result->idContratos; ?> - <?php echo $result->nomeContratos; ?></h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <div class="span12" id="divContratos" style=" margin-left: 0">
                    <ul class="nav nav-tabs">
                        <li class="active" id="tabDetalhes"><a href="#tab1" data-toggle="tab">Geral</a></li>
                        <li><a href="#tab2" data-toggle="tab">Desc/Acres.</a></li>
                        <li><a href="#tab3" data-toggle="tab">Ordens de Serviço</a></li>
                        <li><a href="#tab4" data-toggle="tab">Vendas</a></li>
                        <li><a href="#tab5" data-toggle="tab">Anexos</a></li>
                        <li><a href="#tab6" data-toggle="tab">Sistemas</a></li>
                        <li><a href="#tab8" data-toggle="tab">Checklists do Contrato</a></li>
                        <li><a href="#tab9" data-toggle="tab">Faturas/Notas e Boletos</a></li>
                    </ul>
                    <form action="<?php echo current_url(); ?>" method="post" id="formContrato" novalidate>
                    <div class="tab-content">
                        
                        <!-- ABA GERAL -->
                        <div class="tab-pane active" id="tab1">
                            <div class="span12" id="divEditarContrato">
                                    <input type="hidden" name="idContratos" id="idContratos" value="<?php echo $result->idContratos; ?>" />
                                    
                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        <div class="span4">
                                            <label for="cliente">Cliente<span class="required">*</span></label>
                                            <input id="cliente" class="span12" type="text" name="cliente" value="<?php echo $result->nomeCliente; ?>" />
                                            <input id="clientes_id" class="span12" type="hidden" name="clientes_id" value="<?php echo $result->clientes_id; ?>" />
                                        </div>
                                        <div class="span3">
                                            <label for="tipoContrato">Tipo de Contrato<span class="required">*</span></label>
                                            <select class="span12" name="tipoContrato" id="tipoContrato" required>
                                                <option value="Manutenção Preventiva s/ Peças" <?php if($result->tipoContrato == 'Manutenção Preventiva s/ Peças'){echo 'selected';} ?>>Manutenção Preventiva s/ Peças</option>
                                                <option value="Manutenção Preventiva c/ Peças" <?php if($result->tipoContrato == 'Manutenção Preventiva c/ Peças'){echo 'selected';} ?>>Manutenção Preventiva c/ Peças</option>
                                                <option value="Locação" <?php if($result->tipoContrato == 'Locação'){echo 'selected';} ?>>Locação</option>
                                            </select>
                                        </div>
                                        <div class="span3">
                                            <label for="tecnico_id">Técnico Responsável</label>
                                            <select class="span12" name="tecnico_id" id="tecnico_id">
                                                <option value="">Selecione...</option>
                                                <?php if (isset($tecnicos)) { foreach ($tecnicos as $t) { if ($t->ativo == '1') {
                                                    echo '<option value="' . $t->idTecnicos . '" ' . ($result->tecnico_id == $t->idTecnicos ? 'selected' : '') . '>' . $t->nome . '</option>';
                                                } } } ?>
                                            </select>
                                        </div>
                                        <div class="span2">
                                            <label for="status">Status<span class="required">*</span></label>
                                            <select class="span12" name="status" id="status" required>
                                                <option value="Negociação" <?php if($result->status == 'Negociação' || $result->status == null){echo 'selected';} ?>>Negociação</option>
                                                <option value="Ativo" <?php if($result->status == 'Ativo' || $result->status == '1'){echo 'selected';} ?>>Ativo</option>
                                                <option value="Inativo" <?php if($result->status == 'Inativo' || $result->status == '0'){echo 'selected';} ?>>Inativo</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        <div class="span6">
                                            <label for="nomeContratos">Nome / Identificação do Contrato<span class="required">*</span></label>
                                            <input id="nomeContratos" class="span12" type="text" name="nomeContratos" value="<?php echo $result->nomeContratos; ?>" required />
                                        </div>
                                        <div class="span3">
                                            <label for="dataInicial">Data Inicial<span class="required">*</span></label>
                                            <input id="dataInicial" class="span12 datepicker" type="text" name="dataInicial" value="<?php echo date('d/m/Y', strtotime($result->dataInicial)); ?>" required />
                                        </div>
                                        <div class="span3">
                                            <label for="dataFinal">Data Final</label>
                                            <input id="dataFinal" class="span12 datepicker" type="text" name="dataFinal" value="<?php echo $result->dataFinal ? date('d/m/Y', strtotime($result->dataFinal)) : ''; ?>" />
                                        </div>
                                    </div>

                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        <label for="descricaoContratos">Descrição / Termos</label>
                                        <textarea class="span12 editor" name="descricaoContratos" id="descricaoContratos" cols="30" rows="5"><?php echo $result->descricaoContratos; ?></textarea>
                                    </div>
                                    <div class="span12" style="padding: 1%; margin-left: 0; display: flex; justify-content: center; align-items: center;">
                                        <button class="button btn btn-primary" id="btnContinuar" style="min-width: 150px; margin: 0 5px"><span class="button__icon"><i class="bx bx-sync"></i></span><span class="button__text2">Atualizar</span></button>
                                        <a href="#modal-gerar-os" role="button" data-toggle="modal" class="button btn btn-info" id="btnGerarOs" style="min-width: 150px; margin: 0 5px"><span class="button__icon"><i class="bx bx-calendar-event"></i></span><span class="button__text2">Gerar OS Preventivas</span></a>
                                        <a href="<?php echo base_url() ?>index.php/contratos" class="button btn btn-warning" style="min-width: 150px; margin: 0 5px"><span class="button__icon"><i class="bx bx-undo"></i></span><span class="button__text2">Voltar</span></a>
                                    </div>
                                    </div>
                                <!-- O form só fecha depois para que a aba 2 (valores) possa ser submetida junto se quiser, ou fazemos forms separados. Vamos fechar aqui e a aba 2 envia os campos ocultos. Na verdade, tudo fará parte do formContrato. -->
                        </div>

                        <!-- ABA VALORES -->
                        <div class="tab-pane" id="tab2">
                            <div class="span12" style="padding: 1%">
                                <h4>Gestão de Valores do Contrato</h4>
                                
                                <div class="span12" style="margin-left: 0; margin-bottom: 15px">
                                    <div class="span3">
                                        <?php 
                                            // Soma os sistemas para garantir a exibição correta (corrige dados antigos do BD)
                                            $somaPHP_view = 0;
                                            if(isset($sistemas_contrato)){
                                                foreach($sistemas_contrato as $sc){
                                                    $somaPHP_view += (float)$sc->subTotal;
                                                }
                                            }
                                        ?>
                                        <label for="valorContrato">Valor Base (R$) - Dinâmico</label>
                                        <input id="valorContrato" class="span12 money" type="text" name="valorContrato" value="<?php echo number_format($somaPHP_view, 2, ',', '.'); ?>" readonly style="background-color: #eee; color: #333;" title="O Valor Base é a soma automática dos Sistemas" />
                                    </div>
                                    <div class="span3">
                                        <!-- Placeholder para alinhar -->
                                    </div>
                                </div>
                                
                                <div class="span12" style="margin-left: 0; margin-bottom: 15px">
                                    <div class="span3">
                                        <label for="tipoDesconto">Tipo de Desconto</label>
                                        <select class="span12" name="tipoDesconto" id="tipoDesconto">
                                            <option value="R$" <?php echo ($result->tipoDesconto == 'R$') ? 'selected' : ''; ?>>R$</option>
                                            <option value="%" <?php echo ($result->tipoDesconto == '%') ? 'selected' : ''; ?>>%</option>
                                        </select>
                                    </div>
                                    <div class="span3">
                                        <label for="valorDesconto">Valor do Desconto</label>
                                        <input id="valorDesconto" class="span12 money" type="text" name="valorDesconto" value="<?php echo number_format($result->valorDesconto, 2, ',', '.'); ?>" />
                                    </div>
                                </div>

                                <div class="span12" style="margin-left: 0; margin-bottom: 15px">
                                    <div class="span3">
                                        <label for="tipoAcrescimo">Tipo de Acréscimo</label>
                                        <select class="span12" name="tipoAcrescimo" id="tipoAcrescimo">
                                            <option value="R$" <?php echo ($result->tipoAcrescimo == 'R$') ? 'selected' : ''; ?>>R$</option>
                                            <option value="%" <?php echo ($result->tipoAcrescimo == '%') ? 'selected' : ''; ?>>%</option>
                                        </select>
                                    </div>
                                    <div class="span3">
                                        <label for="valorAcrescimo">Valor do Acréscimo</label>
                                        <input id="valorAcrescimo" class="span12 money" type="text" name="valorAcrescimo" value="<?php echo number_format($result->valorAcrescimo, 2, ',', '.'); ?>" />
                                    </div>
                                </div>

                                <div class="span12" style="margin-left: 0; margin-bottom: 15px">
                                    <div class="span6">
                                        <label for="valorTotal">Valor Mensal / Total Atualizado (R$)</label>
                                        <input id="valorTotal" class="span12 money" type="text" name="valorTotal" value="<?php echo number_format($result->valorTotal, 2, ',', '.'); ?>" readonly style="background-color: #eee; font-weight: bold; color: #333;" />
                                        <small>O valor dos sistemas vinculados já faz parte deste total.</small>
                                    </div>
                                </div>
                                <div class="span12" style="margin-left: 0">
                                    <button class="button btn btn-primary"><span class="button__icon"><i class="bx bx-sync"></i></span><span class="button__text2">Salvar Valores</span></button>
                                </div>
                            </div>
                        </div>
                        <!-- ABA ORDENS DE SERVIÇO -->
                        <div class="tab-pane" id="tab3">
                            <div class="span12" style="padding: 1%">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Nº O.S.</th>
                                            <th>Data</th>
                                            <th>Status</th>
                                            <th>Técnico</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($os)){ echo '<tr><td colspan="5">Nenhuma O.S. vinculada a este contrato.</td></tr>'; }
                                        else{
                                            foreach($os as $o){
                                                switch ($o->status) {
                                                    case 'A Sair | Aguard Conclusão': $cor = '#00cd00'; break;
                                                    case 'Em Andamento':              $cor = '#436eee'; break;
                                                    case 'Negociação':                $cor = '#ffd700'; break;
                                                    case 'Orçamento':                 $cor = '#CDB380'; break;
                                                    case 'Manutenção Preventiva':     $cor = '#AEB404'; break;
                                                    case 'Cancelado':                 $cor = '#CD0000'; break;
                                                    case 'Finalizado':                $cor = '#225566'; break;
                                                    case 'Faturado':                  $cor = '#B266FF'; break;
                                                    case 'Aguardando Peças':          $cor = '#FF7F00'; break;
                                                    case 'Aprovado':                  $cor = '#808080'; break;
                                                    default:                          $cor = '#E0E4CC'; break;
                                                }
                                                $tecnico = !empty($o->tecnicoName) ? $o->tecnicoName : '—';
                                                echo '<tr>';
                                                echo '<td>'.$o->idOs.'</td>';
                                                echo '<td>'.date('d/m/Y', strtotime($o->dataInicial)).'</td>';
                                                echo '<td><span class="badge" style="background-color:'.$cor.'; border-color:'.$cor.'">'.$o->status.'</span></td>';
                                                echo '<td>'.$tecnico.'</td>';
                                                echo '<td><a href="'.base_url().'index.php/os/visualizar/'.$o->idOs.'" target="_blank" class="btn-nwe" title="Visualizar O.S."><i class="bx bx-show"></i></a></td>';
                                                echo '</tr>';
                                            }
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- ABA VENDAS -->
                        <div class="tab-pane" id="tab4">
                            <div class="span12" style="padding: 1%">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Nº Venda</th>
                                            <th>Data</th>
                                            <th>Vendedor</th>
                                            <th>Valor Total</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($vendas)){ echo '<tr><td colspan="5">Nenhuma Venda vinculada a este contrato.</td></tr>'; }
                                        else{
                                            foreach($vendas as $v){
                                                echo '<tr>';
                                                echo '<td>'.$v->idVendas.'</td>';
                                                echo '<td>'.date('d/m/Y', strtotime($v->dataVenda)).'</td>';
                                                echo '<td>'.$v->nome.'</td>';
                                                echo '<td>R$ '.number_format($v->valorTotal, 2, ',', '.').'</td>';
                                                echo '<td><a href="'.base_url().'index.php/vendas/editar/'.$v->idVendas.'" target="_blank" class="btn btn-mini btn-info">Ver Venda</a></td>';
                                                echo '</tr>';
                                            }
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- ABA ANEXOS -->
                        <div class="tab-pane" id="tab5">
                            <div class="span12" style="padding: 1%; margin-left: 0">
                                <div class="span12 well" style="padding: 1%; margin-left: 0" id="form-anexos">
                                    <div id="wrapperAnexos">
                                        <div class="span6">
                                            <label for="">Anexo</label>
                                            <input type="file" class="span12" id="userfile_anexos" name="userfile_anexos[]" multiple="multiple" size="20" />
                                        </div>

                                        <div class="span6" style="margin-left: 0; display: flex; gap: 10px;">
                                            <div style="flex: 1;">
                                                <label for="">.</label>
                                                <button type="button" class="button btn btn-success" id="btnAnexarAjax" style="width: 100%;">
                                                    <span class="button__icon"><i class='bx bx-paperclip'></i></span><span class="button__text2">Anexar</span>
                                                </button>
                                            </div>

                                            <div style="flex: 1;">
                                                <label for="">.</label>
                                                <label class="button btn btn-success" style="cursor: pointer; width: 100%; margin-bottom: 0;">
                                                    <span class="button__icon"><i class="fas fa-camera"></i></span>
                                                    <span class="button__text2"> Câmera Mobile</span>
                                                    <input type="file" id="camera_anexos" name="camera_anexos[]" accept="image/*" capture="environment" multiple="multiple" style="display: none;" />
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="span12 pull-left" id="divAnexos" style="margin-left: 0">
                                    <?php
                                    foreach ($anexos as $a) {
                                        $ext = strtolower(pathinfo($a->anexo, PATHINFO_EXTENSION));

                                        if ($a->thumb == null) {
                                            $link = $a->url . '/' . $a->anexo;
                                            if ($ext == 'pdf') {
                                                $thumb_content = '<div style="height: 150px; display: flex; align-items: center; justify-content: center; background: #f9f9f9;"><i class="fas fa-file-pdf" style="font-size: 80px; color: #e74c3c;"></i></div>';
                                            } else {
                                                $thumb = base_url() . 'assets/img/icon-file.png';
                                                $thumb_content = '<img src="' . $thumb . '" alt="' . $a->anexo . '" style="max-height: 150px;">';
                                            }
                                        } else {
                                            $thumb = $a->url . '/thumbs/' . $a->thumb;
                                            $link = $a->url . '/' . $a->anexo;
                                            $thumb_content = '<img src="' . $thumb . '" alt="' . $a->anexo . '" style="max-height: 150px;">';
                                        }
                                        echo '<div class="span3" style="min-height: 150px; margin-left: 0; margin-bottom: 10px;">
                                                    <a style="min-height: 150px; display: block; border: 1px solid #ddd; overflow: hidden; background: #fff;" href="#modal-anexo" imagem="' . $a->idAnexos . '" link="' . $link . '" role="button" class="btn anexo span12" data-toggle="modal">
                                                        ' . $thumb_content . '
                                                    </a>
                                                </div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <!-- ABA SISTEMAS -->
                        <div class="tab-pane" id="tab6">
                            <div class="span12 well" style="padding: 1%; margin-left: 0">
                                <div id="formSistemas" style="display:flex; align-items:flex-end; gap:8px; flex-wrap:nowrap;">
                                    <input type="hidden" name="idSistema" id="idSistema" />
                                    <input type="hidden" name="idContratosSistemas" id="idContratosSistemas" value="<?php echo $result->idContratos; ?>" />
                                    <div style="flex:3; min-width:0;">
                                        <label for="sistema" style="margin-bottom:3px; display:block;">Sistema</label>
                                        <input type="text" class="span12" name="sistema" id="sistema" placeholder="Digite o nome do sistema" style="margin-bottom:0;" />
                                    </div>
                                    <div style="flex:2; min-width:0;">
                                        <label for="local_sistema" style="margin-bottom:3px; display:block;">Local</label>
                                        <input type="text" class="span12" name="local" id="local_sistema" placeholder="Ex: ENTRADA PRINCIPAL" style="margin-bottom:0;" />
                                    </div>
                                    <div style="flex:1; min-width:0;">
                                        <label for="preco_sistema" style="margin-bottom:3px; display:block;">Preço</label>
                                        <input type="text" placeholder="Preço" id="preco_sistema" name="preco" class="span12 money" style="margin-bottom:0;" />
                                    </div>
                                    <div style="flex:0 0 auto;">
                                        <label style="margin-bottom:3px; display:block;">&nbsp;</label>
                                        <button type="button" class="button btn btn-success" id="btnAdicionarSistema" style="white-space:nowrap; margin-bottom:0;">
                                            <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="span12" id="divSistemas" style="margin-left: 0">
                                <table class="table table-bordered" id="tblSistemas" style="table-layout:fixed; width:100%;">
                                    <colgroup>
                                        <col style="width:40%;" />
                                        <col style="width:30%;" />
                                        <col style="width:20%;" />
                                        <col style="width:10%;" />
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th>Sistema</th>
                                            <th>Local</th>
                                            <th>Sub-total</th>
                                            <th style="text-align:center;">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $totalSistemas = 0;
                                        if (!$sistemas_contrato) {
                                            echo '<tr><td colspan="4">Nenhum Sistema vinculado.</td></tr>';
                                        } else {
                                            foreach ($sistemas_contrato as $s) {
                                                $totalSistemas += $s->subTotal;
                                                echo '<tr>';
                                                echo '<td>' . $s->nome . '</td>';
                                                echo '<td>' . $s->local . '</td>';
                                                echo '<td>R$ ' . number_format($s->subTotal, 2, ',', '.') . '</td>';
                                                echo '<td style="text-align:center;"><a href="" idAcao="' . $s->idSistemas_contratos . '" preco="' . $s->subTotal . '" title="Excluir Sistema" class="btn-nwe4 btnExcluirSistema"><i class="bx bx-trash-alt"></i></a></td>';
                                                echo '</tr>';
                                            }
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" style="text-align: right"><strong>Total Sistemas:</strong></td>
                                            <td><strong>R$ <span id="totalSistemasVal"><?php echo number_format($totalSistemas, 2, ',', '.'); ?></span></strong></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- ABA CHECKLISTS DO CONTRATO -->
                        <div class="tab-pane" id="tab8">
                            <div class="span12" style="padding: 1%;">
                                <h4>Checklists do Contrato</h4>
                                <p style="text-align: center;">Este formulário permite gerenciar os checklists preenchidos pelas Ordens de Serviço vinculadas a este contrato.</p>
                                
                                <div class="table-responsive" style="overflow-x: auto;">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center; width: 15%; white-space: nowrap;"># OS</th>
                                                <th style="text-align: center; width: 15%; white-space: nowrap;">Nº Checklist</th>
                                                <th style="text-align: center; width: 20%; white-space: nowrap;">Data</th>
                                                <th style="text-align: center; width: 30%; white-space: nowrap;">Técnico</th>
                                                <th style="text-align: center; width: 20%; white-space: nowrap;">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(empty($checklists)): ?>
                                                <tr>
                                                    <td colspan="5" style="text-align: center;">Nenhum checklist preenchido para este contrato.</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach($checklists as $chk): ?>
                                                    <tr>
                                                        <td style="text-align: center; vertical-align: middle; white-space: nowrap;"><?php echo $chk->os_id; ?></td>
                                                        <td style="text-align: center; vertical-align: middle; white-space: nowrap;"><?php echo sprintf('%04d', $chk->idChecklist); ?></td>
                                                        <td style="text-align: center; vertical-align: middle; white-space: nowrap;"><?php echo date('d/m/Y', strtotime($chk->data_checklist ?: $chk->data_criacao)); ?></td>
                                                        <td style="text-align: center; vertical-align: middle;"><?php echo $chk->nome_tecnico; ?></td>
                                                        <td style="text-align: center; vertical-align: middle; white-space: nowrap;">
                                                            <div style="display: inline-flex; justify-content: center; align-items: center; gap: 8px; flex-wrap: nowrap;">
                                                                <a href="<?php echo site_url('os/visualizarChecklist/' . $chk->os_id); ?>" target="_blank" class="btn-nwe" title="Visualizar Checklist"><i class="bx bx-show"></i></a>
                                                                <a href="<?php echo site_url('os/imprimirChecklist/' . $chk->os_id); ?>" target="_blank" class="btn-nwe6" title="Imprimir Checklist"><i class="bx bx-printer"></i></a>
                                                                <a href="<?php echo site_url('os/editar/' . $chk->os_id); ?>" target="_blank" class="btn-nwe3" title="Editar OS"><i class="bx bx-edit"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- ABA FATURAS E BOLETOS -->
                        <div class="tab-pane" id="tab9">
                            <div class="span12" style="padding: 1%">
                                <h4>Cobranças e Faturas das O.S./Vendas deste Contrato</h4>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Nº Fatura</th>
                                            <th>Vencimento</th>
                                            <th>Status</th>
                                            <th>Valor</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($faturas)){ echo '<tr><td colspan="5">Nenhuma cobrança vinculada.</td></tr>'; }
                                        else{
                                            foreach($faturas as $f){
                                                echo '<tr>';
                                                echo '<td>'.$f->idCobranca.'</td>';
                                                echo '<td>'.date('d/m/Y', strtotime($f->vencimento)).'</td>';
                                                echo '<td>'.$f->status.'</td>';
                                                echo '<td>R$ '.number_format($f->valor, 2, ',', '.').'</td>';
                                                echo '<td><a href="'.base_url().'index.php/cobrancas/visualizar/'.$f->idCobranca.'" target="_blank" class="btn btn-mini btn-info">Ver Cobrança</a></td>';
                                                echo '</tr>';
                                            }
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal visualizar anexo -->
<div id="modal-anexo" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Visualizar Anexo</h3>
    </div>
    <div class="modal-body">
        <div class="span12" id="div-visualizar-anexo" style="text-align: center">
            <div class='progress progress-info progress-striped active'>
                <div class='bar' style='width: 100%'></div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Fechar</button>
        <a href="" id-imagem="" class="btn btn-inverse" id="download">Download</a>
        <a href="" link="" class="btn btn-danger" id="excluir-anexo">Excluir Anexo</a>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('.money').maskMoney({prefix:'', allowNegative: false, thousands:'.', decimal:',', affixesStay: false});
        $('.datepicker').datepicker({
            dateFormat: 'dd/mm/yy'
        });

        $("#cliente").autocomplete({
            source: "<?php echo base_url(); ?>index.php/contratos/auto_complete_cliente",
            minLength: 1,
            select: function(event, ui) {
                $("#clientes_id").val(ui.item.id);
            }
        });

        // Calculo de desconto e acrescimo com base na soma dos Sistemas (que é o Valor Base)
        $("#valorDesconto, #valorAcrescimo, #tipoDesconto, #tipoAcrescimo").on("keyup change", function(){
            var parseMoney = function(val) {
                if(!val) return 0;
                var unmasked = val.toString().replace(/\./g, '').replace(',', '.');
                return parseFloat(unmasked) || 0;
            };
            
            var totalSistemasPHP = <?php 
                // Soma dos sistemas
                $somaPHP = 0;
                if(isset($sistemas_contrato)){
                    foreach($sistemas_contrato as $sc){
                        $somaPHP += (float)$sc->subTotal;
                    }
                }
                echo $somaPHP;
            ?>;
            
            // O subtotal (e o Valor Base) é exatamente a soma dos sistemas
            var subtotal = totalSistemasPHP;
            
            var desc_val = parseMoney($("#valorDesconto").val());
            var tipo_desc = $("#tipoDesconto").val();
            var desc_real = 0;
            if(tipo_desc === '%') {
                desc_real = subtotal * (desc_val / 100);
            } else {
                desc_real = desc_val;
            }

            var acres_val = parseMoney($("#valorAcrescimo").val());
            var tipo_acres = $("#tipoAcrescimo").val();
            var acres_real = 0;
            if(tipo_acres === '%') {
                acres_real = subtotal * (acres_val / 100);
            } else {
                acres_real = acres_val;
            }

            var finalTotal = subtotal - desc_real + acres_real;
            if(finalTotal < 0) finalTotal = 0;
            
            // Converte para formato BRL na tela
            var formatter = new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            $("#valorTotal").val(formatter.format(finalTotal));
        });
        function uploadAnexosAjax(files) {
            if (files.length === 0) return;
            var formData = new FormData();
            for (var i = 0; i < files.length; i++) {
                formData.append('userfile[]', files[i]);
            }
            formData.append('idContrato', $("#idContratos").val()); // Pegando do formContrato Geral

            var csrfTokenName = $('meta[name="csrf-token-name"]').attr('content');
            var csrfTokenValue = $('[name="' + csrfTokenName + '"]').first().val();
            formData.append(csrfTokenName, csrfTokenValue);

            $("#form-anexos").hide('1000');
            $("#divAnexos").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");

            $.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>index.php/contratos/anexar",
                data: formData,
                mimeType: "multipart/form-data",
                contentType: false,
                cache: false,
                processData: false,
                dataType: 'json',
                success: function(data) {
                    if (data.result == true) {
                        $("#divAnexos").load("<?php echo current_url(); ?> #divAnexos");
                        $("#userfile_anexos").val('');
                        $("#camera_anexos").val('');
                    } else {
                        var erroDetalhado = (data.errors && data.errors.upload) ? '<br>' + data.errors.upload.join('<br>') : '';
                        $("#divAnexos").html('<div class="alert fade in"><button type="button" class="close" data-dismiss="alert">×</button><strong>Atenção!</strong> ' + data.mensagem + erroDetalhado + '</div>');
                    }
                    $("#form-anexos").show('1000');
                },
                error: function(xhr, status, error) {
                    var errorMsg = "Atenção! Ocorreu um erro. Verifique se você anexou o(s) arquivo(s).";
                    if (xhr.responseText) {
                        errorMsg += " Detalhes: " + xhr.responseText;
                    }
                    $("#divAnexos").html('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert">×</button><strong>' + errorMsg + '</strong></div>');
                    $("#form-anexos").show('1000');
                }
            });
        }

        $(document).on('click', '#btnAnexarAjax', function(e) {
            e.preventDefault();
            var files = $('#userfile_anexos')[0].files;
            uploadAnexosAjax(files);
        });

        $(document).on('change', '#camera_anexos', function(e) {
            var files = $(this)[0].files;
            uploadAnexosAjax(files);
        });

        $(document).on('click', '.anexo', function(event) {
            event.preventDefault();
            var link = $(this).attr('link');
            var id = $(this).attr('imagem');
            var url = '<?php echo base_url(); ?>index.php/contratos/excluirAnexo/';
            var ext = link.split('.').pop().toLowerCase();
            
            if (ext === 'pdf') {
                $("#div-visualizar-anexo").html('<iframe src="' + link + '" width="100%" height="400px" style="border: none;"></iframe>');
            } else if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'heic'].includes(ext)) {
                $("#div-visualizar-anexo").html('<img src="' + link + '" alt="">');
            } else {
                $("#div-visualizar-anexo").html('<div><i class="fas fa-file-alt" style="font-size: 100px; color: #555;"></i><br><br>Documento</div>');
            }
            $("#excluir-anexo").attr('link', url + id);

            $("#download").attr('href', "<?php echo base_url(); ?>index.php/contratos/downloadanexo/" + id);

        });

        $(document).on('click', '#excluir-anexo', function(event) {
            event.preventDefault();
            var link = $(this).attr('link');
            var idContrato = "<?php echo $result->idContratos ?>"
            $('#modal-anexo').modal('hide');
            $("#divAnexos").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");

            $.ajax({
                type: "POST",
                url: link,
                dataType: 'json',
                data: "idContrato=" + idContrato,
                success: function(data) {
                    if (data.result == true) {
                        $("#divAnexos").load("<?php echo current_url(); ?> #divAnexos");
                    } else {
                        Swal.fire({
                            type: "error",
                            title: "Atenção",
                            text: data.mensagem
                        });
                    }
                }
            });
        });
        // Autocomplete para Sistemas
        $("#sistema").autocomplete({
            source: "<?php echo base_url(); ?>index.php/contratos/autoCompleteSistema",
            minLength: 1,
            select: function(event, ui) {
                $("#idSistema").val(ui.item.id);
                $("#preco_sistema").val(ui.item.preco);
            }
        });

        // Adicionar Sistema
        $("#btnAdicionarSistema").click(function(e) {
            e.preventDefault();
            var idContrato = $("#idContratosSistemas").val();
            var idSistema = $("#idSistema").val();
            var local = $("#local_sistema").val();
            var preco = $("#preco_sistema").val();

            if (!idSistema || !preco) {
                Swal.fire({ type: "error", title: "Atenção", text: "Preencha o sistema e o preço." });
                return;
            }

            $.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>index.php/contratos/adicionarSistema",
                data: { idSistema: idSistema, preco: preco, idContratosSistemas: idContrato, local: local },
                dataType: 'json',
                success: function(data) {
                    if (data.result == true) {
                        $("#divSistemas").load("<?php echo current_url(); ?> #divSistemas", function(){
                            atualizaTotalContrato();
                        });
                        $("#divChecklists").load("<?php echo current_url(); ?> #divChecklists");
                        $("#sistema").val('');
                        $("#local_sistema").val('');
                        $("#preco_sistema").val('');
                        $("#idSistema").val('');
                    } else {
                        Swal.fire({ type: "error", title: "Atenção", text: "Ocorreu um erro ao adicionar o sistema." });
                    }
                }
            });
        });

        // Excluir Sistema
        $(document).on('click', '.btnExcluirSistema', function(e) {
            e.preventDefault();
            var id = $(this).attr('idAcao');
            if(confirm('Deseja realmente remover este sistema do contrato? (Os checklists também serão removidos)')){
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>index.php/contratos/excluirSistema",
                    data: { idSistemas_contratos: id },
                    dataType: 'json',
                    success: function(data) {
                        if (data.result == true) {
                            $("#divSistemas").load("<?php echo current_url(); ?> #divSistemas", function(){
                                atualizaTotalContrato();
                            });
                            $("#divChecklists").load("<?php echo current_url(); ?> #divChecklists");
                        } else {
                            Swal.fire({ type: "error", title: "Atenção", text: "Ocorreu um erro ao excluir o sistema." });
                        }
                    }
                });
            }
        });

        // Check/Uncheck Checklist
        $(document).on('change', '.checkSistema', function() {
            var idCheck = $(this).data('id');
            var isChecked = $(this).is(':checked') ? 1 : 0;
            
            $.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>index.php/contratos/atualizarCheck",
                data: { idCheck: idCheck, status: isChecked },
                dataType: 'json',
                success: function(data) {
                    if(data.result != true){
                        Swal.fire({ type: "error", title: "Atenção", text: "Ocorreu um erro ao atualizar o status." });
                    }
                }
            });
        });

        // Adicionar Check Extra Manual
        $("#btnAdicionarCheckExtra").click(function(e){
            e.preventDefault();
            var idSistemaContrato = $("#sistemas_contratos_id_check").val();
            var descricao = $("#descricao_check_extra").val();
            
            if(!idSistemaContrato || !descricao){
                Swal.fire({ type: "error", title: "Atenção", text: "Preencha todos os campos do checklist." });
                return;
            }

            $.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>index.php/contratos/adicionarCheckManual",
                data: { sistemas_contratos_id: idSistemaContrato, descricao: descricao },
                dataType: 'json',
                success: function(data) {
                    if(data.result == true){
                        $("#divChecklists").load("<?php echo current_url(); ?> #divChecklists");
                        $("#descricao_check_extra").val('');
                    }else{
                        Swal.fire({ type: "error", title: "Atenção", text: "Ocorreu um erro ao inserir o check." });
                    }
                }
            });
        });

        // Atualiza Valor Total do Contrato ao Adicionar/Remover Sistema
        function atualizaTotalContrato(){
            setTimeout(function(){
                var totalSistemasTexto = $("#totalSistemasVal").text().replace(/\./g, '').replace(',', '.');
                var totalSistemas = parseFloat(totalSistemasTexto) || 0;
                
                // O Valor Base agora é dinâmico, baseado nos sistemas
                $("#valorContrato").val(totalSistemasTexto);
                
                var descTexto = $("#valorDesconto").val().replace(/\./g, '').replace(',', '.') || 0;
                var desc = parseFloat(descTexto) || 0;
                var tipo_desc = $("#tipoDesconto").val();
                
                var acresTexto = $("#valorAcrescimo").val().replace(/\./g, '').replace(',', '.') || 0;
                var acres = parseFloat(acresTexto) || 0;
                var tipo_acres = $("#tipoAcrescimo").val();
                
                var desc_real = (tipo_desc === '%') ? totalSistemas * (desc / 100) : desc;
                var acres_real = (tipo_acres === '%') ? totalSistemas * (acres / 100) : acres;
                
                var novoValorTotal = totalSistemas - desc_real + acres_real;
                if(novoValorTotal < 0) novoValorTotal = 0;
                
                var formatter = new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                $("#valorTotal").val(formatter.format(novoValorTotal));
            }, 500);
        }
    });
</script>

<!-- Modal Gerar OS Preventivas -->
<div id="modal-gerar-os" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form id="formGerarOs" action="<?php echo base_url() ?>index.php/contratos/gerarOsPreventivas" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">Gerar OS Preventivas</h3>
        </div>
        <div class="modal-body">
            <div class="span12 alert alert-info">
                As Ordens de Serviço serão geradas mensalmente até a Data Final do contrato.
            </div>
            <input type="hidden" name="idContratos" value="<?php echo $result->idContratos; ?>" />
            <div class="span12" style="margin-left: 0">
                <label for="dataPrimeiraVisita">Data da Primeira Visita <span class="required">*</span></label>
                <input id="dataPrimeiraVisita" class="span12 datepicker" type="text" name="dataPrimeiraVisita" value="<?php echo date('d/m/Y'); ?>" required />
            </div>
        </div>
        <div class="modal-footer" style="display:flex;justify-content:center;">
            <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true"><span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span></button>
            <button class="button btn btn-primary"><span class="button__icon"><i class="bx bx-check"></i></span><span class="button__text2">Gerar</span></button>
        </div>
    </form>
</div>
<script type="text/javascript">
$(document).ready(function(){
    $('#formGerarOs').validate({
        rules: {
            dataPrimeiraVisita: { required: true }
        },
        messages: {
            dataPrimeiraVisita: { required: 'Campo Requerido.' }
        }
    });
});
</script>
