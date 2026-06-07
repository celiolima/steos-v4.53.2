-- ============================================================
-- Migration: 006_criar_tabela_lancamentos_contas.sql
-- Projeto: steosMapos v4.53.2
-- Fase 2 — Banco de Dados
-- Descrição: Extrato de movimentações de cada conta bancária.
--            Registra entrada/saída/transferência com saldo acumulado.
-- Dependências: contas (005)
-- ============================================================

CREATE TABLE IF NOT EXISTS `lancamentos_contas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contas_id` int(11) NOT NULL COMMENT 'FK: contas.idContas',
  `lancamento` decimal(15,2) NOT NULL COMMENT 'Valor do lançamento (positivo=entrada, negativo=saída)',
  `saldo` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Saldo da conta após o lançamento',
  `tipo_lacamento` varchar(50) NOT NULL COMMENT 'ENTRADA, SAIDA, TRANSFERENCIA',
  `dataLancamento` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data e hora do lançamento',
  PRIMARY KEY (`id`),
  KEY `idx_lancamentos_contas_conta` (`contas_id`),
  KEY `idx_lancamentos_contas_data` (`dataLancamento`),
  CONSTRAINT `fk_lancamentos_contas_conta` FOREIGN KEY (`contas_id`) REFERENCES `contas` (`idContas`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Extrato de movimentações financeiras por conta';
