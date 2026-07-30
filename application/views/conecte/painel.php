<link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>

<div class="quick-actions_homepage">
    <ul class="cardBox">
        <li class="card">
            <a href="<?php echo base_url() ?>index.php/mine/os">
                <div class="lord-icon04">
                    <i class='bx bx-file iconBx04'></i>
                </div>
            </a>
            <a href="<?php echo base_url() ?>index.php/mine/os">
                <div style="font-size: 1.2em" class="numbers">Ordens de Serviço</div>
            </a>
        </li>

        <li class="card">
            <a href="<?php echo base_url() ?>index.php/mine/compras">
                <div class="lord-icon05">
                    <i class='bx bx-cart-alt iconBx05'></i>
                </div>
            </a>
            <a href="<?php echo base_url() ?>index.php/mine/compras">
                <div style="font-size: 1.2em" class="numbers">Compras&nbsp;&nbsp;&nbsp;&nbsp;</div>
            </a>
        <li class="card">
            <a href="<?php echo base_url() ?>index.php/mine/cobrancas">
                <div class="lord-icon05">
                    <i class='bx bx-credit-card-front iconBx05'></i>
                </div>
            </a>
            <a href="<?php echo base_url() ?>index.php/mine/cobrancas">
                <div style="font-size: 1.2em" class="numbers">Cobranças&nbsp;&nbsp;&nbsp;&nbsp;</div>
            </a>
        </li>
        <li class="card">
            <a href="<?php echo base_url() ?>index.php/mine/conta">
                <div class="lord-icon07">
                    <i class='bx bx-user-circle iconBx07'></i></span>
                </div>
            </a>
            <a href="<?php echo base_url() ?>index.php/mine/conta">
                <div style="font-size: 1.2em" class="numbers">Minha Conta</div>
            </a>
        </li>
    </ul>
</div>

