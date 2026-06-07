-- ============================================================
-- Migration: 005_criar_tabela_contas.sql
-- Projeto: steosMapos v4.53.2
-- Fase 2 — Banco de Dados
-- Descrição: Contas bancárias com saldo, tipo, agência e controle
--            de vencimento de cartão de crédito.
-- Dependências: Nenhuma (tabela independente)
-- ============================================================

CREATE TABLE IF NOT EXISTS `contas` (
  `idContas` int(11) NOT NULL AUTO_INCREMENT,
  `conta` varchar(255) NOT NULL COMMENT 'Nome/identificação da conta',
  `banco` varchar(100) DEFAULT NULL COMMENT 'Nome do banco',
  `tipo` varchar(50) DEFAULT NULL COMMENT 'Ex: Corrente, Poupança, Caixa, Cartão',
  `agencia` varchar(20) DEFAULT NULL COMMENT 'Número da agência',
  `numero` varchar(50) DEFAULT NULL COMMENT 'Número da conta',
  `saldo` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Saldo atual da conta',
  `vencimento_cartao` date DEFAULT NULL COMMENT 'Vencimento do cartão de crédito (se aplicável)',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=ativa, 0=inativa',
  `cadastro` date NOT NULL DEFAULT (CURRENT_DATE) COMMENT 'Data de cadastro',
  PRIMARY KEY (`idContas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Contas bancárias e de caixa';
