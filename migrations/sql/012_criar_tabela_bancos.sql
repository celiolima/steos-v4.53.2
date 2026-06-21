-- ============================================================
-- Migration: 012_criar_tabela_bancos.sql
-- Projeto: steosSteos v4.53.2
-- Fase 2 — Banco de Dados
-- Descrição: Tabela de bancos para futura vinculação com contas.
--            PRIORIDADE BAIXA — controller Bancos.php está incompleto
--            no steos e NÃO foi migrado.
-- Dependências: Nenhuma
-- STATUS: Criada para preservar schema futuro. Não utilizada ainda.
-- ============================================================

CREATE TABLE IF NOT EXISTS `bancos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Cadastro de bancos — uso futuro (controller incompleto no steos)';

-- Dados de bancos mais comuns no Brasil
INSERT IGNORE INTO `bancos` (`nome`) VALUES
  ('Banco do Brasil'),
  ('Bradesco'),
  ('Caixa Econômica Federal'),
  ('Itaú'),
  ('Santander'),
  ('Nubank'),
  ('Inter'),
  ('Sicoob'),
  ('Outro');
