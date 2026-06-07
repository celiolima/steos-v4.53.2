<?php
$totalServico = 0;
$totalProdutos = 0;
?>
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
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
    <style>
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #FAFAFA;
        }

        th,
        td {
            padding: none;
            /*  font-size: 10px; */
            /*  background-color: blue; */

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
                                                    <td colspan="3" class="alert">Você precisa configurar os dados do emitente. >>><a href="<?php echo base_url(); ?>index.php/mapos/emitente">Configurar</a>
                                                        <<<< /td>
                                                </tr> <?php } else { ?><td style="width: 20%"><img src=" <?php echo $emitente->url_logo; ?> "></td>
                                                <td>
                                                    <span style="font-size: 20px;"><?php echo $emitente->nome; ?></span></br>
                                                    <?php if ($emitente->cnpj != "00.000.000/0000-00") { ?><span class="icon"><i class="fas fa-fingerprint" style="margin:5px 1px"></i> <?php echo $emitente->cnpj; ?></span></br><?php } ?>
                                                    <span class="icon"><i class="fas fa-map-marker-alt" style="margin:4px 3px"></i><?php echo $emitente->rua . ', ' . $emitente->numero . ', ' . $emitente->bairro . ' - ' . $emitente->cidade . ' - ' . $emitente->uf; ?></span></br>
                                                    <span><span class="icon"><i class="fas fa-comments" style="margin:5px 1px"></i> E-mail: <?php echo $emitente->email . ' - Fone: ' . $emitente->telefone; ?></br>
                                                            <span class="icon"><i class="fas fa-user-check"></i> Responsável: <?php echo $result->nome ?>
                                                <td style="width: 20%; text-align: center; border: 1px solid #D2D4DE; border-radius: 5px;">
                                                    <h3><span><b>RECIBO</b></span></h3>
                                                    <h4><span><b>N° OS:<?php echo $result->idOs ?></b></span></h4>
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
                                    <?php if ($produtos != null || $servicos != null) {
                                        echo "<h5><b>PRODUTOS E SERVIÇOS</b></h5>";
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
                                            $saldoTotal = number_format($result->valor_desconto, 2, ',', '.');
                                        } else {
                                            echo "<h4 style='text-align: right'>TOTAL: R$ " . number_format($totalProdutos + $totalServico, 2, ',', '.') . "</h4>";
                                            $saldoTotal = number_format($totalProdutos + $totalServico, 2, ',', '.');
                                        }
                                    } ?>

                                    <!-- RECEBENDO AS VARIAVEIS PARA SUBSTITUIA NO MODELO -->
                                    <?php
                                    $valor = $saldoTotal;
                                    $nomeCliente = $result->nomeCliente;
                                    $nOs = $result->idOs;
                                    $nF = "xxxx";
                                    $data = date("d,F,Y");
                                    //$nF =  if(!empty($modelos)){}; 
                                    ?>

                                    <!--  tabela RECIBO-->
                                    <table class="table table-bordered table-condensed" style=" border: 1px solid #D2D4DE; border-radius: 5px;">
                                        <tbody>

                                            <tr>
                                                <td>
                                                    <?php if (!empty($modelos)) {
                                                        foreach ($modelos as $modelo) {

                                                            if ($result->status == "Finalizado") {
                                                                if ($modelo->refModelo == "RECIBO") {
                                                                    //echo " <strong>TERMOS </strong><br>";
                                                                    $texto_db = htmlspecialchars_decode($modelo->textoModelo);
                                                                    echo str_replace(
                                                                        array(
                                                                            '{{valor}}',
                                                                            '{{nomeCliente}}',
                                                                            '{{nOs}}',
                                                                            '{{nF}}',
                                                                            '{{data}}'
                                                                        ),
                                                                        array(
                                                                            $valor,
                                                                            $nomeCliente,
                                                                            $nOs,
                                                                            $nF,
                                                                            $data
                                                                        ),
                                                                        $texto_db
                                                                    );
                                                                }
                                                            }
                                                        }
                                                    } ?>
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