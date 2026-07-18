<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/table-custom.css" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>
<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>

<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-file-invoice"></i>
        </span>
        <h5>Notas Fiscais de Serviço (NFS-e)</h5>
    </div>
    <div class="span12" style="margin-left: 0; margin-top: 10px;">
        <form method="get" action="<?php echo base_url(); ?>index.php/nfse/listar">
            <div class="span4">
                <input type="text" name="pesquisa" id="pesquisa" placeholder="Nome do cliente, CPF/CNPJ, Nº OS ou Nº Nota" class="span12" value="<?= $this->input->get('pesquisa') ? htmlspecialchars($this->input->get('pesquisa')) : ''; ?>">
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
                <a href="<?php echo base_url(); ?>index.php/nfse/listar" class="button btn btn-mini btn-success" style="min-width: 110px; margin-top: 5px;">
                    <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Limpar</span>
                </a>
            </div>
        </form>
    </div>

    <div class="widget-box">
        <h5 style="padding: 3px 0"></h5>
        <div class="widget-content nopadding tab-content">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>OS #</th>
                        <th>Cliente</th>
                        <th>Status Asaas</th>
                        <th>Nº NFS-e</th>
                        <th>ID Asaas Invoice</th>
                        <th>Valor Total</th>
                        <th>PDF</th>
                        <th>XML</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!$results || empty($results)) {
                        echo '<tr>
                                <td colspan="9">Nenhuma NFS-e encontrada</td>
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
                            echo '<td><a href="' . base_url() . 'index.php/os/editar/' . $r->idOs . '#tabNotas">#' . $r->idOs . '</a></td>';
                            
                            if (!empty($r->clientes_id)) {
                                echo '<td><a href="' . base_url() . 'index.php/clientes/visualizar/' . $r->clientes_id . '">' . htmlspecialchars($r->nomeCliente ?? '-', ENT_QUOTES, 'UTF-8') . '</a><br><small style="color: #666;">' . htmlspecialchars($r->documento ?? '', ENT_QUOTES, 'UTF-8') . '</small></td>';
                            } else {
                                echo '<td>' . htmlspecialchars($r->nomeCliente ?? '-', ENT_QUOTES, 'UTF-8') . '</td>';
                            }

                            echo '<td><span class="label label-' . $badgeColor . '">' . $statusTxt . '</span>';
                            if (!empty($r->asaas_invoice_error)) {
                                echo '<div style="color: red; font-size: 11px; margin-top: 4px;"><strong>Erro:</strong> ' . htmlspecialchars($r->asaas_invoice_error) . '</div>';
                            }
                            echo '</td>';

                            echo '<td>' . (!empty($r->asaas_invoice_number) ? htmlspecialchars($r->asaas_invoice_number) : '-') . '</td>';
                            echo '<td><small>' . (!empty($r->asaas_invoice_id) ? htmlspecialchars($r->asaas_invoice_id) : '-') . '</small></td>';
                            $valorHerdado = ($r->valorTotal > 0) ? $r->valorTotal : (($r->totalProdutos ?? 0) + ($r->totalServicos ?? 0) - ($r->desconto ?? 0));
                            if ($valorHerdado < 0) {
                                $valorHerdado = 0;
                            }
                            echo '<td>R$ ' . number_format($valorHerdado, 2, ',', '.') . '</td>';

                            echo '<td style="text-align: center;">';
                            if (!empty($r->asaas_invoice_pdf)) {
                                echo '<a href="' . $r->asaas_invoice_pdf . '" target="_blank" class="btn btn-mini btn-success" title="Baixar PDF"><i class="bx bx-download"></i> PDF</a>';
                            } else {
                                echo '-';
                            }
                            echo '</td>';

                            echo '<td style="text-align: center;">';
                            if (!empty($r->asaas_invoice_xml)) {
                                echo '<a href="' . $r->asaas_invoice_xml . '" target="_blank" class="btn btn-mini btn-info" title="Baixar XML"><i class="bx bx-code"></i> XML</a>';
                            } else {
                                echo '-';
                            }
                            echo '</td>';

                            echo '<td>';
                            echo '<a href="' . base_url() . 'index.php/os/editar/' . $r->idOs . '#tabNotas" class="btn-nwe2" style="margin-right: 1%" title="Ver Ordem de Serviço / Nota"><i class="bx bx-show"></i></a>';
                            echo '<a href="javascript:void(0);" class="btn-nwe btn-consultar-nfse-list" data-id="' . $r->idOs . '" style="margin-right: 1%" title="Atualizar Status na Prefeitura"><i class="bx bx-refresh"></i></a>';
                            if ($r->asaas_invoice_status !== 'CANCELED' && $r->asaas_invoice_status !== 'CANCELLED') {
                                echo '<a href="javascript:void(0);" class="btn-nwe4 btn-cancelar-nfse-list" data-id="' . $r->idOs . '" title="Cancelar NFS-e na Prefeitura"><i class="bx bx-trash"></i></a>';
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
    <?php echo $this->pagination->create_links(); ?>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $(".datepicker").datepicker({
        dateFormat: 'dd/mm/yy'
    });

    $(document).on('click', '.btn-consultar-nfse-list', function(e) {
        e.preventDefault();
        var idOs = $(this).data('id');
        Swal.fire({
            title: 'Consultando status...',
            allowOutsideClick: false,
            didOpen: function() {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '<?php echo base_url(); ?>index.php/os/consultar_nfse/' + idOs,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.result) {
                    Swal.fire('Status Atualizado!', 'O status da NFS-e é: ' + res.status, 'success').then(function() {
                        location.reload();
                    });
                } else {
                    Swal.fire('Aviso', res.message, 'info');
                }
            },
            error: function() {
                Swal.fire('Erro!', 'Falha na consulta ao Asaas.', 'error');
            }
        });
    });

    $(document).on('click', '.btn-cancelar-nfse-list', function(e) {
        e.preventDefault();
        var idOs = $(this).data('id');
        Swal.fire({
            title: 'Cancelar NFS-e na Prefeitura?',
            text: 'Isso enviará um pedido de cancelamento da Nota Fiscal de Serviço para a prefeitura.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sim, cancelar!'
        }).then(function(result) {
            if (result.value || result.isConfirmed) {
                Swal.fire({
                    title: 'Cancelando...',
                    allowOutsideClick: false,
                    didOpen: function() {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '<?php echo base_url(); ?>index.php/os/cancelar_nfse/' + idOs,
                    type: 'POST',
                    dataType: 'json',
                    success: function(res) {
                        if (res.result) {
                            Swal.fire('Sucesso!', res.message, 'success').then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Erro!', res.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Erro!', 'Falha de comunicação ao cancelar.', 'error');
                    }
                });
            }
        });
    });
});
</script>