<div class="span12" style="margin-left: 0">
    <?php if (isset($temContrato) && $temContrato): ?>
    <div class="row-fluid" style="margin-top: 0">
        <div class="span6">
            <div class="widget-box">
                <div class="widget-title" style="margin: -20px 0 0">
                    <span class="icon"><i class="fas fa-chart-pie"></i></span>
                    <h5>Ordens de Serviço por Prioridade</h5>
                </div>
                <div class="widget-content">
                    <canvas id="chartPrioridade" style="width:100%; height: 300px;"></canvas>
                </div>
            </div>
        </div>
        <div class="span6">
            <div class="widget-box">
                <div class="widget-title" style="margin: -20px 0 0">
                    <span class="icon"><i class="fas fa-chart-area"></i></span>
                    <h5>Ordens de Serviço por Classificação</h5>
                </div>
                <div class="widget-content">
                    <canvas id="chartClassificacao" style="width:100%; height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="widget-box">
        <div class="widget-title" style="margin: -20px 0 0">
            <span class="icon"><i class="fas fa-signal"></i></span>
            <h5>Últimas Ordens de Serviço</h5>
        </div>
        <div class="widget-content">
            <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table id="tabela" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nº</th>
                        <th>Responsável</th>
                        <th>Data Inicial</th>
                        <th>Data Final</th>
                        <th>Venc. da Garantia</th>
                        <th>Observações</th>
                        <th>Valor Total</th>
                        <th>Valor com Desconto</th>
                        <th>Status</th>
                        <th style="text-align:right">Visualizar / Imprimir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($os != null) {
                        foreach ($os as $o) {
                            $vencGarantia = '';

                            if ($o->garantia && is_numeric($o->garantia)) {
                                $vencGarantia = dateInterval($o->dataFinal, $o->garantia);
                            }
                            $corGarantia = '';
                            if (!empty($vencGarantia)) {
                                $dataGarantia = explode('/', $vencGarantia);
                                $dataGarantiaFormatada = $dataGarantia[2] . '-' . $dataGarantia[1] . '-' . $dataGarantia[0];
                                if (strtotime($dataGarantiaFormatada) >= strtotime(date('d-m-Y'))) {
                                    $corGarantia = '#4d9c79';
                                } else {
                                    $corGarantia = '#f24c6f';
                                }
                            } elseif ($o->garantia == "0") {
                                $vencGarantia = 'Sem Garantia';
                                $corGarantia = '';
                            } else {
                                $vencGarantia = '';
                                $corGarantia = '';
                            }

                            switch ($o->status) {
                                case 'A Sair | Aguard Conclusão':
                                case 'Aberto':
                                    $cor = '#00cd00';
                                    break;
                                case 'Em Andamento':
                                    $cor = '#436eee';
                                    break;
                                case 'Orçamento':
                                    $cor = '#CDB380';
                                    break;
                                case 'Negociação':
                                    $cor = '#AEB404';
                                    break;
                                case 'Cancelado':
                                    $cor = '#CD0000';
                                    break;
                                case 'Finalizado':
                                    $cor = '#256';
                                    break;
                                case 'Faturado':
                                    $cor = '#B266FF';
                                    break;
                                case 'Aguardando Peças':
                                    $cor = '#FF7F00';
                                    break;
                                case 'Aprovado':
                                    $cor = '#808080';
                                    break;
                                default:
                                    $cor = '#E0E4CC';
                                    break;
                            }

                            $valorTotalOS = $o->valorTotal != 0 ? $o->valorTotal : ($o->totalProdutos + $o->totalServicos);
                            $valorDescontoOS = $o->valor_desconto != 0 ? $o->valor_desconto : $valorTotalOS;

                            echo '<tr>';
                            echo '<td>' . $o->idOs . '</td>';
                            echo '<td>' . $o->nome . '</td>';
                            echo '<td>' . date('d/m/Y', strtotime($o->dataInicial)) . '</td>';
                            echo '<td>' . date('d/m/Y', strtotime($o->dataFinal)) . '</td>';
                            echo '<td><span class="badge" style="background-color: ' . $corGarantia . '; border-color: ' . $corGarantia . '">' . $vencGarantia . '</span> </td>';
                            echo '<td><div style="max-height: 80px; overflow-y: auto; max-width: 350px;">' . (!empty($o->observacoes) ? strip_tags(str_replace(['&nbsp;', '&amp;nbsp;'], ' ', html_entity_decode($o->observacoes))) : '') . '</div></td>';
                            echo '<td>R$ ' . number_format($valorTotalOS, 2, ',', '.') . '</td>';
                            echo '<td>R$ ' . number_format($valorDescontoOS, 2, ',', '.') . '</td>';
                            echo '<td><span class="badge" style="background-color: ' . $cor . '; border-color: ' . $cor . '">' . $o->status . '</span> </td>';
                            echo '<td style="text-align:right">';
                            echo '<a href="' . base_url() . 'index.php/mine/visualizarOs/' . $o->idOs . '" class="btn"> <i class="fas fa-eye" ></i></a> ';
                            echo '<a href="' . base_url('index.php/mine/imprimirOs/' . $o->idOs) . '" class="btn" target="_blank"> <i class="fas fa-print"></i></a>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="10">Nenhum ordem de serviço encontrada.</td></tr>';
                    }

            ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="widget-box">
        <div class="widget-title" style="margin: -20px 0 0">
            <span class="icon"><i class="fas fa-signal"></i></span>
            <h5>Últimas Compras</h5>
        </div>
        <div class="widget-content">
            <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table id="tabela" class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Responsável</th>
                        <th>Data da Venda</th>
                        <th>Faturado</th>
                        <th>Venc. da Garantia</th>
                        <th>Status</th>
                        <th style="text-align:right">Visualizar / Imprimir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
            if ($compras != null) {
                foreach ($compras as $c) {
                    $vencGarantia = '';

                    if ($c->garantia && is_numeric($c->garantia)) {
                        $vencGarantia = dateInterval($c->dataVenda, $c->garantia);
                    }
                    $corGarantia = '';
                    if (!empty($vencGarantia)) {
                        $dataGarantia = explode('/', $vencGarantia);
                        $dataGarantiaFormatada = $dataGarantia[2] . '-' . $dataGarantia[1] . '-' . $dataGarantia[0];
                        if (strtotime($dataGarantiaFormatada) >= strtotime(date('d-m-Y'))) {
                            $corGarantia = '#4d9c79';
                        } else {
                            $corGarantia = '#f24c6f';
                        }
                    } elseif ($c->garantia == "0") {
                        $vencGarantia = 'Sem Garantia';
                        $corGarantia = '';
                    } else {
                        $vencGarantia = '';
                        $corGarantia = '';
                    }
                    if ($c->faturado == 1) {
                        $faturado = 'Sim';
                    } else {
                        $faturado = 'Não';
                    }
                    
                    switch ($c->status) {
                        case 'A Sair | Aguard Conclusão':
                        case 'Aberto':
                            $cor = '#00cd00';
                            break;
                        case 'Em Andamento':
                            $cor = '#436eee';
                            break;
                        case 'Orçamento':
                            $cor = '#CDB380';
                            break;
                        case 'Negociação':
                            $cor = '#AEB404';
                            break;
                        case 'Cancelado':
                            $cor = '#CD0000';
                            break;
                        case 'Finalizado':
                            $cor = '#256';
                            break;
                        case 'Faturado':
                            $cor = '#B266FF';
                            break;
                        case 'Aguardando Peças':
                            $cor = '#FF7F00';
                            break;
                        case 'Aprovado':
                            $cor = '#808080';
                            break;
                        default:
                            $cor = '#E0E4CC';
                            break;
                    }
                    echo '<tr>';
                    echo '<td>' . $c->idVendas . '</td>';
                    echo '<td>' . $c->nome . '</td>';
                    echo '<td>' . date('d/m/Y', strtotime($c->dataVenda)) . '</td>';
                    echo '<td>' . $faturado . '</td>';
                    echo '<td><span class="badge" style="background-color: ' . $corGarantia . '; border-color: ' . $corGarantia . '">' . $vencGarantia . '</span> </td>';
                    echo '<td><span class="badge" style="background-color: ' . $cor . '; border-color: ' . $cor . '">' . $c->status . '</span> </td>';
                    echo '<td style="text-align:right">';
                    echo '<a href="' . base_url() . 'index.php/mine/visualizarCompra/' . $c->idVendas . '" class="btn"> <i class="fas fa-eye" ></i> </a> ';
                    echo '<a href="' . base_url() . 'index.php/mine/imprimirCompra/' . $c->idVendas . '" class="btn"> <i class="fas fa-print" ></i> </a>';
                    echo '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="5">Nenhum venda encontrada.</td></tr>';
            }

            ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

<?php if (isset($temContrato) && $temContrato): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const prioridadeData = <?php echo json_encode($graficoPrioridade ?? []); ?>;
        const classificacaoData = <?php echo json_encode($graficoClassificacao ?? []); ?>;
        
        // Setup Prioridade Pie Chart
        const prioridadeLabels = prioridadeData.map(item => item.prioridade || 'Sem');
        const prioridadeValues = prioridadeData.map(item => parseFloat(item.total));
        const prioridadeColorsMap = {
            'sem': '#d9d9d9', // Cinza claro
            'URGENTE': '#f55776',
            'ALTA': '#f89406',
            'MÉDIA': '#2f96b4',
            'BAIXA': '#51a351'
        };
        const prioridadeColors = prioridadeLabels.map(label => prioridadeColorsMap[label] || '#cccccc');
        
        const ctxPrioridade = document.getElementById('chartPrioridade').getContext('2d');
        new Chart(ctxPrioridade, {
            type: 'pie',
            data: {
                labels: prioridadeLabels,
                datasets: [{
                    data: prioridadeValues,
                    backgroundColor: prioridadeColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Setup Classificacao Polar Area Chart
        // Tratamento para caracteres especiais que podem vir corrompidos do banco
        const fixEncoding = (str) => {
            if (!str) return 'Sem';
            const map = {
                'CORREÃ‡Ã£O': 'CORREÇÃO', 'CORREÃ§Ã£O': 'CORREÇÃO',
                'AMPLIAÃ‡Ã£O': 'AMPLIAÇÃO', 'AMPLIAÃ§Ã£O': 'AMPLIAÇÃO',
                'SUGESTÃ£O': 'SUGESTÃO',
                'PREVENÃ‡Ã£O': 'PREVENÇÃO', 'PREVENÃ§Ã£O': 'PREVENÇÃO'
            };
            return map[str.toUpperCase()] || str;
        };

        const classificacaoLabels = classificacaoData.map(item => fixEncoding(item.classificacao));
        const classificacaoValues = classificacaoData.map(item => parseFloat(item.total));
        const classificacaoColorsMap = {
            'CORREÇÃO': '#f55776', // vermelho
            'AMPLIAÇÃO': '#2f96b4', // azul
            'SUGESTÃO': '#f89406', // laranja
            'PREVENÇÃO': '#51a351' // verde
        };
        
        const classificacaoColors = classificacaoLabels.map(label => {
            const hex = classificacaoColorsMap[label.toUpperCase()] || '#999999';
            return hex + 'cc'; // adiciona transparência (cc)
        });

        const ctxClassificacao = document.getElementById('chartClassificacao').getContext('2d');
        new Chart(ctxClassificacao, {
            type: 'polarArea',
            data: {
                labels: classificacaoLabels,
                datasets: [{
                    data: classificacaoValues,
                    backgroundColor: classificacaoColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    });
</script>
<?php endif; ?>
