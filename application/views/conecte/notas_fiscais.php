<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/table-custom.css" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>

<style>
  select {
    width: 70px;
  }
</style>
<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-file-invoice"></i>
        </span>
        <h5>Notas Fiscais de Serviço (NFS-e)</h5>
    </div>
    <div class="span12" style="margin-left: 0; margin-top: 10px;">
        <form method="get" action="<?php echo base_url(); ?>index.php/mine/notasfiscais">
            <div class="span4">
                <input type="text" name="pesquisa" id="pesquisa" placeholder="Nº da OS ou Nº da Nota" class="span12" value="<?= $this->input->get('pesquisa') ? htmlspecialchars($this->input->get('pesquisa')) : ''; ?>">
            </div>

            <div class="span3">
                <select name="status" id="status" class="span12">
                    <option value="">Todos os status</option>
                    <option value="SCHEDULED" <?= $this->input->get('status') == "SCHEDULED" ? "selected" : ""; ?>>Agendada (SCHEDULED)</option>
                    <option value="AUTHORIZED" <?= $this->input->get('status') == "AUTHORIZED" ? "selected" : ""; ?>>Autorizada (AUTHORIZED)</option>
                    <option value="PROCESSING" <?= $this->input->get('status') == "PROCESSING" ? "selected" : ""; ?>>Processando (PROCESSING)</option>
                    <option value="CANCELED" <?= ($this->input->get('status') == "CANCELED" || $this->input->get('status') == "CANCELLED") ? "selected" : ""; ?>>Cancelada</option>
                    <option value="ERROR" <?= $this->input->get('status') == "ERROR" ? "selected" : ""; ?>>Erro na Emissão</option>
                </select>
            </div>

            <div class="span3">
                <div class="span12" style="margin-left: 0;">
                    <input type="text" name="data_de" autocomplete="off" id="data_de" placeholder="Data OS de" title="Data Inicial" class="span6 datepicker" value="<?= $this->input->get('data_de') ? htmlspecialchars($this->input->get('data_de')) : '' ?>">
                    <input type="text" name="data_ate" autocomplete="off" id="data_ate" placeholder="Data OS até" title="Data Final" class="span6 datepicker" value="<?= $this->input->get('data_ate') ? htmlspecialchars($this->input->get('data_ate')) : '' ?>">
                </div>
            </div>

            <div class="span2" style="margin-top: 2px;">
                <button class="button btn btn-mini btn-warning" style="min-width: 110px">
                    <span class="button__icon"><i class='bx bx-search-alt'></i></span>
                    <span class="button__text2">Pesquisar</span>
                </button>
                <a href="<?php echo base_url(); ?>index.php/mine/notasfiscais" class="button btn btn-mini btn-success" style="min-width: 110px; margin-top: 5px;">
                    <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Limpar</span>
                </a>
            </div>
        </form>
    </div>

    <div class="widget-box">
        <h5 style="padding: 3px 0"></h5>
        <div class="widget-content nopadding tab-content">
            <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>OS #</th>
                            <th>Status Asaas</th>
                            <th>Nº NFS-e</th>
                            <th>Valor Total</th>
                            <th>PDF</th>
                            <th>XML</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!$results || empty($results)) {
                            echo '<tr>
                                    <td colspan="6">Nenhuma NFS-e encontrada</td>
                                </tr>';
                        } else {
                            foreach ($results as $r) {
                                $statusNota = $r->asaas_invoice_status;
                                $badgeColor = 'info';
                                $statusTxt = $statusNota;
                                if ($statusNota === 'SCHEDULED') { $badgeColor = 'warning'; $statusTxt = 'Agendada (SCHEDULED)'; }
                                elseif ($statusNota === 'AUTHORIZED') { $badgeColor = 'success'; $statusTxt = 'Autorizada (AUTHORIZED)'; }
                                elseif ($statusNota === 'PROCESSING') { $badgeColor = 'info'; $statusTxt = 'Processando (PROCESSING)'; }
                                elseif ($statusNota === 'CANCELED' || $statusNota === 'CANCELLED') { $badgeColor = 'important'; $statusTxt = 'Cancelada'; }
                                elseif ($statusNota === 'ERROR') { $badgeColor = 'important'; $statusTxt = 'Erro na Emissão'; }

                                echo '<tr>';
                                echo '<td><a href="' . base_url() . 'index.php/mine/detalhesOs/' . $r->idOs . '">  Ordem de Serviço: #' . $r->idOs . '</a></td>';
                                
                                echo '<td><span class="label label-' . $badgeColor . '">' . $statusTxt . '</span>';
                                if (!empty($r->asaas_invoice_error)) {
                                    echo '<div style="color: red; font-size: 11px; margin-top: 4px;"><strong>Erro:</strong> ' . htmlspecialchars($r->asaas_invoice_error) . '</div>';
                                }
                                echo '</td>';

                                echo '<td>' . (!empty($r->asaas_invoice_number) ? htmlspecialchars($r->asaas_invoice_number) : '-') . '</td>';
                                
                                $valorHerdado = ($r->valorTotal > 0) ? $r->valorTotal : (($r->totalProdutos ?? 0) + ($r->totalServicos ?? 0) - ($r->desconto ?? 0));
                                echo '<td>R$ ' . number_format($valorHerdado, 2, ',', '.') . '</td>';

                                // PDF e XML da Nota
                                echo '<td>';
                                if (!empty($r->asaas_invoice_pdf)) {
                                    echo '<a href="' . $r->asaas_invoice_pdf . '" target="_blank" class="btn btn-mini btn-info tip-top" title="Baixar PDF"><i class="bx bxs-file-pdf"></i> PDF</a>';
                                } else {
                                    echo '-';
                                }
                                echo '</td>';

                                echo '<td>';
                                if (!empty($r->asaas_invoice_xml)) {
                                    echo '<a href="' . $r->asaas_invoice_xml . '" target="_blank" class="btn btn-mini btn-warning tip-top" title="Baixar XML"><i class="bx bx-code-alt"></i> XML</a>';
                                } else {
                                    echo '-';
                                }
                                echo '</td>';

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
<?php echo $this->pagination->create_links(); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $(".datepicker").datepicker({
            dateFormat: 'dd/mm/yy'
        });
    });
</script>
