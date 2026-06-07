-- ============================================================
-- Rollback: 014_rollback_alter_lancamentos.sql
-- Desfaz: 014_alter_tabela_lancamentos.sql
-- ATENÇÃO: Remove as 4 colunas adicionadas. Dados serão perdidos!
-- ============================================================
ALTER TABLE `lancamentos`
  DROP COLUMN IF EXISTS `centro_de_gastos`,
  DROP COLUMN IF EXISTS `classificacao_fin`,
  DROP COLUMN IF EXISTS `grupo_financeiro`,
  DROP COLUMN IF EXISTS `forma_pgto`;

DROP INDEX IF EXISTS `idx_lancamentos_centro` ON `lancamentos`;
DROP INDEX IF EXISTS `idx_lancamentos_class` ON `lancamentos`;
DROP INDEX IF EXISTS `idx_lancamentos_grupo` ON `lancamentos`;
