<?php
//echo "<pre>";
//print_r($modelos);
//print_r($equipamentos);
//print_r($tecnicos_os);
//die();
?>

<link href="<?= base_url('assets/css/custom.css'); ?>" rel="stylesheet">
<div class="row-fluid" style="margin-top: 0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-diagnoses"></i>
                </span>
                <h5>Dados da Ordem de Serviço</h5>
                <!-- butões -->
                <div class="buttons">
                    <?php if ($editavel) {
                        if (($result->status != 'Finalizado') || $this->session->userdata('permissao') == '1') {
                            echo '<a title="Editar OS" class="button btn btn-mini btn-success" href="' . base_url() . 'index.php/os/editar/' . $result->idOs . '">
                                <span class="button__icon"><i class="bx bx-edit"></i> </span> <span class="button__text">Editar</span></a>';
                        }
                    } ?>

                    <a target="_blank" title="Imprimir OS" class="button btn btn-mini btn-inverse" href="<?php echo site_url() ?>/os/imprimir/<?php echo $result->idOs; ?>">
                        <span class="button__icon"><i class="bx bx-printer"></i></span> <span class="button__text">Papel A4</span></a>
                    <?php if ($result->status == "Finalizado") { ?>
                        <a target="_blank" title="Imprimir OS Recibo" class="button btn btn-mini btn-inverse" href="<?php echo site_url() ?>/os/Recibo/<?php echo $result->idOs; ?>">
                            <span class="button__icon"><i class="bx bx-printer"></i></span> <span class="button__text">Recibo</span></a>
                    <?php } ?>
                    <a target="_blank" title="Imprimir OS" class="button btn btn-mini btn-inverse" href="<?php echo site_url() ?>/os/imprimirTermica/<?php echo $result->idOs; ?>">
                        <span class="button__icon"><i class="bx bx-printer"></i></span> <span class="button__text">CP Não Fiscal</span></a>
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) {
                        $this->load->model('os_model');
                        $zapnumber = preg_replace("/[^0-9]/", "", $result->celular_cliente);
                        $troca = [$result->nomeCliente, $result->idOs, $result->status, 'R$ ' . ($result->desconto != 0 && $result->valor_desconto != 0 ? number_format($result->valor_desconto, 2, ',', '.') : number_format($totalProdutos + $totalServico, 2, ',', '.')), strip_tags($result->descricaoProduto), ($emitente ? $emitente->nome : ''), ($emitente ? $emitente->telefone : ''), strip_tags($result->observacoes), strip_tags($result->defeito), strip_tags($result->laudoTecnico), date('d/m/Y', strtotime($result->dataFinal)), date('d/m/Y', strtotime($result->dataInicial)), $result->garantia . ' dias'];
                        $texto_de_notificacao = $this->os_model->criarTextoWhats($texto_de_notificacao, $troca);
                        if (!empty($zapnumber)) {
                            echo '<a title="Enviar Por WhatsApp" class="button btn btn-mini btn-success" id="enviarWhatsApp" target="_blank" href="https://api.whatsapp.com/send?phone=55' . $zapnumber . '&text=' . $texto_de_notificacao . '">
                               <span class="button__icon"><i class="bx bxl-whatsapp"></i></span> <span class="button__text">WhatsApp</span></a>';
                        }
                    } ?>

                    <a title="Enviar por E-mail" class="button btn btn-mini btn-warning" href="<?php echo site_url() ?>/os/enviar_email/<?php echo $result->idOs; ?>">
                        <span class="button__icon"><i class="bx bx-envelope"></i></span> <span class="button__text">Via E-mail</span></a>
                    <?php if ($result->garantias_id) { ?> <a target="_blank" title="Imprimir Termo de Garantia" class="button btn btn-mini btn-inverse" href="<?php echo site_url() ?>/garantias/imprimirGarantiaOs/<?php echo $result->garantias_id; ?>">
                            <span class="button__icon"><i class="bx bx-printer"></i></span> <span class="button__text">Garantia</span></a> <?php } ?>
                    <a href="#modal-gerar-pagamento" id="btn-forma-pagamento" role="button" data-toggle="modal" class="button btn btn-mini btn-info">
                        <span class="button__icon"><i class='bx bx-qr'></i></span><span class="button__text">Gerar Pagamento</span></a></i>
                </div>
            </div>
            <div class="widget-content" id="printOs">
                <div class="invoice-content">
                    <!--  heder -->
                    <div class="invoice-head" style="margin-bottom: 0">
                        <!-- linha01 - tabela01 -->
                        <table class="table table-condensed">
                            <tbody>
                                <?php if ($emitente == null) { ?>
                                    <tr>
                                        <td colspan="3" class="alert">Você precisa configurar os dados do emitente. >>><a href="<?php echo base_url(); ?>index.php/mapos/emitente">Configurar</a>
                                            <<< </td>
                                    </tr> <?php } else { ?>
                                    <tr>
                                        <td style="width: 25%"><img src=" <?php echo $emitente->url_logo; ?> " style="max-height: 100px"></td>
                                        <td style="width: 50%">
                                            <span style="font-size: 20px;"><?php echo $emitente->nome; ?></span></br>
                                            <?php if ($emitente->cnpj != "00.000.000/0000-00") { ?><span class="icon"><i class="fas fa-fingerprint" style="margin:5px 1px"></i> <?php echo $emitente->cnpj; ?></span></br><?php } ?>
                                            <span class="icon"><i class="fas fa-map-marker-alt" style="margin:4px 3px"></i><?php echo $emitente->rua . ', ' . $emitente->numero . ', ' . $emitente->bairro . ' - ' . $emitente->cidade . ' - ' . $emitente->uf; ?></span></br>
                                            <span class="icon"><i class="fas fa-comments" style="margin:5px 1px"></i> E-mail: <?php echo $emitente->email . ' - Fone: ' . $emitente->telefone; ?></span>
                                        </td>
                                        <td style="width: 25%; text-align: center;border: 1px solid #D2D4DE; border-radius: 5px;">
                                            <h3><span><b>N° OS:</b></span></h3>
                                            <h2><span><b><?php echo $result->idOs ?></b></span></h2>
                                            <span>Emissão: <?php echo date('d/m/Y') ?></span>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <!-- linha02 - tabela02 -->
                        <table class="table table-condensend" style=" border: 1px solid #D2D4DE; border-radius: 5px;">
                            <tbody>
                                <tr>
                                    <td style=" width: 40%; ">
                                        <ul>
                                            <li>
                                                <span>
                                                    <h5><b>CLIENTE</b></h5>
                                                    <span><?php echo $result->nomeCliente ?></span><br />
                                                    <?php
                                                    $retorno_end = array_filter([$result->rua, $result->numero, $result->complemento, $result->bairro]);
                                                    $endereco = implode(', ', $retorno_end);
                                                    if (!empty($endereco)) {
                                                        echo $endereco . '<br>';
                                                    }
                                                    if (!empty($result->cidade) || !empty($result->estado) || !empty($result->cep)) {
                                                        echo "<span>{$result->cidade} - {$result->estado}, {$result->cep}</span><br>";
                                                    }
                                                    ?>
                                                    <?php if (!empty($result->email)) : ?>
                                                        <span>E-mail: <?php echo $result->email ?></span><br>
                                                    <?php endif; ?>
                                                    <?php if (!empty($result->celular_cliente) || !empty($result->telefone_cliente) || !empty($result->contato_cliente)) : ?>
                                                        <span>Contato: <?= !empty($result->contato_cliente) ? $result->contato_cliente . ' ' : "" ?>
                                                            <?php if ($result->celular_cliente == $result->telefone_cliente) { ?>
                                                                <?= $result->celular_cliente ?>
                                                            <?php } else { ?>
                                                                <?= !empty($result->telefone_cliente) ? $result->telefone_cliente : "" ?>
                                                                <?= !empty($result->celular_cliente) && !empty($result->telefone_cliente) ? ' / ' : "" ?>
                                                                <?= !empty($result->celular_cliente) ? $result->celular_cliente : "" ?>
                                                            <?php } ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </span>
                                            </li>
                                        </ul>
                                    </td>
                                    <?php
                                    //echo "<pre>";
                                    //print_r($equipamentos);
                                    //print_r($tecnicos_os);
                                    //exit;
                                    ?>

                                    <td style=" width: 30%; padding-left: 0">
                                        <ul>
                                            <li>
                                                <span>
                                                    <h5><b>RESPONSÁVEL</b></h5>
                                                </span>
                                                <span><?php echo $result->nome ?></span> <br />
                                                <span>Contato: <?php echo $result->telefone_usuario ?></span><br />
                                                <span>Email: <?php echo $result->email_usuario ?></span>
                                            </li>
                                            <li>
                                                <span>
                                                    <h5><b>Técnicos</b></h5>
                                                </span>
                                                <?php if (!empty($tecnicos_os)) {
                                                    foreach ($tecnicos_os as $tecnico) { ?>
                                                        <span><?php echo $tecnico->nome ?></span> <br />
                                                <?php }
                                                } ?>
                                            </li>
                                        </ul>
                                    </td>

                                    <?php if ($qrCode) : ?>
                                        <td style="width: 15%; padding: 0;text-align:center;">
                                            <img style="margin:12px 0px 0px 0px" src="<?php echo base_url(); ?>assets/img/logo_pix.png" width="64px" alt="QR Code de Pagamento" /></br>
                                            <img style="margin:5px 0px 0px 0px" width="94px" src="<?= $qrCode ?>" alt="QR Code de Pagamento" /></br>
                                            <?php echo '<span style="margin:0px;font-size: 80%;text-align:center;">Chave PIX: ' . $chaveFormatada . '</span>'; ?>
                                        </td>
                                    <?php endif ?>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                    <!--  body -->
                    <div style="margin-top: 0; padding-top: 0; ">
                        <!--  tabela03 status data inicial final termo de garantia-->
                        <table class="table table-condensed ">
                            <tbody>
                                <?php if ($result->dataInicial != null) { ?>
                                    <tr>
                                        <td>
                                            <b>STATUS OS: </b><?php echo $result->status ?>
                                        </td>

                                        <td>
                                            <b>DATA INICIAL: </b><?php echo date('d/m/Y', strtotime($result->dataInicial)); ?>
                                        </td>

                                        <td>
                                            <b>DATA FINAL: </b><?php echo $result->dataFinal ? date('d/m/Y', strtotime($result->dataFinal)) : ''; ?>
                                        </td>

                                        <td>
                                            <?php if ($result->garantia) { ?>
                                                <b>GARANTIA: </b><?php echo $result->garantia . ' dia(s)'; ?>
                                            <?php } ?>
                                        </td>

                                        <td>
                                            <?php if ($result->status == 'Finalizado') { ?>
                                                <b>VENC. DA GARANTIA:</b><?php echo dateInterval($result->dataFinal, $result->garantia); ?>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>

                                <?php if ($result->descricaoProduto != null) { ?>
                                    <tr>
                                        <td colspan="5">
                                            <b>DESCRIÇÃO: </b>
                                            <?php echo htmlspecialchars_decode($result->descricaoProduto) ?>
                                        </td>
                                    </tr>
                                <?php } ?>

                                <?php if ($result->defeito != null) { ?>
                                    <tr>
                                        <td colspan="5">
                                            <b>DEFEITO RECLAMADO ATENDIMENTO: </b>
                                            <?php echo htmlspecialchars_decode($result->defeito) ?>
                                        </td>
                                    </tr>
                                <?php } ?>

                                <?php if ($result->defeito_encontrado != null) { ?>
                                    <tr>
                                        <td colspan="5">
                                            <b>DEFEITO ENCONTRADO ATENDIMENTO: </b>
                                            <?php echo htmlspecialchars_decode($result->defeito_encontrado) ?>
                                        </td>
                                    </tr>
                                <?php } ?>

                                <?php if ($result->observacoes != null) { ?>
                                    <tr>
                                        <td colspan="5">
                                            <b>OBSERVAÇÕES: </b>
                                            <?php echo htmlspecialchars_decode($result->observacoes) ?>
                                        </td>
                                    </tr>
                                <?php } ?>


                            </tbody>
                        </table>
                        <!--  tabela04 Eqipamentos-->
                        <?php if ($equipamentos != null) { ?>
                            <h5><b>EQUIPAMENTOS</b></h5>
                            <?php $i = 10;
                            foreach ($equipamentos as $equipamento) {
                                $i++;  ?>
                                <table class="table table-bordered" style=" border: 1px solid #D2D4DE; border-radius: 5px;">
                                    <thead>
                                        <tr>
                                            <td>
                                                <strong id="<?php echo $i; ?>equipamentos_label"><?php echo $equipamento->equipamento; ?> </strong>
                                                <!--  <td colspan="7" style="text-align: left; "><a id="<?php echo $i; ?>equipamentos" name="equipamentos[<?php echo $i; ?>][equipamentos]"></a></td> -->
                                                <input id="<?php echo $i; ?>equipamentos_value" class="span12" type="hidden" name="equipamentos[<?php echo $i; ?>][equipamentos_value]" value="<?php echo $equipamento->equipamento; ?>" />
                                                <input id="<?php echo $i; ?>equipamento_id" class="span12" name="equipamentos[<?php echo $i; ?>][equipamento_id]" type="hidden" value="<?php echo $equipamento->idEquipamentos_os; ?>" />
                                                <input id="<?php echo $i; ?>os_id" class="span12" name="equipamentos[<?php echo $i; ?>][os_id]" type="hidden" value="<?php echo $equipamento->os_id; ?>" />
                                                <input id="<?php echo $i; ?>clientes_id" class="span12" name="equipamentos[<?php echo $i; ?>][clientes_id]" type="hidden" value="<?php echo $equipamento->clientes_id; ?>" />
                                            </td>
                                            <td>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="7" style="text-align: left; ">Numero de Serie:<a id="<?php echo $i; ?>serie" style="color: green">&nbsp<?php echo $equipamento->serie; ?></a></td>
                                            <input id="<?php echo $i; ?>serie_value" class="span12" type="hidden" name="equipamentos[<?php echo $i; ?>][serie_value]" value="<?php echo $equipamento->serie; ?>" />

                                            <td colspan="7" style="text-align: left; ">Modelo:<a id="<?php echo $i; ?>modelo" style="color: green">&nbsp<?php echo $equipamento->modelo; ?></a></td>
                                            <input id="<?php echo $i; ?>modelo_value" class="span12" type="hidden" name="equipamentos[<?php echo $i; ?>][modelo_value]" value="<?php echo $equipamento->modelo; ?>" />
                                        </tr>
                                        <tr>
                                            <td colspan="7" style="text-align: left; ">Cor:<a id="<?php echo $i; ?>cor" style="color: green">&nbsp<?php echo $equipamento->cor; ?></a></td>
                                            <input id="<?php echo $i; ?>cor_value" class="span12" type="hidden" name="equipamentos[<?php echo $i; ?>][cor_value]" value="<?php echo $equipamento->cor; ?>" />

                                            <td colspan="7" style="text-align: left; ">Descricao:<a id="<?php echo $i; ?>descricao" style="color: green">&nbsp<?php echo $equipamento->descricao; ?></a></td>
                                            <input id="<?php echo $i; ?>descricao_value" class="span12" type="hidden" name="equipamentos[<?php echo $i; ?>][descricao_value]" value="<?php echo $equipamento->descricao; ?>" />
                                        </tr>
                                        <tr>
                                            <td colspan="7" style="text-align: left; ">Potência:<a id="<?php echo $i; ?>potencia" style="color: green">&nbsp<?php echo $equipamento->potecia; ?></a></td>
                                            <input id="<?php echo $i; ?>potencia_value" class="span12" type="hidden" name="equipamentos[<?php echo $i; ?>][potencia_value]" value="<?php echo $equipamento->potecia; ?>" />

                                            <td colspan="7" style="text-align: left; ">Voltagem:<a id="<?php echo $i; ?>voltagem" style="color: green">&nbsp<?php echo $equipamento->voltagem; ?></a></td>
                                            <input id="<?php echo $i; ?>voltagem_value" class="span12" type="hidden" name="equipamentos[<?php echo $i; ?>][voltagem_value]" value="<?php echo $equipamento->voltagem; ?>" />
                                        </tr>
                                        <tr>
                                            <td colspan="7" style="text-align: left; ">Marcas:<a id="<?php echo $i; ?>marcas" style="color: green">&nbsp<?php echo $equipamento->marca; ?></a></td>
                                            <input id="<?php echo $i; ?>marcas_value" class="span12" type="hidden" name="equipamentos[<?php echo $i; ?>][marcas_value]" value="<?php echo $equipamento->marca; ?>" />

                                            <td colspan="7" style="text-align: left; ">Local:&nbsp<a id="<?php echo $i; ?>local" style="color: green"><input disabled id="<?php echo $i; ?>local_value" name="equipamentos[<?php echo $i; ?>][local_value]" value="<?php echo $equipamento->local; ?>" style="width:90%; border: none; " /></a></td>

                                        </tr>
                                        <tr>
                                            <td colspan="7" style="text-align: left; ">
                                                <label>Defeito Reclamado</label>
                                                <textarea disabled name="equipamentos[<?php echo $i; ?>][defeito_relatado]" id="<?php echo $i; ?>defeito_relatado" placeholder="insira o defeito reclamado" value="<?php echo $equipamento->defeito_declarado; ?>" style="width:100%"><?php echo $equipamento->defeito_declarado; ?></textarea>
                                            </td>
                                            <td colspan="7" style="text-align: left; ">
                                                <label> Defeito Encontrado</label>
                                                <textarea disabled name="equipamentos[<?php echo $i; ?>][defeito_encontrado]" id="<?php echo $i; ?>defeito_encontardo" placeholder="insira o defeito encontrado" value="<?php echo $equipamento->defeito_encontrado; ?>" style="width:100%"><?php echo $equipamento->defeito_encontrado; ?></textarea>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            <?php } ?>
                        <?php } ?>
                        <!--  tabela05 Anotações-->
                        <?php if ($anotacoes != null) { ?>
                            <table class="table table-bordered" style=" border: 1px solid #D2D4DE; border-radius: 5px;">
                                <thead>
                                    <tr>
                                        <th>Anotação</th>
                                        <th>Data/Hora</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($anotacoes as $a) {
                                        echo '<tr>';
                                        echo '<td>' . $a->anotacao . '</td>';
                                        echo '<td>' . date('d/m/Y H:i:s', strtotime($a->data_hora)) . '</td>';
                                        echo '</tr>';
                                    }
                                    if (!$anotacoes) {
                                        echo '<tr><td colspan="2">Nenhuma anotação cadastrada</td></tr>';
                                    } ?>
                                </tbody>
                            </table>
                        <?php } ?>
                        <!--  tabela06 Anexos-->
                        <?php if ($anexos != null) { ?>
                            <table class="table table-bordered table-condensed" style=" border: 1px solid #D2D4DE; border-radius: 5px;">
                                <thead>
                                    <tr>
                                        <th>Anexo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <th colspan="5">
                                        <?php foreach ($anexos as $a) {
                                            if ($a->thumb == null) {
                                                $thumb = base_url() . 'assets/img/icon-file.png';
                                                $link = base_url() . 'assets/img/icon-file.png';
                                            } else {
                                                $thumb = $a->url . '/thumbs/' . $a->thumb;
                                                $link = $a->url . '/' . $a->anexo;
                                            }
                                            echo '<div class="span3" style="min-height: 150px; margin-left: 0"><a style="min-height: 150px;" href="#modal-anexo" imagem="' . $a->idAnexos . '" link="' . $link . '" role="button" class="btn anexo span12" data-toggle="modal"><img src="' . $thumb . '" alt=""></a></div>';
                                        } ?>
                                    </th>
                                </tbody>
                            </table>
                        <?php } ?>
                        <?php if ($produtos != null || $servicos != null) {
                            echo "</br><h5><b>PRODUTOS E SERVIÇOS</b></h5>";
                        } ?>
                        <!--  tabela07 Produtos-->
                        <?php if ($produtos != null) { ?>
                            <br />
                            <table class="table table-bordered table-condensed" id="tblProdutos" style=" border: 1px solid #D2D4DE; border-radius: 5px;">
                                <thead>
                                    <tr>
                                        <th>PRODUTO</th>
                                        <th>QTD</th>
                                        <th>UNT</th>
                                        <th>SUBTOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($produtos as $p) {
                                        echo '<tr>';
                                        echo '<td>' . $p->descricao . '</td>';
                                        echo '<td>' . $p->quantidade . '</td>';
                                        echo '<td>R$ ' . $p->preco ?: $p->precoVenda . '</td>';
                                        echo '<td>R$ ' . number_format($p->subTotal, 2, ',', '.') . '</td>';
                                        echo '</tr>';
                                    } ?>
                                    <tr>
                                        <td></td>
                                        <td colspan="2" style="text-align: right"><strong>TOTAL:</strong></td>
                                        <td><strong>R$ <?php echo number_format($totalProdutos, 2, ',', '.'); ?></strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        <?php } ?>
                        <!--  tabela07 Serviços-->
                        <?php if ($servicos != null) { ?>
                            <table class="table table-bordered table-condensed" style=" border: 1px solid #D2D4DE; border-radius: 5px;">
                                <thead>
                                    <tr>
                                        <th>SERVIÇO</th>
                                        <th>QTD</th>
                                        <th>UNT</th>
                                        <th>SUBTOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php setlocale(LC_MONETARY, 'en_US');
                                    foreach ($servicos as $s) {
                                        $preco = $s->preco ?: $s->precoVenda;
                                        $subtotal = $preco * ($s->quantidade ?: 1);
                                        echo '<tr>';
                                        echo '<td>' . $s->nome . '</td>';
                                        echo '<td>' . ($s->quantidade ?: 1) . '</td>';
                                        echo '<td>R$ ' . $preco . '</td>';
                                        echo '<td>R$ ' . number_format($subtotal, 2, ',', '.') . '</td>';
                                        echo '</tr>';
                                    } ?>
                                    <tr>
                                        <td colspan="3" style="text-align: right"><strong>TOTAL:</strong></td>
                                        <td><strong>R$ <?php echo number_format($totalServico, 2, ',', '.'); ?></strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        <?php } ?>
                        <!--  tabela08 Totais-->
                        <?php if ($totalProdutos != 0 || $totalServico != 0) {
                            if ($result->valor_desconto != 0) {
                                echo "<h4 style='text-align: right'>SUBTOTAL: R$ " . number_format($totalProdutos + $totalServico, 2, ',', '.') . "</h4>";
                                echo $result->valor_desconto != 0 ? "<h4 style='text-align: right'>DESCONTO: R$ " . number_format($result->valor_desconto != 0 ? $result->valor_desconto - ($totalProdutos + $totalServico) : 0.00, 2, ',', '.') . "</h4>" : "";
                                echo "<h4 style='text-align: right'>TOTAL: R$ " . number_format($result->valor_desconto, 2, ',', '.') . "</h4>";
                            } else {
                                echo "<h4 style='text-align: right'>TOTAL: R$ " . number_format($totalProdutos + $totalServico, 2, ',', '.') . "</h4>";
                            }
                            echo "</br>";
                        } ?>
                        <!--  tabela09 Documentos-->
                        <?php if ($result->status == "Finalizado") { ?>
                            <table class="table table-condensed">
                                <tbody>
                                    <?php if ($result->garantias_id != null) { ?>
                                        <tr>
                                            <td colspan="5">
                                                <strong>TERMO DE GARANTIA </strong><br>
                                                <?php echo htmlspecialchars_decode($result->textoGarantia) ?>
                                            </td>
                                        </tr>
                                    <?php } ?>

                                    <?php if ($result->laudoTecnico != null) { ?>
                                        <tr>
                                            <td colspan="5">
                                                <b>LAUDO TÉCNICO: </b>
                                                <?php echo htmlspecialchars_decode($result->laudoTecnico) ?>
                                            </td>
                                        </tr>
                                    <?php } ?>

                                </tbody>
                            </table>
                        <?php } ?>
                        <!--  tabela08 modelos-->
                        <?php if ($modelos != null) { ?>
                            <table class="table table-bordered table-condensed" style=" border: 1px solid #D2D4DE; border-radius: 5px;">

                                <tbody>
                                    <tr>
                                        <td>
                                            <?php $tipoModelo = "avista"; ?>
                                            <?php if (!empty($modelos)) {
                                                foreach ($modelos as $modelo) {
                                                    if ($result->status == "Negociação") {
                                                        if ($tipoModelo == "avista") {
                                                            if ($modelo->refModelo == "VENDA AVISTA") {
                                                                //echo " <strong>TERMOS </strong><br>";
                                                                echo htmlspecialchars_decode($modelo->textoModelo);
                                                            }
                                                        }
                                                        if ($tipoModelo == "prazo") {
                                                            if ($modelo->refModelo == "VENDA A PRAZO") {
                                                                //echo " <strong>TERMOS </strong><br>";
                                                                echo htmlspecialchars_decode($modelo->textoModelo);
                                                            }
                                                        }
                                                    }
                                                    if ($result->status == "A Sair | Aguard Conclusão") {
                                                        if ($modelo->refModelo == "CHECKLIST") {
                                                            //echo " <strong>TERMOS </strong><br>";
                                                            echo htmlspecialchars_decode($modelo->textoModelo);
                                                        }
                                                    }
                                                }
                                            } ?>
                                        </td>
                                    </tr>


                                </tbody>
                            </table>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $modalGerarPagamento ?>

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
        $(document).on('click', '.anexo', function(event) {
            event.preventDefault();
            var link = $(this).attr('link');
            var id = $(this).attr('imagem');
            var url = '<?php echo base_url(); ?>index.php/os/excluirAnexo/';
            $("#div-visualizar-anexo").html('<img src="' + link + '" alt="">');
            $("#excluir-anexo").attr('link', url + id);

            $("#download").attr('href', "<?php echo base_url(); ?>index.php/os/downloadanexo/" + id);

        });

        $(document).on('click', '#excluir-anexo', function(event) {
            event.preventDefault();

            var link = $(this).attr('link');
            var idOS = "<?php echo $result->idOs; ?>"

            $('#modal-anexo').modal('hide');
            $("#divAnexos").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");

            $.ajax({
                type: "POST",
                url: link,
                dataType: 'json',
                data: "idOs=" + idOS,
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
    });
</script>