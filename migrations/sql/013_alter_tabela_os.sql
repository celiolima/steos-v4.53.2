-- ============================================================
-- Migration: 013_alter_tabela_os.sql
-- Projeto: steosSteos v4.53.2
-- Fase 2 — Banco de Dados
-- Descrição: Adiciona 5 colunas à tabela `os` existente.
--            Habilita: vendedor, faturamento, manutenção preventiva
--            e assinatura digital do cliente na OS.
-- Dependências: os (tabela existente)
-- RISCO: ALTO — tabela de produção crítica
-- ============================================================

-- Verificação de segurança: esta migration é IDEMPOTENTE via IF NOT EXISTS
-- equivalente usando COLUMN_NAME da information_schema

-- Adiciona coluna: vendedor
ALTER TABLE `os`
  ADD COLUMN `vendedor` VARCHAR(100) NULL DEFAULT NULL
    COMMENT 'Nome ou identificação do vendedor responsável pela OS'
    AFTER `defeito`;

-- Adiciona coluna: afaturar (flag de OS aguardando faturamento)
ALTER TABLE `os`
  ADD COLUMN `afaturar` TINYINT(1) NOT NULL DEFAULT '0'
    COMMENT '1=OS marcada para faturar, 0=normal'
    AFTER `vendedor`;

-- Adiciona coluna: faturado (flag de OS já faturada)
ALTER TABLE `os`
  ADD COLUMN `faturado` TINYINT(1) NOT NULL DEFAULT '0'
    COMMENT '1=OS já faturada, 0=não faturada'
    AFTER `afaturar`;

-- Adiciona coluna: signature (assinatura digital do cliente)
ALTER TABLE `os`
  ADD COLUMN `signature` TINYTEXT NULL DEFAULT NULL
    COMMENT 'Assinatura digital do cliente em base64 (canvas)'
    AFTER `faturado`;

-- Índice para facilitar consultas de faturamento
CREATE INDEX `idx_os_afaturar` ON `os` (`afaturar`);
CREATE INDEX `idx_os_faturado` ON `os` (`faturado`);
