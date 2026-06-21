<?php $totalServico = 0;
$totalProdutos = 0; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title>STE_OS_<?php echo $result->idOs ?>_<?php echo $result->nomeCliente ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/matrix-style.css" />
    <link href="<?php echo base_url(); ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href="<?= base_url('assets/css/custom.css'); ?>" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
    <style>
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #FAFAFA;
        }

        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 4mm;
            margin: 1mm auto;
            border: 1px #D3D3D3 solid;
            border-radius: 5px;
            background: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .subpage {
            padding: 0.5cm;
            border: 0px red solid;
            height: 257mm;
            outline: 2cm #FFEAEA solid;
        }

        @page {
            size: A4;
            margin: 0;
        }

        @media print {

            html,
            body {
                width: 210mm;
                height: 297mm;
            }

            .page {
                margin: 0;
                border: initial;
                border-radius: initial;
                width: initial;
                min-height: initial;
                box-shadow: initial;
                background: initial;
                page-break-after: always;
            }
        }
    </style>
</head>

<body style="background-color: rgba(0,0,0,.4)" id=body>
    <div id="principal">
        <div class="book">
            <div class="container-fluid page" id="viaCliente">
                <div class="subpage"><?php echo (!$configuration['control_2vias']) ? "" : "Via Cliente" ?>
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="invoice-content">
                                <!--  heder -->
                                <div class="invoice-head" style="margin-bottom: 0">
                                    <!-- linha01 - tabela01 -->
                                    <!--  CABEÇALHO -->
                                    <table class="table table-condensed">
                                        <tbody>
                                            <?php if ($emitente == null) { ?>
                                                <tr>
                                                    <td colspan="3" class="alert">Você precisa configurar os dados do emitente. >>><a href="<?php echo base_url(); ?>index.php/steos/emitente">Configurar</a>
                                                        <<<< /td>
                                                </tr> <?php } else { ?><td style="width: 20%"><img src=" <?php echo $emitente->url_logo; ?> "></td>
                                                <td>
                                                    <span style="font-size: 20px;"><?php echo $emitente->nome; ?></span></br>
                                                    <?php if ($emitente->cnpj != "00.000.000/0000-00") { ?><span class="icon"><i class="fas fa-fingerprint" style="margin:5px 1px"></i> <?php echo $emitente->cnpj; ?></span></br><?php } ?>
                                                    <span class="icon"><i class="fas fa-map-marker-alt" style="margin:4px 3px"></i><?php echo $emitente->rua . ', ' . $emitente->numero . ', ' . $emitente->bairro . ' - ' . $emitente->cidade . ' - ' . $emitente->uf; ?></span></br>
                                                    <span><span class="icon"><i class="fas fa-comments" style="margin:5px 1px"></i> E-mail: <?php echo $emitente->email . ' - Fone: ' . $emitente->telefone; ?></br>
                                                            <span class="icon"><i class="fas fa-user-check"></i> Responsável: <?php echo $result->nome ?>
                                                <td style="width: 18%; text-align: center; border: 1px solid #D2D4DE; border-radius: 5px;">
                                                    <h4><span><b>N° OS:</b></span></h4>
                                                    <h3><span><b><?php echo $result->idOs ?></b></span></h3>
                                                    <span>Emissão: <?php echo date('d/m/Y') ?></span>
                                                </td></span>
                                                </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    <!-- linha02 - tabela02 -->
                                    <!--  CLIENTE -->
                                    <table class="table table-condensend" style=" border: 1px solid #D2D4DE; border-radius: 10px;">
                                        <tbody>
                                            <tr>
                                                <td style=" width: 50%; ">
                                                    <ul>
                                                        <li>
                                                            <span>
                                                                <h5><b>CLIENTE</b></h5>
                                                                <span><?php echo $result->nomeCliente ?></span><br />
                                                                <span><?php echo "CNPJ/CPF: " . $result->documento ?></span><br />
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
                                                <td style="width: 25%; padding-left: 0">
                                                    <ul>
                                                        <li>
                                                            <span>
                                                                <h5><b>TÉCNICOS</b></h5>

                                                            </span>
                                                            <?php if (!empty($tecnicos_os)) {
                                                                foreach ($tecnicos_os as $tecnico) { ?>
                                                                    <span><?php echo $tecnico->nome ?></span> <br />
                                                            <?php }
                                                            } ?>
                                                        </li>
                                                    </ul>
                                                </td>
                                                <?php if ($result->status == 'Finalizado' || $result->status == 'Aprovado') { ?>
                                                    <?php if ($qrCode) : ?>
                                                        <td style="width: 15%; padding: 0;text-align:center;">
                                                            <img style="margin:12px 0px 0px 0px" src="<?php echo base_url(); ?>assets/img/logo_pix.png" width="64px" alt="QR Code de Pagamento" /></br>
                                                            <img style="margin:5px 0px 0px 0px" width="94px" src="<?= $qrCode ?>" alt="QR Code de Pagamento" /></br>
                                                            <?php echo '<span style="margin:0px;font-size: 80%;text-align:center;">Chave PIX: ' . $chaveFormatada . '</span>'; ?>
                                                        </td>
                                                    <?php endif ?>
                                                <?php } ?>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!--  body -->
                                <div style="margin-top: 0; padding-top: 0">
                                    <!--  tabela03 status data inicial final termo de garantia-->
                                    <table class="table table-condensed">
                                        <tbody>
                                            <?php if ($result->dataInicial != null) { ?>
                                                <tr>
                                                    <td>
                                                        <b>STATUS OS: </b>
                                                        <?php echo $result->status ?>
                                                    </td>
                                                    <td>
                                                        <b>DATA INICIAL: </b>
                                                        <?php echo date('d/m/Y', strtotime($result->dataInicial)); ?>
                                                    </td>
                                                    <td>
                                                        <b>DATA FINAL: </b>
                                                        <?php echo $result->dataFinal ? date('d/m/Y', strtotime($result->dataFinal)) : ''; ?>
                                                    </td>
                                                    <?php if ($result->garantia) {
                                                    ?>
                                                        <td>
                                                            <b>GARANTIA: </b>
                                                            <?php echo $result->garantia . ' dia(s)'; ?>
                                                        </td>
                                                    <?php
                                                    } ?>
                                                    <td>
                                                        <b>
                                                            <?php if ($result->status == 'Finalizado') { ?>
                                                                VENC. DA GARANTIA:
                                                        </b>
                                                        <?php echo dateInterval($result->dataFinal, $result->garantia); ?><?php } ?>
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
                                            <?php if ($result->laudoTecnico != null) { ?>
                                                <tr>
                                                    <td colspan="5">
                                                        <b>LAUDO TÉCNICO: </b>
                                                        <?php echo htmlspecialchars_decode($result->laudoTecnico) ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($result->garantias_id != null) { ?>
                                                <tr>
                                                    <td colspan="5">
                                                        <strong>TERMO DE GARANTIA </strong><br>
                                                        <?php echo htmlspecialchars_decode($result->textoGarantia) ?>
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
                                                            <strong id=" <?php echo $i; ?>equipamentos_label"><?php echo $equipamento->equipamento; ?> </strong>
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

                                                        <td colspan="7" style="text-align: left; ">Local:&nbsp<a id="<?php echo $i; ?>local" style="color: green"><input disabled id="<?php echo $i; ?>local_value" name="equipamentos[<?php echo $i; ?>][local_value]" value="<?php echo $equipamento->local; ?>" style="width:90%; border: none;  " /></a></td>

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
                                    <?php if ($produtos != null || $servicos != null) {
                                        echo "</br><h5><b>PRODUTOS E SERVIÇOS</b></h5>";
                                    } ?>
                                    <!--  tabela05 Produtos-->
                                    <?php if ($produtos != null) { ?>
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
                                                    $totalProdutos = $totalProdutos + $p->subTotal;
                                                    echo '<tr>';
                                                    echo '<td>' . $p->descricao . '</td>';
                                                    echo '<td>' . $p->quantidade . '</td>';
                                                    echo '<td>' . $p->preco ?: $p->precoVenda . '</td>';
                                                    echo '<td>R$ ' . number_format($p->subTotal, 2, ',', '.') . '</td>';
                                                    echo '</tr>';
                                                } ?>
                                                <tr>
                                                    <td colspan=" 3" style="text-align: right"><strong>TOTAL:</strong></td>
                                                    <td><strong>R$ <?php echo number_format($totalProdutos, 2, ',', '.'); ?></strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    <?php } ?>
                                    <!--  tabela06 Serviços-->
                                    <?php if ($servicos != null) { ?>
                                        <table class="table table-bordered table-condensed" style=" border: 1px solid #D2D4DE; border-radius: 5px;">
                                            <thead>
                                                <tr>
                                                    <th>SERVIÇOS</th>
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
                                                    $totalServico = $totalServico + $subtotal;
                                                    echo '<tr>';
                                                    echo '<td>' . $s->nome . '</td>';
                                                    echo '<td>' . ($s->quantidade ?: 1) . '</td>';
                                                    echo '<td>R$ ' . $preco . '</td>';
                                                    echo '<td>R$ ' . number_format($subtotal, 2, ',', '.') . '</td>';
                                                    echo '</tr>';
                                                } ?>
                                                <tr>
                                                    <td colspan=" 3" style="text-align: right"><strong>TOTAL:</strong></td>
                                                    <td><strong>R$ <?php echo number_format($totalServico, 2, ',', '.'); ?></strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    <?php } ?>
                                    <?php if ($totalProdutos != 0 || $totalServico != 0) {
                                        if ($result->valor_desconto != 0) {
                                            echo "<h4 style='text-align: right'>SUBTOTAL: R$ " . number_format($totalProdutos + $totalServico, 2, ',', '.') . "</h4>";
                                            echo $result->valor_desconto != 0 ? "<h4 style='text-align: right'>DESCONTO: R$ " . number_format($result->valor_desconto != 0 ? $result->valor_desconto - ($totalProdutos + $totalServico) : 0.00, 2, ',', '.') . "</h4>" : "";
                                            echo "<h4 style='text-align: right'>TOTAL: R$ " . number_format($result->valor_desconto, 2, ',', '.') . "</h4>";
                                        } else {
                                            echo "<h4 style='text-align: right'>TOTAL: R$ " . number_format($totalProdutos + $totalServico, 2, ',', '.') . "</h4>";
                                        }
                                    } ?>
                                    <?php if ($result->status == "Finalizado") { ?>
                                        <!--  tabela07 documentos-->
                                        <table class="table table-bordered table-condensed">
                                            <tbody>
                                                <?php if ($result->garantias_id != null) { ?>
                                                    <tr style=" border: 1px solid #D2D4DE; border-radius: 5px;">
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

                                    <!--  tabela09 assinaturas-->
                                    <?php if ($result->status == "Finalizado") { ?>
                                        <table class="table table-bordered table-condensed" style="padding-top: 20px">
                                            <tbody>
                                                <tr>
                                                    <td style="width: 20%; padding: 0;text-align:center;">Documento</br>
                                                        <a style="color: blue;width: 90%;"><?php echo  $assinatura->doc; ?></a>
                                                        <hr style="color: blue;width: 90%;">
                                                    </td>
                                                    <td style="width: 40%; padding: 0;text-align:center;">Assinatura do Cliente</br>
                                                        <?php echo '<img  src="' . $assinatura->assinatura . '"style="width: 70%;">'; ?>
                                                        <hr style="color: blue;width: 90%;">
                                                    </td>
                                                    <td style="width: 40%; padding: 0;text-align:center;">Nome</br>
                                                        <a style="color: blue;width: 90%;"><?php echo  $assinatura->nameAssinatura; ?></a>
                                                        <hr style="color: blue;width: 90%;">
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
            <!-- VIA EMPRESA  -->
            <?php $totalServico = 0;
            $totalProdutos = 0; ?>
            <div class="container-fluid page" id="ViaEmpresa" <?php echo (!$configuration['control_2vias']) ? "style='display: none;'" : "style='display: block;'" ?>>
                <div class="subpage">Via Empresa
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="invoice-content">
                                <!--  heder -->
                                <div class="invoice-head" style="margin-bottom: 0">
                                    <!-- linha01 - tabela01 -->
                                    <table class="table table-condensed">
                                        <tbody>
                                            <?php if ($emitente == null) { ?>
                                                <tr>
                                                    <td colspan="3" class="alert">Você precisa configurar os dados do emitente. >>><a href="<?php echo base_url(); ?>index.php/steos/emitente">Configurar</a>
                                                        <<<< /td>
                                                </tr> <?php } else { ?><td style="width: 20%"><img src=" <?php echo $emitente->url_logo; ?> "></td>
                                                <td>
                                                    <span style="font-size: 17px;"><?php echo $emitente->nome; ?></span></br>
                                                    <?php if ($emitente->cnpj != "00.000.000/0000-00") { ?><span class="icon"><i class="fas fa-fingerprint" style="margin:5px 1px"></i> <?php echo $emitente->cnpj; ?></span></br><?php } ?>
                                                    <span class="icon"><i class="fas fa-map-marker-alt" style="margin:4px 3px"></i><?php echo $emitente->rua . ', ' . $emitente->numero . ', ' . $emitente->bairro . ' - ' . $emitente->cidade . ' - ' . $emitente->uf; ?></span></br>
                                                    <span><span class="icon"><i class="fas fa-comments" style="margin:5px 1px"></i> E-mail: <?php echo $emitente->email . ' - Fone: ' . $emitente->telefone; ?></br>
                                                            <span class="icon"><i class="fas fa-user-check"></i> Responsável: <?php echo $result->nome ?>
                                                <td style="width: 18%; text-align: center"><b>N° OS:</b> <span><?php echo $result->idOs ?></span></br></br><span>Emissão: <?php echo date('d/m/Y') ?></span></td></span>
                                                </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    <!-- linha02 - tabela02 -->
                                    <table class="table table-condensend">
                                        <tbody>
                                            <tr>
                                                <td style="width: 85%; padding-left: 0">
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
                                                <?php if ($result->status == 'Finalizado' || $result->status == 'Aprovado') { ?>
                                                    <?php if ($qrCode) : ?>
                                                        <td style="width: 15%; padding: 0;text-align:center;">
                                                            <img style="margin:12px 0px 0px 0px" src="<?php echo base_url(); ?>assets/img/logo_pix.png" width="64px" alt="QR Code de Pagamento" /></br>
                                                            <img style="margin:5px 0px 0px 0px" width="94px" src="<?= $qrCode ?>" alt="QR Code de Pagamento" /></br>
                                                            <?php echo '<span style="margin:0px;font-size: 80%;text-align:center;">Chave PIX: ' . $chaveFormatada . '</span>'; ?>
                                                        </td>
                                                    <?php endif ?>
                                                <?php } ?>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!--  body -->
                                <div style="margin-top: 0; padding-top: 0">
                                    <!--  tabela03 status data inicial final termo de garantia-->
                                    <table class="table table-condensed">
                                        <tbody>
                                            <?php if ($result->dataInicial != null) { ?>
                                                <tr>
                                                    <td>
                                                        <b>STATUS OS: </b>
                                                        <?php echo $result->status ?>
                                                    </td>
                                                    <td>
                                                        <b>DATA INICIAL: </b>
                                                        <?php echo date('d/m/Y', strtotime($result->dataInicial)); ?>
                                                    </td>
                                                    <td>
                                                        <b>DATA FINAL: </b>
                                                        <?php echo $result->dataFinal ? date('d/m/Y', strtotime($result->dataFinal)) : ''; ?>
                                                    </td>
                                                    <?php if ($result->garantia) {
                                                    ?>
                                                        <td>
                                                            <b>GARANTIA: </b>
                                                            <?php echo $result->garantia . ' dia(s)'; ?>
                                                        </td>
                                                    <?php
                                                    } ?>
                                                    <td>
                                                        <b>
                                                            <?php if ($result->status == 'Finalizado') { ?>
                                                                VENC. DA GARANTIA:
                                                        </b>
                                                        <?php echo dateInterval($result->dataFinal, $result->garantia); ?><?php } ?>
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
                                                        <b>DEFEITO APRESENTADO: </b>
                                                        <?php echo htmlspecialchars_decode($result->defeito) ?>
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
                                            <?php if ($result->laudoTecnico != null) { ?>
                                                <tr>
                                                    <td colspan="5">
                                                        <b>LAUDO TÉCNICO: </b>
                                                        <?php echo htmlspecialchars_decode($result->laudoTecnico) ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($result->garantias_id != null) { ?>
                                                <tr>
                                                    <td colspan="5">
                                                        <strong>TERMO DE GARANTIA </strong><br>
                                                        <?php echo htmlspecialchars_decode($result->textoGarantia) ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    <?php if ($produtos != null) { ?>
                                        <table class="table table-bordered table-condensed" id="tblProdutos">
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
                                                    $totalProdutos = $totalProdutos + $p->subTotal;
                                                    echo '<tr>';
                                                    echo '<td>' . $p->descricao . '</td>';
                                                    echo '<td>' . $p->quantidade . '</td>';
                                                    echo '<td>R$ ' . $p->preco ?: $p->precoVenda . '</td>';
                                                    echo '<td>R$ ' . number_format($p->subTotal, 2, ',', '.') . '</td>';
                                                    echo '</tr>';
                                                } ?>
                                                <tr>
                                                    <td colspan="3" style="text-align: right"><strong>TOTAL:</strong></td>
                                                    <td><strong>R$ <?php echo number_format($totalProdutos, 2, ',', '.'); ?></strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    <?php } ?>
                                    <?php if ($servicos != null) { ?>
                                        <table class="table table-bordered table-condensed">
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
                                                    $totalServico = $totalServico + $subtotal;
                                                    echo '<tr>';
                                                    echo '<td>' . $s->nome . '</td>';
                                                    echo '<td>' . ($s->quantidade ?: 1) . '</td>';
                                                    echo '<td>R$ ' . $preco . '</td>';
                                                    echo '<td>R$ ' . number_format($subtotal, 2, ',', '.') . '</td>';
                                                    echo '</tr>';
                                                } ?>
                                                <tr>
                                                    <td colspan="3" style="text-align: right"><strong>TOTAL:</strong></td>
                                                    <td><strong>R$ <?php echo number_format($totalServico, 2, ',', '.'); ?></strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    <?php } ?>

                                    <?php if ($totalProdutos != 0 || $totalServico != 0) {
                                        if ($result->valor_desconto != 0) {
                                            echo "<h4 style='text-align: right'>SUBTOTAL: R$ " . number_format($totalProdutos + $totalServico, 2, ',', '.') . "</h4>";
                                            echo $result->valor_desconto != 0 ? "<h4 style='text-align: right'>DESCONTO: R$ " . number_format($result->valor_desconto != 0 ? $result->valor_desconto - ($totalProdutos + $totalServico) : 0.00, 2, ',', '.') . "</h4>" : "";
                                            echo "<h4 style='text-align: right'>TOTAL: R$ " . number_format($result->valor_desconto, 2, ',', '.') . "</h4>";
                                        } else {
                                            echo "<h4 style='text-align: right'>TOTAL: R$ " . number_format($totalProdutos + $totalServico, 2, ',', '.') . "</h4>";
                                        }
                                    } ?>
                                    <table class="table table-bordered table-condensed" style="padding-top: 20px">
                                        <tbody>
                                            <tr>
                                                <td>Data
                                                    <hr>
                                                </td>
                                                <td>Assinatura do Cliente
                                                    <hr>
                                                </td>
                                                <td>Assinatura do Responsável
                                                    <hr>
                                                </td>
                                            </tr>
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
        window.print();
    </script>
</body>

</html>