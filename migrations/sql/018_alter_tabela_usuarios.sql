-- ============================================================
-- Migration: 018_alter_tabela_usuarios.sql
-- Projeto: steosSteos v4.53.2
-- ============================================================

ALTER TABLE `usuarios`
  ADD COLUMN `tecnico` VARCHAR(50) NOT NULL DEFAULT '0'
    COMMENT 'Identificador ou flag para usuarios que operam como tecnico';
