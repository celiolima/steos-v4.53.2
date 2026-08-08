<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title>Lançamentos Financeiros</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/matrix-style.css" />
    <link href="<?php echo base_url(); ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href="<?= base_url('assets/css/custom.css'); ?>" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
    
    <!-- Scripts for Export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

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
            size: A4 landscape; /* Using landscape because there are 10 columns */
            margin: 0;
        }

        @media print {
            html,
            body {
                width: 297mm;
                height: 210mm;
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

        .table-condensed th, .table-condensed td {
            font-size: 11px;
            padding: 4px;
        }
    </style>
</head>

<body style="background-color: rgba(0,0,0,.4)" id="body">
    <div id="principal">
        <div class="book">
            <div class="container-fluid page" id="viaCliente">
                <div class="subpage">
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="invoice-content">
                                <!--  CABEÇALHO -->
                                <div class="invoice-head" style="margin-bottom: 0">
                                    <table class="table table-condensed">
                                        <tbody>
                                            <?php if ($emitente == null) { ?>
                                                <tr>
                                                    <td colspan="3" class="alert">Você precisa configurar os dados do emitente.</td>
                                                </tr> 
                                            <?php } else { ?>
                                                <tr>
                                                    <td style="width: 20%">
                                                        <img src="<?php echo base_url('assets/uploads/' . basename($emitente->url_logo)); ?>" style="max-height: 100px">
                                                    </td>
                                                    <td>
                                                        <span style="font-size: 20px;"><?php echo $emitente->nome; ?></span></br>
                                                        <?php if ($emitente->cnpj != "00.000.000/0000-00") { ?>
                                                            <span class="icon"><i class="fas fa-fingerprint" style="margin:5px 1px"></i> <?php echo $emitente->cnpj; ?></span></br>
                                                        <?php } ?>
                                                        <span class="icon"><i class="fas fa-map-marker-alt" style="margin:4px 3px"></i><?php echo $emitente->rua . ', ' . $emitente->numero . ', ' . $emitente->bairro . ' - ' . $emitente->cidade . ' - ' . $emitente->uf; ?></span></br>
                                                        <span class="icon"><i class="fas fa-comments" style="margin:5px 1px"></i> E-mail: <?php echo $emitente->email . ' - Fone: ' . $emitente->telefone; ?></span>
                                                    </td>
                                                    <td style="width: 25%; text-align: center; border: 1px solid #D2D4DE; border-radius: 5px;">
                                                        <h4><span><b>Lançamentos Financeiros</b></span></h4>
                                                        <span style="font-size: 11px;">Relatório emitido em: <?php echo date('d/m/Y H:i'); ?></span>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- CORPO -->
                                <div style="margin-top: 15px;">
                                    <table class="table table-bordered table-condensed" id="tabelaLancamentos">
                                        <thead>
                                            <tr style="background-color: #2D335B; color: white;">
                                                <th style="color: white;">#</th>
                                                <th style="color: white;">Tipo</th>
                                                <th style="color: white;">Cliente / Fornecedor</th>
                                                <th style="color: white;">Forma de Pagamento</th>
                                                <th style="color: white;">Centro de Gastos</th>
                                                <th style="color: white;">Classificação Fin.</th>
                                                <th style="color: white;">Grupo Financeiro</th>
                                                <th style="color: white;">Vencimento</th>
                                                <th style="color: white;">Status</th>
                                                <th style="color: white;">Valor Total (=)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (!$results) {
                                                echo '<tr><td colspan="10" style="text-align: center;">Nenhum lançamento encontrado para os filtros informados.</td></tr>';
                                            } else {
                                                foreach ($results as $r) {
                                                    $vencimento = date('d/m/Y', strtotime($r->data_vencimento));

                                                    if ($r->baixado == 0) {
                                                        $status = 'Pendente';
                                                    } else {
                                                        $status = 'Pago';
                                                    };
                                                    
                                                    if ($r->tipo == 'receita') {
                                                        $cor = '#00a65a'; // Verde
                                                    } else {
                                                        $cor = '#dd4b39'; // Vermelho
                                                    }

                                                    $valorTotal = $r->valor_desconto != 0 ? $r->valor_desconto : $r->valor;

                                                    echo '<tr>';
                                                    echo '<td>' . $r->idLancamentos . '</td>';
                                                    echo '<td><span style="color: ' . $cor . '; font-weight: bold;">' . ucfirst($r->tipo) . '</span></td>';
                                                    echo '<td>' . htmlspecialchars($r->cliente_fornecedor) . '</td>';
                                                    echo '<td>' . htmlspecialchars($r->forma_pgto) . '</td>';
                                                    echo '<td>' . htmlspecialchars($r->centro_de_gastos) . '</td>';
                                                    echo '<td>' . htmlspecialchars($r->classificacao_fin) . '</td>';
                                                    echo '<td>' . htmlspecialchars($r->grupo_finaceiro) . '</td>';
                                                    echo '<td>' . $vencimento . '</td>';
                                                    echo '<td>' . $status . '</td>';
                                                    echo '<td> R$ ' . number_format($valorTotal, 2, ',', '.') . '</td>';
                                                    echo '</tr>';
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- RODAPÉ - ESTATÍSTICAS -->
                                <?php if($results) { ?>
                                <div style="margin-top: 15px; padding: 10px; background-color: #f9f9f9; border: 1px solid #ddd; border-radius: 5px;">
                                    <h5 style="margin-top: 0;">Resumo dos Valores</h5>
                                    <table class="table table-condensed" style="margin-bottom: 0; width: 100%;">
                                        <tr>
                                            <td style="border: none; width: 33%;">
                                                <strong>Total Receitas:</strong> 
                                                <span style="color: #00a65a;">R$ <?php echo number_format($totals['receitas'], 2, ',', '.'); ?></span>
                                            </td>
                                            <td style="border: none; width: 33%;">
                                                <strong>Total Despesas:</strong> 
                                                <span style="color: #dd4b39;">R$ <?php echo number_format($totals['despesas'], 2, ',', '.'); ?></span>
                                            </td>
                                            <td style="border: none; width: 33%;">
                                                <strong>Saldo:</strong> 
                                                <?php $saldo = $totals['receitas'] - $totals['despesas']; ?>
                                                <span style="color: <?php echo $saldo >= 0 ? '#00a65a' : '#dd4b39'; ?>;">
                                                    R$ <?php echo number_format($saldo, 2, ',', '.'); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <?php } ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        window.onload = function() {
            var format = '<?php echo $format; ?>';
            
            if (format === 'print') {
                window.print();
            } else if (format === 'pdf') {
                exportPDF();
            } else if (format === 'xlsx') {
                exportXLSX();
            } else if (format === 'csv') {
                exportCSV();
            }
        };

        function exportPDF() {
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF('landscape');
            
            pdf.text("Lançamentos Financeiros - Relatório", 14, 15);
            pdf.autoTable({ 
                html: '#tabelaLancamentos', 
                startY: 20,
                styles: { fontSize: 8 },
                theme: 'grid',
                headStyles: { fillColor: [45, 51, 91] } // #2D335B
            });
            pdf.save('lancamentos_financeiros.pdf');
            // Fechar aba apos gerar (opcional, pode ser irritante as vezes)
            setTimeout(() => { window.close(); }, 500);
        }

        function exportXLSX() {
            const table = document.getElementById('tabelaLancamentos');
            const wb = XLSX.utils.table_to_book(table, {sheet: "Lançamentos"});
            XLSX.writeFile(wb, "lancamentos_financeiros.xlsx");
            setTimeout(() => { window.close(); }, 500);
        }

        function exportCSV() {
            const table = document.getElementById('tabelaLancamentos');
            const wb = XLSX.utils.table_to_book(table, {sheet: "Lançamentos"});
            const csv = XLSX.utils.sheet_to_csv(wb.Sheets["Lançamentos"]);
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement("a");
            const url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", "lancamentos_financeiros.csv");
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            setTimeout(() => { window.close(); }, 500);
        }
    </script>
</body>
</html>
