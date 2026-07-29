<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_asaas_service_id extends CI_Migration {

    public function up()
    {
        if (!$this->db->field_exists('asaas_service_id', 'servicos_nfse')) {
            $fields = array(
                'asaas_service_id' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '50',
                    'null' => TRUE,
                    'after' => 'codigo_servico_municipal'
                )
            );
            $this->dbforge->add_column('servicos_nfse', $fields);
        }
    }

    public function down()
    {
        $this->dbforge->drop_column('servicos_nfse', 'asaas_service_id');
    }
}
