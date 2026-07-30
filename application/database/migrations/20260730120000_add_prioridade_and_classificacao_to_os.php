<?php

class Migration_add_prioridade_and_classificacao_to_os extends CI_Migration
{
    public function up()
    {
        $fields = [
            'prioridade' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => TRUE,
                'default'    => NULL,
            ],
            'classificacao' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => TRUE,
                'default'    => NULL,
            ],
        ];

        // Verificar se os campos já existem antes de adicionar
        if (!$this->db->field_exists('prioridade', 'os')) {
            $this->dbforge->add_column('os', ['prioridade' => $fields['prioridade']]);
        }
        
        if (!$this->db->field_exists('classificacao', 'os')) {
            $this->dbforge->add_column('os', ['classificacao' => $fields['classificacao']]);
        }
    }

    public function down()
    {
        if ($this->db->field_exists('prioridade', 'os')) {
            $this->dbforge->drop_column('os', 'prioridade');
        }
        
        if ($this->db->field_exists('classificacao', 'os')) {
            $this->dbforge->drop_column('os', 'classificacao');
        }
    }
}
