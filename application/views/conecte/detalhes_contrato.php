<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0; height: auto; min-height: 36px;">
                <span class="icon" style="float: left; padding: 8px 10px;">
                    <i class="fas fa-file-signature"></i>
                </span>
                <h5 style="float: left; line-height: 1.5; padding: 8px 10px; margin: 0; white-space: normal; width: calc(100% - 60px);">Contrato: #<?php echo $result->idContratos; ?> - <?php echo $result->nomeContratos; ?></h5>
                <div style="clear: both;"></div>
            </div>
            <div class="widget-content nopadding tab-content">
                <div class="span12" id="divContratos" style="margin-left: 0">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab1" data-toggle="tab">Geral</a></li>
                        <li><a href="#tab3" data-toggle="tab">Ordens de Serviços</a></li>
                        <li><a href="#tab4" data-toggle="tab">Vendas</a></li>
                        <li><a href="#tab5" data-toggle="tab">Anexos</a></li>
                        <li><a href="#tab6" data-toggle="tab">Sistemas</a></li>
                        <li><a href="#tab8" data-toggle="tab">Checklists do Contrato</a></li>
                        <li><a href="#tab9" data-toggle="tab">Cobranças</a></li>
                        <li><a href="#tab10" data-toggle="tab">NFS-e</a></li>
                    </ul>
                    
                    <div class="tab-content" style="padding: 15px;">
                        
                        <!-- ABA GERAL -->
                        <div class="tab-pane active" id="tab1">
                            <div class="span12" style="margin-left: 0;">
                                <div class="span12" style="padding: 1%; margin-left: 0; border-bottom: 1px solid #eee; margin-bottom: 15px;">
                                    <div class="span4">
                                        <label><strong>Cliente:</strong></label>
                                        <input class="span12" type="text" value="<?php echo $result->nomeCliente; ?>" readonly style="background-color: #f9f9f9;" />
                                    </div>
                                    <div class="span4">
                                        <label><strong>Tipo de Contrato:</strong></label>
                                        <input class="span12" type="text" value="<?php echo $result->tipoContrato; ?>" readonly style="background-color: #f9f9f9;" />
                                    </div>
                                    <div class="span4">
                                        <label><strong>Status:</strong></label>
                                        <?php
                                            $statusStr = $result->status;
                                            $cor = '#E0E4CC';
                                            if ($statusStr == 'Ativo' || $statusStr == '1') { $cor = '#54c795'; $statusStr = 'Ativo'; }
                                            elseif ($statusStr == 'Inativo' || $statusStr == '0') { $cor = '#225566'; $statusStr = 'Inativo'; }
                                            elseif ($statusStr == 'Negociação') { $cor = '#ffd700'; }
                                        ?>
                                        <div>
                                            <span class="badge" style="background-color: <?php echo $cor; ?>; border-color: <?php echo $cor; ?>; font-size: 14px; padding: 6px 12px;"><?php echo $statusStr; ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="span12" style="padding: 1%; margin-left: 0; border-bottom: 1px solid #eee; margin-bottom: 15px;">
                                    <div class="span4">
                                        <label><strong>Nome / Identificação do Contrato:</strong></label>
                                        <input class="span12" type="text" value="<?php echo $result->nomeContratos; ?>" readonly style="background-color: #f9f9f9;" />
                                    </div>
                                    <div class="span4">
                                        <label><strong>Data Inicial:</strong></label>
                                        <input class="span12" type="text" value="<?php echo date('d/m/Y', strtotime($result->dataInicial)); ?>" readonly style="background-color: #f9f9f9;" />
                                    </div>
                                    <div class="span4">
                                        <label><strong>Data Final:</strong></label>
                                        <input class="span12" type="text" value="<?php echo $result->dataFinal ? date('d/m/Y', strtotime($result->dataFinal)) : 'Indeterminado'; ?>" readonly style="background-color: #f9f9f9;" />
                                    </div>
                                </div>

                                <?php if (!empty($result->descricaoContratos)): ?>
                                <div class="span12" style="padding: 1%; margin-left: 0; border-bottom: 1px solid #eee; margin-bottom: 15px;">
                                    <label><strong>Descrição / Termos:</strong></label>
                                    <div style="background-color: #f9f9f9; padding: 10px; border: 1px solid #e3e3e3; border-radius: 4px; min-height: 80px;">
                                        <?php echo nl2br($result->descricaoContratos); ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <div class="span12" style="padding: 1%; margin-left: 0;">
                                    <h4>Resumo de Valores</h4>
                                    <div class="span4">
                                        <label><strong>Valor Base (Sistemas):</strong></label>
                                        <?php
                                            $somaSistemas = 0;
                                            if (!empty($sistemas_contrato)) {
                                                foreach ($sistemas_contrato as $sc) {
                                                    $somaSistemas += (float)$sc->subTotal;
                                                }
                                            }
                                        ?>
                                        <input class="span12" type="text" value="R$ <?php echo number_format($somaSistemas, 2, ',', '.'); ?>" readonly style="background-color: #f9f9f9;" />
                                    </div>
                                    <div class="span4">
                                        <label><strong>Descontos / Acréscimos:</strong></label>
                                        <?php
                                            $descAcresStr = 'R$ 0,00';
                                            if ($result->valorDesconto > 0) {
                                                $descAcresStr = 'Desconto: ' . ($result->tipoDesconto == '%' ? $result->valorDesconto . '%' : 'R$ ' . number_format($result->valorDesconto, 2, ',', '.'));
                                            } elseif ($result->valorAcrescimo > 0) {
                                                $descAcresStr = 'Acréscimo: ' . ($result->tipoAcrescimo == '%' ? $result->valorAcrescimo . '%' : 'R$ ' . number_format($result->valorAcrescimo, 2, ',', '.'));
                                            }
                                        ?>
                                        <input class="span12" type="text" value="<?php echo $descAcresStr; ?>" readonly style="background-color: #f9f9f9;" />
                                    </div>
                                    <div class="span4">
                                        <label><strong>Valor Mensal / Total Atualizado:</strong></label>
                                        <input class="span12" type="text" value="R$ <?php echo number_format($result->valorTotal, 2, ',', '.'); ?>" readonly style="background-color: #eee; font-weight: bold; color: #333;" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ABA ORDENS DE SERVIÇO -->
                        <div class="tab-pane" id="tab3">
                            <div class="span12" style="margin-left: 0;">
                                <h4>Ordens de Serviço do Contrato</h4>
                                <div class="table-responsive" style="overflow-x: auto;">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center;">Nº O.S.</th>
                                                <th style="text-align: center;">Data</th>
                                                <th style="text-align: center;">Status</th>
                                                <th>Técnico</th>
                                                <th style="text-align: right;">Valor Total</th>
                                                <th style="text-align: center;">Status NFS-e</th>
                                                <th style="text-align: center;">Ações NFS-e</th>
                                                <th style="text-align: center;">Ações OS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($os)): ?>
                                                <tr><td colspan="6" style="text-align: center;">Nenhuma O.S. vinculada a este contrato.</td></tr>
                                            <?php else: ?>
                                                <?php foreach ($os as $o): ?>
                                                    <?php
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
                                                        $valTotalOs = isset($o->valorTotal) && $o->valorTotal != 0 ? $o->valorTotal : (($o->totalProdutos ?? 0) + ($o->totalServicos ?? 0));
                                                    ?>
                                                    <tr>
                                                        <td style="text-align: center;"><?php echo $o->idOs; ?></td>
                                                        <td style="text-align: center;"><?php echo date('d/m/Y', strtotime($o->dataInicial)); ?></td>
                                                        <td style="text-align: center;"><span class="badge" style="background-color:<?php echo $cor; ?>; border-color:<?php echo $cor; ?>"><?php echo $o->status; ?></span></td>
                                                        <td><?php echo $tecnico; ?></td>
                                                        <td style="text-align: right;">R$ <?php echo number_format($valTotalOs, 2, ',', '.'); ?></td>
                                                        <td style="text-align: center;">
                                                            <?php if (!empty($o->asaas_invoice_status)): ?>
                                                                <?php
                                                                $statusNota = $o->asaas_invoice_status;
                                                                $badgeColorNfse = 'info';
                                                                $statusTxtNfse = $statusNota;
                                                                if ($statusNota === 'SCHEDULED') { $badgeColorNfse = 'warning'; $statusTxtNfse = 'Agendada'; }
                                                                elseif ($statusNota === 'AUTHORIZED') { $badgeColorNfse = 'success'; $statusTxtNfse = 'Autorizada'; }
                                                                elseif ($statusNota === 'PROCESSING') { $badgeColorNfse = 'info'; $statusTxtNfse = 'Processando'; }
                                                                elseif ($statusNota === 'CANCELED' || $statusNota === 'CANCELLED') { $badgeColorNfse = 'important'; $statusTxtNfse = 'Cancelada'; }
                                                                elseif ($statusNota === 'ERROR') { $badgeColorNfse = 'important'; $statusTxtNfse = 'Erro'; }
                                                                ?>
                                                                <span class="label label-<?php echo $badgeColorNfse; ?>"><?php echo $statusTxtNfse; ?></span>
                                                                <?php if (!empty($o->asaas_invoice_number)): ?>
                                                                    <br><small>#<?php echo $o->asaas_invoice_number; ?></small>
                                                                <?php endif; ?>
                                                            <?php else: ?>
                                                                -
                                                            <?php endif; ?>
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <?php if (!empty($o->asaas_invoice_pdf)): ?>
                                                                <a href="<?php echo $o->asaas_invoice_pdf; ?>" target="_blank" class="btn btn-mini btn-info tip-top" title="Baixar PDF"><i class="bx bxs-file-pdf"></i></a>
                                                            <?php endif; ?>
                                                            <?php if (!empty($o->asaas_invoice_xml)): ?>
                                                                <a href="<?php echo $o->asaas_invoice_xml; ?>" target="_blank" class="btn btn-mini btn-warning tip-top" title="Baixar XML"><i class="bx bx-code-alt"></i></a>
                                                            <?php endif; ?>
                                                            <?php if (empty($o->asaas_invoice_pdf) && empty($o->asaas_invoice_xml)): ?>
                                                                -
                                                            <?php endif; ?>
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <a href="<?php echo base_url(); ?>index.php/mine/visualizarOs/<?php echo $o->idOs; ?>" class="btn-nwe" title="Visualizar OS"><i class="bx bx-show-alt"></i></a>
                                                            <a href="<?php echo base_url(); ?>index.php/mine/imprimirOs/<?php echo $o->idOs; ?>" target="_blank" class="btn-nwe3" title="Imprimir OS"><i class="bx bx-printer"></i></a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- ABA VENDAS -->
                        <div class="tab-pane" id="tab4">
                            <div class="span12" style="margin-left: 0;">
                                <h4>Vendas do Contrato</h4>
                                <div class="table-responsive" style="overflow-x: auto;">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center;">Nº Venda</th>
                                                <th style="text-align: center;">Data</th>
                                                <th>Vendedor</th>
                                                <th style="text-align: right;">Valor Total</th>
                                                <th style="text-align: center;">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($vendas)): ?>
                                                <tr><td colspan="5" style="text-align: center;">Nenhuma Venda vinculada a este contrato.</td></tr>
                                            <?php else: ?>
                                                <?php foreach ($vendas as $v): ?>
                                                    <tr>
                                                        <td style="text-align: center;"><?php echo $v->idVendas; ?></td>
                                                        <td style="text-align: center;"><?php echo date('d/m/Y', strtotime($v->dataVenda)); ?></td>
                                                        <td><?php echo $v->nome; ?></td>
                                                        <td style="text-align: right;">R$ <?php echo number_format($v->valorTotal, 2, ',', '.'); ?></td>
                                                        <td style="text-align: center;">
                                                            <a href="<?php echo base_url(); ?>index.php/mine/visualizarCompra/<?php echo $v->idVendas; ?>" class="btn-nwe" title="Visualizar Venda"><i class="bx bx-show-alt"></i></a>
                                                            <a href="<?php echo base_url(); ?>index.php/mine/imprimirCompra/<?php echo $v->idVendas; ?>" target="_blank" class="btn-nwe3" title="Imprimir Venda"><i class="bx bx-printer"></i></a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- ABA ANEXOS -->
                        <div class="tab-pane" id="tab5">
                            <div class="span12" style="margin-left: 0;">
                                <h4>Anexos do Contrato</h4>
                                <div class="span12 pull-left" id="divAnexos" style="margin-left: 0; margin-top: 15px;">
                                    <?php if (empty($anexos)): ?>
                                        <p style="text-align: center; width: 100%;">Nenhum anexo disponível para este contrato.</p>
                                    <?php else: ?>
                                        <?php foreach ($anexos as $a): ?>
                                            <?php
                                                $ext = strtolower(pathinfo($a->anexo, PATHINFO_EXTENSION));
                                                $link = $a->url . '/' . $a->anexo;
                                                if ($a->thumb == null) {
                                                    if ($ext == 'pdf') {
                                                        $thumb_content = '<div style="height: 120px; display: flex; align-items: center; justify-content: center; background: #f9f9f9;"><i class="fas fa-file-pdf" style="font-size: 60px; color: #e74c3c;"></i></div>';
                                                    } else {
                                                        $thumb = base_url() . 'assets/img/icon-file.png';
                                                        $thumb_content = '<img src="' . $thumb . '" alt="' . $a->anexo . '" style="max-height: 120px;">';
                                                    }
                                                } else {
                                                    $thumb = $a->url . '/thumbs/' . $a->thumb;
                                                    $thumb_content = '<img src="' . $thumb . '" alt="' . $a->anexo . '" style="max-height: 120px;">';
                                                }
                                            ?>
                                            <div class="span3" style="min-height: 180px; margin-left: 0; margin-right: 15px; margin-bottom: 15px; text-align: center;">
                                                <a style="min-height: 130px; display: block; border: 1px solid #ddd; overflow: hidden; background: #fff; padding: 5px;" href="#modal-anexo" imagem="<?php echo $a->idAnexos; ?>" link="<?php echo $link; ?>" role="button" class="btn anexo span12" data-toggle="modal">
                                                    <?php echo $thumb_content; ?>
                                                </a>
                                                <div style="margin-top: 5px;">
                                                    <a href="<?php echo base_url(); ?>index.php/mine/downloadanexo/<?php echo $a->idAnexos; ?>" class="btn btn-mini btn-success" style="width: 90%;"><i class="bx bx-download"></i> Baixar</a>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- ABA SISTEMAS -->
                        <div class="tab-pane" id="tab6">
                            <div class="span12" style="margin-left: 0;">
                                <h4>Sistemas e Equipamentos do Contrato</h4>
                                <div class="table-responsive" style="overflow-x: auto;">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Sistema</th>
                                                <th>Local</th>
                                                <th style="text-align: right;">Sub-total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($sistemas_contrato)): ?>
                                                <tr><td colspan="3" style="text-align: center;">Nenhum sistema ou equipamento vinculado.</td></tr>
                                            <?php else: ?>
                                                <?php
                                                    $totalSist = 0;
                                                    foreach ($sistemas_contrato as $s):
                                                        $totalSist += $s->subTotal;
                                                ?>
                                                    <tr>
                                                        <td><?php echo $s->nome; ?></td>
                                                        <td><?php echo $s->local; ?></td>
                                                        <td style="text-align: right;">R$ <?php echo number_format($s->subTotal, 2, ',', '.'); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                <tr>
                                                    <td colspan="2" style="text-align: right;"><strong>Total de Sistemas:</strong></td>
                                                    <td style="text-align: right;"><strong>R$ <?php echo number_format($totalSist, 2, ',', '.'); ?></strong></td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- ABA CHECKLISTS DO CONTRATO -->
                        <div class="tab-pane" id="tab8">
                            <div class="span12" style="margin-left: 0;">
                                <h4>Checklists do Contrato</h4>
                                <div class="table-responsive" style="overflow-x: auto;">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center; width: 15%;"># OS</th>
                                                <th style="text-align: center; width: 15%;">Nº Checklist</th>
                                                <th style="text-align: center; width: 25%;">Data</th>
                                                <th style="text-align: center; width: 30%;">Técnico</th>
                                                <th style="text-align: center; width: 15%;">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($checklists)): ?>
                                                <tr><td colspan="5" style="text-align: center;">Nenhum checklist preenchido para este contrato.</td></tr>
                                            <?php else: ?>
                                                <?php foreach ($checklists as $chk): ?>
                                                    <tr>
                                                        <td style="text-align: center; vertical-align: middle;"><?php echo $chk->os_id; ?></td>
                                                        <td style="text-align: center; vertical-align: middle;"><?php echo sprintf('%04d', $chk->idChecklist); ?></td>
                                                        <td style="text-align: center; vertical-align: middle;"><?php echo date('d/m/Y', strtotime($chk->data_checklist ?: $chk->data_criacao)); ?></td>
                                                        <td style="text-align: center; vertical-align: middle;"><?php echo !empty($chk->nome_tecnico) ? $chk->nome_tecnico : '—'; ?></td>
                                                        <td style="text-align: center; vertical-align: middle;">
                                                            <a href="<?php echo base_url(); ?>index.php/mine/imprimirChecklist/<?php echo $chk->os_id; ?>" target="_blank" class="btn-nwe6" title="Visualizar e Imprimir Checklist"><i class="bx bx-printer"></i></a>
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
                            <div class="span12" style="margin-left: 0;">
                                <h4>Cobranças</h4>
                                <div class="table-responsive" style="overflow-x: auto;">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center;">Nº Cobrança</th>
                                                <th style="text-align: center;">Vencimento</th>
                                                <th style="text-align: center;">Status</th>
                                                <th style="text-align: right;">Valor</th>
                                                <th style="text-align: center;">Visualizar Boleto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($faturas)): ?>
                                                <tr><td colspan="5" style="text-align: center;">Nenhuma cobrança ou fatura vinculada a este contrato.</td></tr>
                                            <?php else: ?>
                                                <?php foreach ($faturas as $f): ?>
                                                    <?php
                                                        $idCob = !empty($f->charge_id) ? $f->charge_id : ($f->idCobranca ?? '—');
                                                        $venc = !empty($f->expire_at) ? date('d/m/Y', strtotime($f->expire_at)) : (!empty($f->vencimento) ? date('d/m/Y', strtotime($f->vencimento)) : '—');
                                                        $val = !empty($f->total) ? ($f->total / 100) : (!empty($f->valor) ? $f->valor : 0);
                                                        $linkBoleto = !empty($f->link) ? $f->link : (!empty($f->url_boleto) ? $f->url_boleto : '');
                                                    ?>
                                                    <tr>
                                                        <td style="text-align: center;"><?php echo $idCob; ?></td>
                                                        <td style="text-align: center;"><?php echo $venc; ?></td>
                                                        <td style="text-align: center;"><?php echo $f->status; ?></td>
                                                        <td style="text-align: right;">R$ <?php echo number_format($val, 2, ',', '.'); ?></td>
                                                        <td style="text-align: center;">
                                                            <?php if (!empty($linkBoleto)): ?>
                                                                <a href="<?php echo $linkBoleto; ?>" target="_blank" class="btn-nwe" title="Visualizar Boleto / Fatura"><i class="bx bx-barcode bx-md"></i></a>
                                                            <?php else: ?>
                                                                <span class="badge" style="background-color: #999;">Indisponível</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- ABA NFS-e -->
                        <div class="tab-pane" id="tab10">
                            <div class="span12" style="margin-left: 0;">
                                <h4>Notas Fiscais de Serviço (NFS-e)</h4>
                                <div class="table-responsive" style="overflow-x: auto;">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center;">Nº O.S.</th>
                                                <th style="text-align: center;">Status Asaas</th>
                                                <th style="text-align: center;">Nº NFS-e</th>
                                                <th style="text-align: center;">PDF</th>
                                                <th style="text-align: center;">XML</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $temNfse = false;
                                                if (!empty($os)) {
                                                    foreach ($os as $o) {
                                                        if (!empty($o->asaas_invoice_status)) {
                                                            $temNfse = true;
                                                            $statusNota = $o->asaas_invoice_status;
                                                            $badgeColorNfse = 'info';
                                                            $statusTxtNfse = $statusNota;
                                                            if ($statusNota === 'SCHEDULED') { $badgeColorNfse = 'warning'; $statusTxtNfse = 'Agendada'; }
                                                            elseif ($statusNota === 'AUTHORIZED') { $badgeColorNfse = 'success'; $statusTxtNfse = 'Autorizada'; }
                                                            elseif ($statusNota === 'PROCESSING') { $badgeColorNfse = 'info'; $statusTxtNfse = 'Processando'; }
                                                            elseif ($statusNota === 'CANCELED' || $statusNota === 'CANCELLED') { $badgeColorNfse = 'important'; $statusTxtNfse = 'Cancelada'; }
                                                            elseif ($statusNota === 'ERROR') { $badgeColorNfse = 'important'; $statusTxtNfse = 'Erro'; }
                                            ?>
                                                            <tr>
                                                                <td style="text-align: center;"><?php echo $o->idOs; ?></td>
                                                                <td style="text-align: center;">
                                                                    <span class="label label-<?php echo $badgeColorNfse; ?>"><?php echo $statusTxtNfse; ?></span>
                                                                    <?php if (!empty($o->asaas_invoice_error)): ?>
                                                                        <br><small style="color: red;"><?php echo htmlspecialchars($o->asaas_invoice_error); ?></small>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td style="text-align: center;"><?php echo !empty($o->asaas_invoice_number) ? $o->asaas_invoice_number : '-'; ?></td>
                                                                <td style="text-align: center;">
                                                                    <?php if (!empty($o->asaas_invoice_pdf)): ?>
                                                                        <a href="<?php echo $o->asaas_invoice_pdf; ?>" target="_blank" class="btn btn-mini btn-info tip-top" title="Baixar PDF"><i class="bx bxs-file-pdf"></i></a>
                                                                    <?php else: ?>
                                                                        -
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td style="text-align: center;">
                                                                    <?php if (!empty($o->asaas_invoice_xml)): ?>
                                                                        <a href="<?php echo $o->asaas_invoice_xml; ?>" target="_blank" class="btn btn-mini btn-warning tip-top" title="Baixar XML"><i class="bx bx-code-alt"></i></a>
                                                                    <?php else: ?>
                                                                        -
                                                                    <?php endif; ?>
                                                                </td>
                                                            </tr>
                                            <?php
                                                        }
                                                    }
                                                }
                                                if (!$temNfse):
                                            ?>
                                                <tr><td colspan="5" style="text-align: center;">Nenhuma NFS-e vinculada às Ordens de Serviço deste contrato.</td></tr>
                                            <?php endif; ?>
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
        <a href="" class="btn btn-inverse" id="download-anexo-modal">Download</a>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', '.anexo', function(event) {
            event.preventDefault();
            var link = $(this).attr('link');
            var id = $(this).attr('imagem');
            var ext = link.split('.').pop().toLowerCase();
            
            if (ext === 'pdf') {
                $("#div-visualizar-anexo").html('<iframe src="' + link + '" width="100%" height="400px" style="border: none;"></iframe>');
            } else if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'heic'].includes(ext)) {
                $("#div-visualizar-anexo").html('<img src="' + link + '" alt="" style="max-height: 400px; max-width: 100%;">');
            } else {
                $("#div-visualizar-anexo").html('<div><i class="fas fa-file-alt" style="font-size: 100px; color: #555;"></i><br><br>Documento</div>');
            }

            $("#download-anexo-modal").attr('href', "<?php echo base_url(); ?>index.php/mine/downloadanexo/" + id);
        });
    });
</script>
