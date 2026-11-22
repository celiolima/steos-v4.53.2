-- Migration 027: Cria o perfil ACL "API MCP Admin" (92 chaves) e a conta de servico "Agente MCP STEOS"
-- Corresponde a migration PHP: application/database/migrations/20261122170000_add_mcp_service_profile.php
--
-- IMPORTANTE:
--   Prefira executar via CLI CodeIgniter (`php index.php tools migrate`), pois a migration PHP
--   le $_ENV['MCP_SERVICE_INIT_PASSWORD'] (ou gera senha aleatoria segura com random_bytes(24))
--   e aplica password_hash(..., PASSWORD_DEFAULT) automaticamente sem segredos em texto claro.
--   Caso execute este SQL manualmente, gere um hash bcrypt via PHP `password_hash()` e substitua
--   o placeholder '<GERAR_HASH_BCRYPT_VIA_PASSWORD_HASH>' antes de rodar.

INSERT INTO `permissoes` (`nome`, `permissoes`, `situacao`, `data`)
SELECT
    'API MCP Admin',
    JSON_OBJECT(
        'aCliente', '1', 'eCliente', '1', 'dCliente', NULL, 'vCliente', '1',
        'aProduto', NULL, 'eProduto', NULL, 'dProduto', NULL, 'vProduto', '1',
        'aServico', NULL, 'eServico', NULL, 'dServico', NULL, 'vServico', '1',
        'aOs', '1', 'eOs', '1', 'dOs', NULL, 'vOs', '1',
        'aVenda', NULL, 'eVenda', NULL, 'dVenda', NULL, 'vVenda', '1',
        'aArquivo', NULL, 'eArquivo', NULL, 'dArquivo', NULL, 'vArquivo', '1',
        'aPagamento', NULL, 'ePagamento', NULL, 'dPagamento', NULL, 'vPagamento', '1',
        'aLancamento', NULL, 'eLancamento', NULL, 'dLancamento', NULL, 'vLancamento', '1',
        'cUsuario', NULL, 'cEmitente', NULL, 'cPermissao', NULL, 'cBackup', NULL, 'cAuditoria', NULL, 'cEmail', NULL, 'cSistema', NULL,
        'rCliente', NULL, 'rProduto', NULL, 'rServico', NULL, 'rOs', NULL, 'rVenda', NULL, 'rFinanceiro', NULL, 'rComissao', NULL,
        'vGarantia', '1', 'aGarantia', NULL, 'eGarantia', NULL, 'dGarantia', NULL,
        'aCobranca', NULL, 'eCobranca', NULL, 'dCobranca', NULL, 'vCobranca', '1',
        'aVeiculo', NULL, 'eVeiculo', NULL, 'dVeiculo', NULL, 'vVeiculo', '1',
        'vContrato', '1', 'aContrato', NULL, 'eContrato', NULL, 'dContrato', NULL,
        'vNfse', '1', 'aNfse', '1', 'eNfse', '1', 'dNfse', '1',
        'vEquipamento', '1', 'aEquipamento', NULL, 'eEquipamento', NULL, 'dEquipamento', NULL,
        'vSistema', '1', 'aSistema', NULL, 'eSistema', NULL, 'dSistema', NULL,
        'vModelo', '1', 'aModelo', NULL, 'eModelo', NULL, 'dModelo', NULL,
        'vTecnico', '1', 'aTecnico', NULL, 'eTecnico', NULL, 'dTecnico', NULL,
        'vConta', '1', 'aConta', NULL, 'eConta', NULL, 'dConta', NULL,
        'vCadFinanceiro', '1', 'aCadFinanceiro', NULL, 'eCadFinanceiro', NULL, 'dCadFinanceiro', NULL,
        'vLogsAsaas', '1', 'cAsaas', NULL
    ),
    1,
    CURDATE()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `permissoes` WHERE `nome` = 'API MCP Admin'
);

INSERT INTO `usuarios` (
    `nome`, `rg`, `cpf`, `cep`, `rua`, `numero`, `bairro`, `cidade`, `estado`,
    `email`, `senha`, `telefone`, `celular`, `situacao`, `dataCadastro`,
    `permissoes_id`, `dataExpiracao`, `tecnico`
)
SELECT
    'Agente MCP STEOS', '', '00000000000', '00000-000', 'Integracao MCP', 'S/N', 'Sistema', 'Sistema', 'SP',
    'mcp.service@stesistemas.com', '<GERAR_HASH_BCRYPT_VIA_PASSWORD_HASH>', '0000000000', '', 1, CURDATE(),
    (SELECT `idPermissao` FROM `permissoes` WHERE `nome` = 'API MCP Admin' LIMIT 1), '2035-12-31', '0'
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `usuarios` WHERE `email` = 'mcp.service@stesistemas.com'
);
