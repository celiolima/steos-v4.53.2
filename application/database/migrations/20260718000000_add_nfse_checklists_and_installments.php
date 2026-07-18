<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Migration_add_nfse_checklists_and_installments extends CI_Migration
{
    public function up()
    {
        // 1. Criar tabela servicos_nfse (se não existir)
        $this->db->query("CREATE TABLE IF NOT EXISTS `servicos_nfse` (
            `idServicosNfse` int(11) NOT NULL AUTO_INCREMENT,
            `nome_servico` varchar(255) NOT NULL,
            `codigo_servico_municipal` varchar(50) NOT NULL,
            `codigo_nbs` varchar(50) DEFAULT NULL,
            `aliquota` decimal(5,2) NOT NULL DEFAULT 0.00,
            PRIMARY KEY (`idServicosNfse`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");

        // 2. Adicionar colunas NFS-e e manutPreventiva em `os` (se não existirem)
        if (!$this->db->field_exists('manutPreventiva', 'os')) {
            $this->db->query("ALTER TABLE `os` ADD COLUMN `manutPreventiva` tinyint(1) DEFAULT 0 AFTER `local`;");
        }
        if (!$this->db->field_exists('asaas_invoice_id', 'os')) {
            $this->db->query("ALTER TABLE `os` ADD COLUMN `asaas_invoice_id` varchar(100) DEFAULT NULL AFTER `status`;");
            $this->db->query("ALTER TABLE `os` ADD INDEX `idx_os_invoice_id` (`asaas_invoice_id`);");
        }
        if (!$this->db->field_exists('asaas_invoice_status', 'os')) {
            $this->db->query("ALTER TABLE `os` ADD COLUMN `asaas_invoice_status` varchar(50) DEFAULT NULL AFTER `asaas_invoice_id`;");
        }
        if (!$this->db->field_exists('asaas_invoice_number', 'os')) {
            $this->db->query("ALTER TABLE `os` ADD COLUMN `asaas_invoice_number` varchar(50) DEFAULT NULL AFTER `asaas_invoice_status`;");
        }
        if (!$this->db->field_exists('asaas_invoice_pdf', 'os')) {
            $this->db->query("ALTER TABLE `os` ADD COLUMN `asaas_invoice_pdf` text DEFAULT NULL AFTER `asaas_invoice_number`;");
        }
        if (!$this->db->field_exists('asaas_invoice_xml', 'os')) {
            $this->db->query("ALTER TABLE `os` ADD COLUMN `asaas_invoice_xml` text DEFAULT NULL AFTER `asaas_invoice_pdf`;");
        }
        if (!$this->db->field_exists('asaas_invoice_error', 'os')) {
            $this->db->query("ALTER TABLE `os` ADD COLUMN `asaas_invoice_error` text DEFAULT NULL AFTER `asaas_invoice_xml`;");
        }

        // 3. Adicionar coluna installment_id em `cobrancas` (se não existir)
        if ($this->db->table_exists('cobrancas') && !$this->db->field_exists('installment_id', 'cobrancas')) {
            $this->db->query("ALTER TABLE `cobrancas` ADD COLUMN `installment_id` varchar(50) DEFAULT NULL AFTER `charge_id`;");
        }

        // 4. Criar e atualizar tabelas de checklist `os_checklists` e `os_checklists_itens`
        $this->db->query("CREATE TABLE IF NOT EXISTS `os_checklists` (
            `idChecklist` int(11) NOT NULL AUTO_INCREMENT,
            `os_id` int(11) NOT NULL,
            `contratos_id` int(11) NOT NULL,
            `data_criacao` datetime NOT NULL,
            `usuarios_id` int(11) NOT NULL,
            `status` varchar(50) DEFAULT 'Aberto',
            `observacoes` text DEFAULT NULL,
            PRIMARY KEY (`idChecklist`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");

        if (!$this->db->field_exists('assinatura_cliente', 'os_checklists')) {
            $this->db->query("ALTER TABLE `os_checklists` ADD COLUMN `assinatura_cliente` longtext DEFAULT NULL;");
        }
        if (!$this->db->field_exists('assinatura_tecnico', 'os_checklists')) {
            $this->db->query("ALTER TABLE `os_checklists` ADD COLUMN `assinatura_tecnico` longtext DEFAULT NULL;");
        }
        if (!$this->db->field_exists('nome_tecnico', 'os_checklists')) {
            $this->db->query("ALTER TABLE `os_checklists` ADD COLUMN `nome_tecnico` varchar(100) DEFAULT NULL;");
        }
        if (!$this->db->field_exists('data_checklist', 'os_checklists')) {
            $this->db->query("ALTER TABLE `os_checklists` ADD COLUMN `data_checklist` date DEFAULT NULL;");
        }
        if (!$this->db->field_exists('obs_gerais', 'os_checklists')) {
            $this->db->query("ALTER TABLE `os_checklists` ADD COLUMN `obs_gerais` text DEFAULT NULL;");
        }

        $this->db->query("CREATE TABLE IF NOT EXISTS `os_checklists_itens` (
            `idItem` int(11) NOT NULL AUTO_INCREMENT,
            `checklist_id` int(11) NOT NULL,
            `descricao` text NOT NULL,
            `concluido` tinyint(1) DEFAULT 0,
            `observacoes` text DEFAULT NULL,
            PRIMARY KEY (`idItem`),
            KEY `fk_os_checklists_itens` (`checklist_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");

        if (!$this->db->field_exists('sistema', 'os_checklists_itens')) {
            $this->db->query("ALTER TABLE `os_checklists_itens` ADD COLUMN `sistema` varchar(100) DEFAULT NULL;");
        }
        if (!$this->db->field_exists('local', 'os_checklists_itens')) {
            $this->db->query("ALTER TABLE `os_checklists_itens` ADD COLUMN `local` varchar(100) DEFAULT NULL;");
        }
        if (!$this->db->field_exists('check_desc', 'os_checklists_itens')) {
            $this->db->query("ALTER TABLE `os_checklists_itens` ADD COLUMN `check_desc` varchar(255) DEFAULT NULL;");
        }
        if (!$this->db->field_exists('status', 'os_checklists_itens')) {
            $this->db->query("ALTER TABLE `os_checklists_itens` ADD COLUMN `status` varchar(10) DEFAULT NULL;");
        }
        if (!$this->db->field_exists('obs_local', 'os_checklists_itens')) {
            $this->db->query("ALTER TABLE `os_checklists_itens` ADD COLUMN `obs_local` text DEFAULT NULL;");
        }
        if (!$this->db->field_exists('os_local', 'os_checklists_itens')) {
            $this->db->query("ALTER TABLE `os_checklists_itens` ADD COLUMN `os_local` varchar(50) DEFAULT NULL;");
        }
    }

    public function down()
    {
        // Revert (opcional e seguro)
        if ($this->db->field_exists('manutPreventiva', 'os')) {
            $this->db->query("ALTER TABLE `os` DROP COLUMN `manutPreventiva`;");
        }
        if ($this->db->field_exists('asaas_invoice_id', 'os')) {
            $this->db->query("ALTER TABLE `os` DROP COLUMN `asaas_invoice_id`;");
        }
        if ($this->db->field_exists('asaas_invoice_status', 'os')) {
            $this->db->query("ALTER TABLE `os` DROP COLUMN `asaas_invoice_status`;");
        }
        if ($this->db->field_exists('asaas_invoice_number', 'os')) {
            $this->db->query("ALTER TABLE `os` DROP COLUMN `asaas_invoice_number`;");
        }
        if ($this->db->field_exists('asaas_invoice_pdf', 'os')) {
            $this->db->query("ALTER TABLE `os` DROP COLUMN `asaas_invoice_pdf`;");
        }
        if ($this->db->field_exists('asaas_invoice_xml', 'os')) {
            $this->db->query("ALTER TABLE `os` DROP COLUMN `asaas_invoice_xml`;");
        }
        if ($this->db->field_exists('asaas_invoice_error', 'os')) {
            $this->db->query("ALTER TABLE `os` DROP COLUMN `asaas_invoice_error`;");
        }
        if ($this->db->table_exists('cobrancas') && $this->db->field_exists('installment_id', 'cobrancas')) {
            $this->db->query("ALTER TABLE `cobrancas` DROP COLUMN `installment_id`;");
        }
        $this->db->query("DROP TABLE IF EXISTS `servicos_nfse`;");
        $this->db->query("DROP TABLE IF EXISTS `os_checklists_itens`;");
        $this->db->query("DROP TABLE IF EXISTS `os_checklists`;");
    }
}
