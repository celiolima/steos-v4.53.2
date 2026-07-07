<div class="span12" style="margin-left: 0">
    <div class="widget-box">
        <div class="widget-title" style="margin: -20px 0 0">
            <span class="icon">
                <i class="fas fa-file-signature"></i>
            </span>
            <h5>Meus Contratos</h5>
        </div>

        <div class="widget-content nopadding tab-content">
            <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table id="tabela" class="table table-bordered ">
                    <thead>
                        <tr>
                            <th>Nº</th>
                            <th>Nome do Contrato</th>
                            <th>Tipo</th>
                            <th>Data Inicial</th>
                            <th>Data Final</th>
                            <th>Valor Total</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!$contratos) {
                            echo '<tr>
                                    <td colspan="8">Nenhum Contrato Encontrado</td>
                                  </tr>';
                        } else {
                            foreach ($contratos as $r) {
                                $dataInicial = date(('d/m/Y'), strtotime($r->dataInicial));
                                $dataFinal = $r->dataFinal ? date(('d/m/Y'), strtotime($r->dataFinal)) : 'Indeterminado';
                                $valorExibir = (float)($r->valorTotal ?: ($r->valorContrato ?: 0));

                                switch ($r->status) {
                                    case 'Ativo':
                                    case '1':
                                        $cor = '#54c795';
                                        $statusStr = 'Ativo';
                                        break;
                                    case 'Inativo':
                                    case '0':
                                        $cor = '#225566';
                                        $statusStr = 'Inativo';
                                        break;
                                    case 'Negociação':
                                        $cor = '#ffd700';
                                        $statusStr = 'Negociação';
                                        break;
                                    default:
                                        $cor = '#E0E4CC';
                                        $statusStr = $r->status;
                                        break;
                                }

                                echo '<tr>';
                                echo '<td>' . $r->idContratos . '</td>';
                                echo '<td><strong>' . $r->nomeContratos . '</strong></td>';
                                echo '<td>' . $r->tipoContrato . '</td>';
                                echo '<td>' . $dataInicial . '</td>';
                                echo '<td>' . $dataFinal . '</td>';
                                echo '<td>R$ ' . number_format($valorExibir, 2, ',', '.') . '</td>';
                                echo '<td><span class="badge" style="background-color: ' . $cor . '; border-color: ' . $cor . '">' . $statusStr . '</span></td>';
                                echo '<td>
                                        <a href="' . base_url() . 'index.php/mine/detalhesContrato/' . $r->idContratos . '" class="btn-nwe4" title="Visualizar Detalhes do Contrato"><i class="bx bx-show-alt"></i></a>
                                      </td>';
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
