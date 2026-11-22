-- Migration 026: Adiciona as 35 novas chaves granulares na tabela permissoes (totalizando 92 chaves)
-- Observacao: A aplicacao automatizada e heranca por perfil ocorrem via migration PHP
-- application/database/migrations/20261122153000_add_granular_permissions.php (`php index.php tools migrate`).

-- Garante que o perfil Administrador (idPermissao = 1) possua todas as novas chaves habilitadas em JSON valido:
UPDATE `permissoes`
SET `permissoes` = JSON_MERGE_PATCH(
    `permissoes`,
    JSON_OBJECT(
        'vContrato', '1', 'aContrato', '1', 'eContrato', '1', 'dContrato', '1',
        'vNfse', '1', 'aNfse', '1', 'eNfse', '1', 'dNfse', '1',
        'vEquipamento', '1', 'aEquipamento', '1', 'eEquipamento', '1', 'dEquipamento', '1',
        'vSistema', '1', 'aSistema', '1', 'eSistema', '1', 'dSistema', '1',
        'vModelo', '1', 'aModelo', '1', 'eModelo', '1', 'dModelo', '1',
        'vTecnico', '1', 'aTecnico', '1', 'eTecnico', '1', 'dTecnico', '1',
        'vConta', '1', 'aConta', '1', 'eConta', '1', 'dConta', '1',
        'vCadFinanceiro', '1', 'aCadFinanceiro', '1', 'eCadFinanceiro', '1', 'dCadFinanceiro', '1',
        'rComissao', '1', 'vLogsAsaas', '1', 'cAsaas', '1',
        'aPagamento', '1', 'ePagamento', '1', 'dPagamento', '1', 'vPagamento', '1'
    )
)
WHERE (`idPermissao` = 1 OR UPPER(TRIM(`nome`)) = 'ADMINISTRADOR')
  AND JSON_VALID(`permissoes`);
