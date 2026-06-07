-- ============================================================
-- Migration: 012_criar_tabela_bancos.sql
-- Projeto: steosMapos v4.53.2
-- Fase 2 — Banco de Dados
-- Descrição: Tabela de bancos para futura vinculação com contas.
--            PRIORIDADE BAIXA — controller Bancos.php está incompleto
--            no steos e NÃO foi migrado.
-- Dependências: Nenhuma
-- STATUS: Criada para preservar schema futuro. Não utilizada ainda.
-- ============================================================

CREATE TABLE IF NOT EXISTS `bancos` (
  `idBancos` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL COMMENT 'Nome do banco',
  `codigo` varchar(10) DEFAULT NULL COMMENT 'Código BACEN do banco',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=ativo, 0=inativo',
  PRIMARY KEY (`idBancos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Cadastro de bancos — uso futuro (controller incompleto no steos)';

-- Dados de bancos mais comuns no Brasil
INSERT IGNORE INTO `bancos` (`nome`, `codigo`) VALUES
  ('Banco do Brasil', '001'),
  ('Bradesco', '237'),
  ('Caixa Econômica Federal', '104'),
  ('Itaú', '341'),
  ('Santander', '033'),
  ('Nubank', '260'),
  ('Inter', '077'),
  ('Sicoob', '756'),
  ('Outro', '000');
