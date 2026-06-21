-- ============================================================
-- Migration: 019_alter_tabela_os_complemento.sql
-- Projeto: steosSteos v4.53.2
-- ============================================================

ALTER TABLE `os`
  ADD COLUMN `equipamentos_id` INT(11) NULL DEFAULT NULL AFTER `signature`,
  ADD COLUMN `tipo` VARCHAR(45) NULL DEFAULT NULL AFTER `defeito`,
  ADD COLUMN `local` VARCHAR(45) NULL DEFAULT NULL AFTER `tipo`,
  ADD COLUMN `defeito_encontrado` TEXT NULL DEFAULT NULL AFTER `local`;
