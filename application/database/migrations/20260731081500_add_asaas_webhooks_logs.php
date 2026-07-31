<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_asaas_webhooks_logs extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'event' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE,
            ],
            'asaas_payment_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => FALSE,
                'default' => 'SUCESSO'
            ],
            'mensagem_erro' => [
                'type' => 'TEXT',
                'null' => TRUE,
            ],
            'data_recebimento' => [
                'type' => 'DATETIME',
                'null' => FALSE,
            ]
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('asaas_payment_id');
        $this->dbforge->create_table('asaas_webhooks_logs', TRUE);
    }

    public function down()
    {
        $this->dbforge->drop_table('asaas_webhooks_logs', TRUE);
    }
}
