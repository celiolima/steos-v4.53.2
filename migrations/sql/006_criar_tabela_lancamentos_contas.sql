-- ============================================================
-- Migration: 006_criar_tabela_lancamentos_contas.sql
-- Projeto: steosSteos v4.53.2
-- Fase 2 — Banco de Dados
-- Descrição: Extrato de movimentações de cada conta bancária.
--            Registra entrada/saída/transferência com saldo acumulado.
-- Dependências: contas (005)
-- ============================================================

CREATE TABLE IF NOT EXISTS `lancamentos_contas` (
  `idlancamentos_contas` int(11) NOT NULL AUTO_INCREMENT,
  `dataLancamento` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data e hora do lançamento',
  `tipo_lacamento` varchar(50) DEFAULT NULL COMMENT 'ENTRADA, SAIDA, TRANSFERENCIA',
  `lancamento` decimal(10,2) DEFAULT NULL COMMENT 'Valor do lançamento (positivo=entrada, negativo=saída)',
  `saldo` decimal(10,2) DEFAULT NULL COMMENT 'Saldo da conta após o lançamento',
  `contas_id` int(11) NOT NULL COMMENT 'FK: contas.idContas',
  PRIMARY KEY (`idlancamentos_contas`),
  KEY `idx_lancamentos_contas_conta` (`contas_id`),
  KEY `idx_lancamentos_contas_data` (`dataLancamento`),
  CONSTRAINT `fk_lancamentos_contas_conta` FOREIGN KEY (`contas_id`) REFERENCES `contas` (`idContas`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Extrato de movimentações financeiras por conta';
