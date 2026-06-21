-- ============================================================
-- Migration: 015_configuracoes_insert.sql
-- Projeto: steosSteos v4.53.2
-- Fase 2 — Banco de Dados
-- Descrição: Insere 4 novas chaves de configuração para os
--            módulos adicionados pelo steos.
--            Usa INSERT IGNORE para segurança idempotente.
-- Dependências: configuracoes (tabela existente)
-- ============================================================

-- Módulo de Veículos (controle de frota)
INSERT IGNORE INTO `configuracoes` (`chave`, `valor`)
  VALUES ('modulo_veiculos', '0');

-- Módulo de Técnicos (CRUD e comissão de técnicos)
INSERT IGNORE INTO `configuracoes` (`chave`, `valor`)
  VALUES ('modulo_tecnicos', '0');

-- Módulo de Contas (contas bancárias e caixa)
INSERT IGNORE INTO `configuracoes` (`chave`, `valor`)
  VALUES ('modulo_contas', '0');

-- Módulo de Modelos (templates de documentos)
INSERT IGNORE INTO `configuracoes` (`chave`, `valor`)
  VALUES ('modulo_modelos', '0');

-- Verificação: listar as configurações inseridas
-- SELECT * FROM configuracoes WHERE chave IN ('modulo_veiculos','modulo_tecnicos','modulo_contas','modulo_modelos');
