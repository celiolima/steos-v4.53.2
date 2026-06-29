<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Checklist Equipamento</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: Arial, sans-serif; }
        body { background-color: #f0f0f0; padding: 20px; display: flex; flex-direction: column; align-items: center; }
        .document-container { width: 100%; max-width: 1000px; background-color: #fff; border: 2px solid #000; position: relative; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .watermark { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 120px; color: rgba(0, 0, 0, 0.15); font-weight: bold; z-index: 0; pointer-events: none; white-space: nowrap; }
        .header { display: flex; border-bottom: 1px solid #000; position: relative; z-index: 1; }
        .logo-area { width: 250px; padding: 10px; font-weight: bold; font-size: 14px; text-align: center; border-right: 1px solid #000; display: flex; flex-direction: column; justify-content: center; align-items: center; }
        .title-area { flex-grow: 1; display: flex; align-items: center; justify-content: center; font-size: 36px; font-style: italic; font-weight: normal; letter-spacing: 2px; text-align: center; padding: 10px; }
        .info-grid { display: grid; grid-template-columns: 120px 1fr 120px 1fr; border-bottom: 1px solid #000; position: relative; z-index: 1; }
        .info-grid > div { padding: 5px 10px; border-right: 1px solid #000; font-size: 14px; }
        .info-grid > div:last-child { border-right: none; }
        .bg-orange { background-color: #f4b183; }
        .client-row { display: grid; grid-template-columns: 180px 1fr; border-bottom: 1px solid #000; position: relative; z-index: 1; }
        .client-row > div { padding: 5px 10px; font-size: 14px; }
        .client-row > div:first-child { border-right: 1px solid #000; }
        .section-title { text-align: center; padding: 5px; border-bottom: 1px solid #000; font-size: 16px; position: relative; z-index: 1; }
        .table-responsive { width: 100%; overflow-x: auto; position: relative; z-index: 1; }
        table { width: 100%; min-width: 800px; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 2px; font-size: 12px; text-align: center; }
        th { font-weight: normal; }
        .col-local { width: 150px; text-align: left; vertical-align: bottom; padding-left: 5px; }
        .col-obs { width: auto; text-align: left; vertical-align: bottom; padding-left: 5px; }
        .col-check { width: 25px; }
        .col-ordem { width: 40px; }
        .vertical-text { writing-mode: vertical-rl; transform: rotate(180deg); height: 140px; text-align: left; padding: 5px 0; margin: 0 auto; }
        td { height: 20px; }
        td.text-left { text-align: left; padding-left: 5px; }
        .status-v { color: #00b050; font-weight: bold; }
        .status-x { color: #ff0000; font-weight: bold; }
        .status-o { color: #c65911; font-weight: bold; }
        .footer-section { padding: 10px 0 10px 0; position: relative; z-index: 1; width: 100%; }
        .obs-box { border: 1px solid #000; min-height: 50px; padding: 6px; font-size: 13px; margin-top: 5px; margin-bottom: 15px; white-space: pre-wrap; width: 100%; }
        .signatures-table { width: 100%; border-collapse: collapse; border: none !important; margin-top: 15px; page-break-inside: avoid; break-inside: avoid; table-layout: fixed; }
        .signatures-table tr, .signatures-table td { border: none !important; vertical-align: bottom; padding: 2px; height: auto; }
        .company-info { font-size: 8.5px; text-align: center; line-height: 1.2; width: 100%; }
        .company-info strong { font-size: 10.5px; }
        .company-info a { color: #0000ff; text-decoration: none; }
        .signature-wrapper { display: flex; flex-direction: column; align-items: center; width: 100%; }
        .pad-container { width: 100%; max-width: 180px; height: 50px; border: 1px dashed #ccc; background-color: #fafafa; position: relative; margin: 0 auto 3px auto; display: flex; justify-content: center; align-items: center; }
        .pad-container img { max-width: 100%; max-height: 100%; object-fit: contain; }
        .sig-line { border-top: 1px solid #000; text-align: center; padding-top: 4px; font-size: 11.5px; width: 100%; max-width: 180px; margin: 0 auto; }
        .action-buttons { margin-top: 20px; margin-bottom: 20px; display: flex; gap: 15px; flex-wrap: wrap; justify-content: center; }
        .btn { color: white; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: background-color 0.3s; display: flex; align-items: center; gap: 8px; }
        .btn-print { background-color: #4CAF50; }
        .btn-print:hover { background-color: #45a049; }
        .btn-whatsapp { background-color: #25D366; }
        .btn-whatsapp:hover { background-color: #128C7E; }
        .btn-voltar { background-color: #6c757d; text-decoration: none;}
        .btn-voltar:hover { background-color: #5a6268; }
        @media (max-width: 768px) {
            body { padding: 10px; }
            .header { flex-direction: column; }
            .logo-area { width: 100%; border-right: none; border-bottom: 1px solid #000; }
            .title-area { font-size: 24px; }
            .info-grid { grid-template-columns: 100px 1fr; }
            .info-grid > div:nth-child(even) { border-right: none; }
            .client-row { grid-template-columns: 1fr; }
            .client-row > div:first-child { border-right: none; border-bottom: 1px solid #000; background-color: #f4b183; }
            .signatures-table { min-width: 0 !important; width: 100% !important; }
            .signatures-table, .signatures-table tbody, .signatures-table tr, .signatures-table td { display: block !important; width: 100% !important; box-sizing: border-box; }
            .signatures-table td { margin-bottom: 25px; padding: 0 !important; text-align: center !important; }
            .company-info, .signature-wrapper { width: 100% !important; max-width: 100% !important; margin: 0 auto !important; display: flex; flex-direction: column; align-items: center; }
            .watermark { font-size: 60px; }
        }
        @page {
            size: auto;
            margin: 0mm;
        }
        @media print {
            body { background-color: #fff; padding: 10mm; }
            .document-container { border: none; box-shadow: none; max-width: 100%; width: 100%; }
            .action-buttons { display: none; }
            .pad-container { border: none; background-color: transparent; }
            .table-responsive { overflow-x: visible; }
            table { min-width: 100%; }
            .signatures-table { display: table !important; width: 100% !important; }
            .signatures-table tbody { display: table-row-group !important; }
            .signatures-table tr { display: table-row !important; }
            .signatures-table td { display: table-cell !important; width: 33.33% !important; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
    </style>
</head>
<body>

<div class="document-container">
    <div class="watermark">Página 1</div>

    <!-- Cabeçalho -->
    <div class="header">
        <div class="logo-area">
            <span style="font-size: 24px; font-style: italic; font-weight: 900; letter-spacing: -1px;">STE</span>
            <span>AUTOMAÇÃO E SISTEMAS</span>
        </div>
        <div class="title-area">
            CHECKLIST EQUIPAMENTO Nº <?php echo sprintf('%04d', $checklist->idChecklist); ?>
        </div>
    </div>

    <!-- Informações -->
    <div class="info-grid">
        <div class="bg-orange">Técnico:</div>
        <div><?php echo !empty($checklist->nome_tecnico) ? $checklist->nome_tecnico : (isset($tecnicoResp) ? $tecnicoResp : ''); ?></div>
        <div class="bg-orange">Data:</div>
        <div><?php echo date('d/m/Y', strtotime($checklist->data_checklist ?: $checklist->data_criacao)); ?></div>
    </div>
    <div class="client-row">
        <div>DADOS DO CLIENTE:</div>
        <div><?php echo $result->nomeCliente; ?></div>
    </div>

    <!-- Seção 1 -->
    <div class="section-title bg-orange">1-Intens de verificação(verificação mensal)</div>
    
    <?php if(empty($matriz)): ?>
        <p style="text-align:center; padding: 20px;">Nenhum item salvo neste checklist.</p>
    <?php else: ?>
        <?php foreach($matriz as $nomeSistema => $dados): ?>
            <div class="section-title bg-orange" style="border-top: 1px solid #000;"><?php echo $nomeSistema; ?></div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th class="col-local">Local/Nome:</th>
                            <?php foreach($dados['checks'] as $checkDesc): ?>
                                <th class="col-check"><div class="vertical-text"><?php echo $checkDesc; ?></div></th>
                            <?php endforeach; ?>
                            <th class="col-obs" style="text-align: center;">Observações</th>
                            <th class="col-ordem"><div class="vertical-text">Ordens de serviços</div></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($dados['locais'] as $localNome => $localData): ?>
                            <tr>
                                <td class="text-left"><?php echo $localNome; ?></td>
                                <?php foreach($dados['checks'] as $checkDesc): 
                                    $status = isset($localData['checks'][$checkDesc]) ? $localData['checks'][$checkDesc] : '';
                                    $classeStatus = '';
                                    if($status == 'V') $classeStatus = 'status-v';
                                    if($status == 'X') $classeStatus = 'status-x';
                                    if($status == 'O') $classeStatus = 'status-o';
                                ?>
                                    <td class="<?php echo $classeStatus; ?>"><?php echo $status; ?></td>
                                <?php endforeach; ?>
                                <td><?php echo $localData['obs_local']; ?></td>
                                <td><?php echo $localData['os_local']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <br>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Rodapé e Observações -->
    <div class="footer-section">
        <div style="font-weight: bold; font-size: 14px; margin-top: 5px; margin-bottom: 5px;">Observações Gerais:</div>
        <div class="obs-box"><?php echo htmlspecialchars($checklist->obs_gerais); ?></div>

        <table class="signatures-table">
            <tr>
                <td style="width: 34%; text-align: center; vertical-align: bottom;">
                    <div class="company-info">
                        <strong>STE AUTOMAÇÕES E SITEMAS</strong><br>
                        C.G.C. 31.569.670/0001-57 C.G.F 06.420803-6<br>
                        RUA NOGUEIRA ACIOLI, 371 - PRAIA DE IRACEMA<br>
                        FORTALEZA - CEARÁ - 60.110-140<br>
                        FONE/FAX: (0XX85)3226-7154/40629558<br>
                        E-mail: steautomacaoesistema@gmail.com<br>
                        site: <a href="https://steautomacaoesistemas.com.br/">https://steautomacaoesistemas.com.br/</a>
                    </div>
                </td>
                <td style="width: 33%; text-align: center; vertical-align: bottom;">
                    <div class="signature-wrapper">
                        <div class="pad-container">
                            <?php if($checklist->assinatura_cliente): ?>
                                <img src="<?php echo $checklist->assinatura_cliente; ?>" alt="Assinatura Cliente">
                            <?php endif; ?>
                        </div>
                        <div class="sig-line">Assinatura do Cliente</div>
                    </div>
                </td>
                <td style="width: 33%; text-align: center; vertical-align: bottom;">
                    <div class="signature-wrapper">
                        <div class="pad-container">
                            <?php if($checklist->assinatura_tecnico): ?>
                                <img src="<?php echo $checklist->assinatura_tecnico; ?>" alt="Assinatura Técnico">
                            <?php endif; ?>
                        </div>
                        <div class="sig-line">Técnico: <?php echo !empty($checklist->nome_tecnico) ? $checklist->nome_tecnico : ''; ?></div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>

<script>

    <?php if (isset($autoPrint) && $autoPrint): ?>
    window.onload = function() {
        window.print();
    };
    <?php endif; ?>
</script>

</body>
</html>