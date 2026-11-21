<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_update_servicos_nfse_reforma_tributaria extends CI_Migration
{
    public function up()
    {
        if (!$this->db->table_exists('servicos_nfse')) {
            return;
        }

        if (!$this->db->field_exists('cst_ibs_cbs', 'servicos_nfse')) {
            $this->db->query("ALTER TABLE `servicos_nfse` ADD COLUMN `cst_ibs_cbs` VARCHAR(20) NULL DEFAULT '000' AFTER `aliquota`");
        }

        if (!$this->db->field_exists('class_trib_ibs_cbs', 'servicos_nfse')) {
            $this->db->query("ALTER TABLE `servicos_nfse` ADD COLUMN `class_trib_ibs_cbs` VARCHAR(20) NULL DEFAULT '000001' AFTER `cst_ibs_cbs`");
        }

        if (!$this->db->field_exists('ind_operacao', 'servicos_nfse')) {
            $this->db->query("ALTER TABLE `servicos_nfse` ADD COLUMN `ind_operacao` VARCHAR(20) NULL DEFAULT '' AFTER `class_trib_ibs_cbs`");
        }

        // Portal Nacional nao usa ID municipal legado; zera asaas_service_id e popula defaults IBS/CBS
        $this->db->query("UPDATE `servicos_nfse` SET `asaas_service_id` = NULL, `cst_ibs_cbs` = COALESCE(NULLIF(`cst_ibs_cbs`, ''), '000'), `class_trib_ibs_cbs` = COALESCE(NULLIF(`class_trib_ibs_cbs`, ''), '000001')");

        // Locacao 99.03: indicador de operacao 010106 e ISS 0.00
        $this->db->query("UPDATE `servicos_nfse` SET `ind_operacao` = '010106', `aliquota` = 0.00 WHERE `codigo_servico_municipal` LIKE '%99.03%'");

        // Demais servicos (14.01, 0101): indicador vazio e ISS 3.50 quando aplicavel
        $this->db->query("UPDATE `servicos_nfse` SET `ind_operacao` = COALESCE(`ind_operacao`, ''), `aliquota` = 3.50 WHERE (`codigo_servico_municipal` LIKE '%14.01%' OR `codigo_servico_municipal` LIKE '%0101%') AND `codigo_servico_municipal` NOT LIKE '%99.03%'");
    }

    public function down()
    {
        if (!$this->db->table_exists('servicos_nfse')) {
            return;
        }

        if ($this->db->field_exists('ind_operacao', 'servicos_nfse')) {
            $this->db->query("ALTER TABLE `servicos_nfse` DROP COLUMN `ind_operacao`");
        }

        if ($this->db->field_exists('class_trib_ibs_cbs', 'servicos_nfse')) {
            $this->db->query("ALTER TABLE `servicos_nfse` DROP COLUMN `class_trib_ibs_cbs`");
        }

        if ($this->db->field_exists('cst_ibs_cbs', 'servicos_nfse')) {
            $this->db->query("ALTER TABLE `servicos_nfse` DROP COLUMN `cst_ibs_cbs`");
        }
    }
}
