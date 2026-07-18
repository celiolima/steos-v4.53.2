-- Migration para suporte a emissão de NFS-e Asaas no Módulo Ordem de Serviço
ALTER TABLE `os`
ADD COLUMN `asaas_invoice_id` VARCHAR(100) NULL DEFAULT NULL AFTER `status`,
ADD COLUMN `asaas_invoice_status` VARCHAR(50) NULL DEFAULT NULL AFTER `asaas_invoice_id`,
ADD COLUMN `asaas_invoice_number` VARCHAR(50) NULL DEFAULT NULL AFTER `asaas_invoice_status`,
ADD COLUMN `asaas_invoice_pdf` TEXT NULL DEFAULT NULL AFTER `asaas_invoice_number`,
ADD COLUMN `asaas_invoice_xml` TEXT NULL DEFAULT NULL AFTER `asaas_invoice_pdf`,
ADD COLUMN `asaas_invoice_error` TEXT NULL DEFAULT NULL AFTER `asaas_invoice_xml`,
ADD INDEX `idx_os_invoice_id` (`asaas_invoice_id`);
