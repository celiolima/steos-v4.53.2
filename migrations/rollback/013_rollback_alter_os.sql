-- ============================================================
-- Rollback: 013_rollback_alter_os.sql
-- Desfaz: 013_alter_tabela_os.sql
-- ATENÇÃO: Remove as 5 colunas adicionadas. Dados serão perdidos!
-- ============================================================
ALTER TABLE `os`
  DROP COLUMN IF EXISTS `vendedor`,
  DROP COLUMN IF EXISTS `afaturar`,
  DROP COLUMN IF EXISTS `faturado`,
  DROP COLUMN IF EXISTS `manPrevnt`,
  DROP COLUMN IF EXISTS `signature`;

DROP INDEX IF EXISTS `idx_os_afaturar` ON `os`;
DROP INDEX IF EXISTS `idx_os_faturado` ON `os`;
