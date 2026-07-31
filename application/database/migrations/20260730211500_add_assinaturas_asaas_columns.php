<?php

class Migration_add_assinaturas_asaas_columns extends CI_Migration
{
    public function up()
    {
        // Tabela contratos
        if (!$this->db->field_exists('asaas_subscription_id', 'contratos')) {
            $this->dbforge->add_column('contratos', [
                'asaas_subscription_id' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '100',
                    'null'       => true,
                    'after'      => 'valorTotal'
                ]
            ]);
        }

        if (!$this->db->field_exists('data_ativacao_assinatura', 'contratos')) {
            $this->dbforge->add_column('contratos', [
                'data_ativacao_assinatura' => [
                    'type' => 'DATE',
                    'null' => true,
                    'after' => 'asaas_subscription_id'
                ]
            ]);
        }

        // Tabela OS
        if (!$this->db->field_exists('asaas_payment_id', 'os')) {
            $this->dbforge->add_column('os', [
                'asaas_payment_id' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '100',
                    'null'       => true,
                    'after'      => 'manutPreventiva'
                ]
            ]);
        }
    }

    public function down()
    {
        if ($this->db->field_exists('asaas_subscription_id', 'contratos')) {
            $this->dbforge->drop_column('contratos', 'asaas_subscription_id');
        }
        if ($this->db->field_exists('data_ativacao_assinatura', 'contratos')) {
            $this->dbforge->drop_column('contratos', 'data_ativacao_assinatura');
        }
        if ($this->db->field_exists('asaas_payment_id', 'os')) {
            $this->dbforge->drop_column('os', 'asaas_payment_id');
        }
    }
}
