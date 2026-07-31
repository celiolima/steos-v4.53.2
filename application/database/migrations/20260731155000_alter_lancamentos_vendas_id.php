<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Alter_lancamentos_vendas_id extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('lancamentos')) {
            $this->db->query("ALTER TABLE lancamentos MODIFY vendas_id INT NULL;");
        }
    }

    public function down()
    {
        // Reverter não é estritamente necessário nem seguro caso existam valores NULL,
        // mas a título de rollback, ficaria assim:
        // $this->db->query("ALTER TABLE lancamentos MODIFY vendas_id INT NOT NULL;");
    }
}
