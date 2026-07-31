<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon"><i class="fas fa-list-alt"></i></span>
        <h5>Monitor de Integração - Webhooks Asaas</h5>
    </div>

    <div class="widget-box">
        <div class="widget-content nopadding tab-content">
            <table class="table table-bordered table-striped data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Data Recebimento</th>
                        <th>Evento</th>
                        <th>ID Pagamento (Asaas)</th>
                        <th>Status</th>
                        <th>Mensagem / Observação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$results) { ?>
                        <tr>
                            <td colspan="6">Nenhum evento registrado.</td>
                        </tr>
                    <?php } else {
                        foreach ($results as $r) {
                            $statusLabel = 'badge-secondary';
                            if ($r->status === 'SUCESSO') $statusLabel = 'badge-success';
                            elseif ($r->status === 'ERRO') $statusLabel = 'badge-important';
                            elseif ($r->status === 'IGNORADO') $statusLabel = 'badge-warning';
                    ?>
                        <tr>
                            <td><?= $r->id ?></td>
                            <td><?= date('d/m/Y H:i:s', strtotime($r->data_recebimento)) ?></td>
                            <td><?= htmlspecialchars($r->event) ?></td>
                            <td><?= htmlspecialchars($r->asaas_payment_id) ?></td>
                            <td><span class="badge <?= $statusLabel ?>"><?= htmlspecialchars($r->status) ?></span></td>
                            <td><?= htmlspecialchars($r->mensagem_erro ?? '') ?></td>
                        </tr>
                    <?php } } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('.data-table').dataTable({
            "bJQueryUI": true,
            "sPaginationType": "full_numbers",
            "sDom": '<""l>t<"F"p>',
            "order": [[0, "desc"]]
        });
    });
</script>
