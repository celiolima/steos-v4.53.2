<!DOCTYPE html>
<html>

<head>
    <title>STEOS</title>
    <meta charset="UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/fullcalendar.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/main.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/blue.css" class="skin-color" />
</head>
<style>
    th {
        padding: 0px;
        margin: 0px;
        border: 1px solid black;
        height: 15px;
    }
</style>

<?php

$totalVendedor = 0;
$comissaoProdutos = 0;


if (!empty($results)) {
    foreach ($results as $r) {

        if (!empty($r['totalProdutos'])) {
            $totalVendedor += (float)$r['totalProdutos'];
            $comissaoProdutos += (float)$r['totalProdutos']  * 0.03;
        }
    }
}
?>

<body style="background-color: transparent">
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
                <div class="widget-box">
                    <?= $topo ?>
                    <div class="widget-title">
                        <h4 style="text-align: center; font-size: 1em; padding: 5px;">
                            <?= ucfirst($title) ?>
                        </h4>
                    </div>
                    <div class="widget_content nopadding">
                        <div class="table-responsive ">
                            <table style=" font-size: 11px;table-layout: auto; width: 100%;padding: 0px; margin: 0px;border: 1px solid black;">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th width="40%">Cliente</th>
                                        <th>Data Final</th>
                                        <th>Valor Produtos</th>
                                        <th>Vend. <?php if (!empty($this->input->post('vendedor'))) {
                                                        echo $this->input->post('vendedor');
                                                    }; ?></th>
                                        <th>Ações</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                    if (!empty($results)) {
                                        $this->load->model('os_model');
                                        foreach ($results as $r) {
                                            /* echo "<pre> comissaoOs :<br>";
                                            print_r($r);
                                            echo "</pre>";
                                            exit; */

                                            $dataInicial = date(('d/m/Y'), strtotime($r['dataInicial']));
                                            if ($r['dataFinal'] != null) {
                                                $dataFinal = date(('d/m/Y'), strtotime($r['dataFinal']));
                                            } else {
                                                $dataFinal = "";
                                            }
                                            echo '<tr >';
                                            echo '<td style="text-align: center;">' . $r['idOs'] . '</td>';
                                            echo '<td style=" border: "><a href="' . base_url() . 'index.php/clientes/visualizar/' . $r['idClientes'] . '" >' . $r['nomeCliente'] . '</a></td>';
                                            echo '<td style="text-align: center;">' . $dataFinal . '</td>';
                                            echo '<td style="text-align: center; border: ">R$ ' . number_format($r['totalProdutos'], 2, ',', '.') . '</td>';
                                            echo '<td style="text-align: center; border: ">R$ ' . number_format((float)$r['totalProdutos']  * 0.03, 2, ',', '.') . '</td>';
                                            echo '<td style="text-align: center;">' . '<a  href="' . base_url() . 'index.php/os/visualizar/' . $r['idOs'] . '"  >Ver</a>' . '</td>';
                                            echo '</tr>';
                                        }
                                    } ?>
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <td colspan="6" style="text-align: center; color: blue">
                                            <strong>Produção do Vendedor:</strong>
                                            <strong>R$ <?php if (!empty($totalVendedor)) {
                                                            echo number_format($totalVendedor, 2, ',', '.');
                                                        } ?></strong>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td colspan="6" style="text-align: center; color: green">
                                            <strong>Total de Comissões:</strong>
                                            <strong>R$ <?php if (!empty($comissaoProdutos)) {
                                                            echo number_format($comissaoProdutos, 2, ',', '.');
                                                        } ?></strong>
                                        </td>

                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>
                <h5 style="text-align: right; font-size: 0.8em; padding: 5px;">Data do Relatório: <?php echo date('d/m/Y'); ?>
                </h5>
            </div>
        </div>
    </div>
</body>

</html>