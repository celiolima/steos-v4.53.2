<?php

class Migration_Alter_servicos_nfse_varchar_length extends CI_Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE `servicos_nfse` MODIFY COLUMN `codigo_servico_municipal` VARCHAR(255) NOT NULL;");
        $this->db->query("ALTER TABLE `servicos_nfse` MODIFY COLUMN `codigo_nbs` VARCHAR(255) DEFAULT NULL;");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `servicos_nfse` MODIFY COLUMN `codigo_servico_municipal` VARCHAR(50) NOT NULL;");
        $this->db->query("ALTER TABLE `servicos_nfse` MODIFY COLUMN `codigo_nbs` VARCHAR(50) DEFAULT NULL;");
    }
}
