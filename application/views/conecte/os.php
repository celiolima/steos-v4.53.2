<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>

<?php
// alterar para permissão de o cliente adicionar ou não a ordem de serviço
if (!$this->session->userdata('cadastra_os')) { ?>
    <div class="span12" style="margin-left: 0">
        <div class="span3">
            <a href="<?php echo base_url(); ?>index.php/mine/adicionarOs" class="button btn btn-success" style="max-width: 150px">
              <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar</span></a>
        </div>
    </div>
<?php
}
?>

<div class="span12" style="margin-left: 0; margin-bottom: 10px;">
    <form method="get" action="<?php echo base_url(); ?>index.php/mine/os">
        <div class="span3" style="margin-left: 0">
            <input type="text" name="os" id="os" placeholder="Pesquisar por Nº da OS" class="span12" value="<?= $this->input->get('os') ? $this->input->get('os') : ''; ?>">
        </div>
        <div class="span3">
            <select name="tecnico" id="tecnico" class="span12">
                <option value="">Todos os técnicos</option>
                <?php
                if (!empty($tecnicos)) {
                    foreach ($tecnicos as $t) {
                        $selected = $this->input->get('tecnico') == $t->nome ? 'selected' : '';
                        echo '<option value="' . $t->nome . '" ' . $selected . '>' . $t->nome . '</option>';
                    }
                }
                ?>
            </select>
        </div>
        <div class="span4">
            <input type="text" name="data" autocomplete="off" id="data" placeholder="Data Inicial" title="Data Inicial" class="span6 datepicker" value="<?= $this->input->get('data') ? $this->input->get('data') : '' ?>">
            <input type="text" name="data2" autocomplete="off" id="data2" placeholder="Data Final" title="Data Final" class="span6 datepicker" value="<?= $this->input->get('data2') ? $this->input->get('data2') : '' ?>">
        </div>
        <div class="span2">
            <button class="button btn btn-mini btn-warning" style="margin-top: 0">
                <span class="button__icon"><i class='bx bx-search-alt'></i></span>
                <span class="button__text2">Pesquisar</span>
            </button>
            <a href="<?php echo base_url(); ?>index.php/mine/os" class="button btn btn-mini btn-success" style="max-width: 140px; margin-top: 0">
                <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Limpar</span></a>
        </div>
    </form>
</div>

<?php
if (!$results) {
    ?>
    <div class="span12" style="margin-left: 0">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-diagnoses"></i>
                </span>
                <h5>Ordens de Serviço</h5>

            </div>

            <div class="widget-content nopadding tab-content">
                <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table id="tabela" class="table table-bordered ">
                    <thead>
                        <tr>
                            <th>Nº</th>
                            <th>Responsável</th>
                            <th>Data Inicial</th>
                            <th>Data Final</th>
                            <th>Venc. Garantia</th>
                            <th>Observações</th>
                            <th>Valor Total</th>
                            <th>Valor com Desconto</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td colspan="10">Nenhuma ordem de serviço encontrada</td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>
        </div>

    </div>

<?php
} else { ?>

    <div class="span12" style="margin-left: 0">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="fas fa-diagnoses"></i>
                </span>
                <h5>Ordens de Serviço</h5>

            </div>

            <div class="widget-content nopadding tab-content">
                <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="table table-bordered ">
                    <thead>
                        <tr>
                            <th>Nº</th>
                            <th>Responsável</th>
                            <th>Data Inicial</th>
                            <th>Data Final</th>
                            <th>Venc. Garantia</th>
                            <th>Observações</th>
                            <th>Valor Total</th>
                            <th>Valor com Desconto</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $this->load->model('os_model');
    foreach ($results as $r) {
        $dataInicial = date(('d/m/Y'), strtotime($r->dataInicial));
        $dataFinal = date(('d/m/Y'), strtotime($r->dataFinal));
        switch ($r->status) {
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
                            
        $vencGarantia = '';
        if ($r->garantia && is_numeric($r->garantia)) {
            $vencGarantia = dateInterval($r->dataFinal, $r->garantia);
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
        } elseif ($r->garantia == "0") {
            $vencGarantia = 'Sem Garantia';
            $corGarantia = '';
        } else {
            $vencGarantia = '';
            $corGarantia = '';
        }

        $valorTotalOS = $r->valorTotal != 0 ? $r->valorTotal : ($r->totalProdutos + $r->totalServicos);
        $valorDescontoOS = $r->valor_desconto != 0 ? $r->valor_desconto : $valorTotalOS;

        echo '<tr>';
        echo '<td>' . $r->idOs . '</td>';
        echo '<td>' . $r->nome . '</td>';
        echo '<td>' . $dataInicial . '</td>';
        echo '<td>' . $dataFinal . '</td>';
        echo '<td><span class="badge" style="background-color: ' . $corGarantia . '; border-color: ' . $corGarantia . '">' . $vencGarantia . '</span> </td>';
        echo '<td><div style="max-height: 80px; overflow-y: auto; max-width: 350px;">' . (!empty($r->observacoes) ? strip_tags(str_replace(['&nbsp;', '&amp;nbsp;'], ' ', html_entity_decode($r->observacoes))) : '') . '</div></td>';
        echo '<td>R$ ' . number_format($valorTotalOS, 2, ',', '.') . '</td>';
        echo '<td>R$ ' . number_format($valorDescontoOS, 2, ',', '.') . '</td>';
        echo '<td><span class="badge" style="background-color: ' . $cor . '; border-color: ' . $cor . '">' . $r->status . '</span> </td>';

        echo '<td><a href="' . base_url() . 'index.php/mine/visualizarOs/' . $r->idOs . '" class="btn-nwe" title="Visualizar e Imprimir"><i class="bx bx-show-alt"></i></a>
                                  <a href="' . base_url() . 'index.php/mine/imprimirOs/' . $r->idOs . '" class="btn-nwe3" title="Imprimir" target="_blank"><i class="bx bx-printer"></i></a>
                                  <a href="' . base_url() . 'index.php/mine/detalhesOs/' . $r->idOs . '" class="btn-nwe4" title="Ver mais detalhes"><i class="bx bx-detail"></i></a>
                                  </td>';
        echo '</tr>';
    } ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
<?php echo $this->pagination->create_links();
} ?>

<script type="text/javascript">
    $(document).ready(function() {
        $(".datepicker").datepicker({
            dateFormat: 'dd/mm/yy'
        });
    });
</script>
