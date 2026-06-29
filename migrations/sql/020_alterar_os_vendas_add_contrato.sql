-- ============================================================
-- Migration: 020_alterar_os_vendas_add_contrato.sql
-- Projeto: Steos v4.53.2
-- Descrição: Adiciona o campo contratos_id nas tabelas os e vendas
-- ============================================================

ALTER TABLE `os` 
ADD COLUMN `contratos_id` int(11) DEFAULT NULL AFTER `clientes_id`,
ADD KEY `fk_os_contratos1_idx` (`contratos_id`),
ADD CONSTRAINT `fk_os_contratos1` FOREIGN KEY (`contratos_id`) REFERENCES `contratos` (`idContratos`) ON DELETE SET NULL ON UPDATE NO ACTION;

ALTER TABLE `vendas` 
ADD COLUMN `contratos_id` int(11) DEFAULT NULL AFTER `clientes_id`,
ADD KEY `fk_vendas_contratos1_idx` (`contratos_id`),
ADD CONSTRAINT `fk_vendas_contratos1` FOREIGN KEY (`contratos_id`) REFERENCES `contratos` (`idContratos`) ON DELETE SET NULL ON UPDATE NO ACTION;
