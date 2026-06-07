-- ============================================================
-- Rollback: 015_rollback_configuracoes.sql
-- Desfaz: 015_configuracoes_insert.sql
-- Remove as 4 chaves de configuração dos módulos novos
-- ============================================================
DELETE FROM `configuracoes`
  WHERE `chave` IN ('modulo_veiculos', 'modulo_tecnicos', 'modulo_contas', 'modulo_modelos');
