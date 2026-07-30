<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CliMigration extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!is_cli()) {
            exit('No direct script access allowed');
        }
    }

    public function migrate()
    {
        $this->load->database();
        if (!$this->db->field_exists('prioridade', 'os')) {
            $this->db->query("ALTER TABLE `os` ADD COLUMN `prioridade` VARCHAR(50) NULL DEFAULT 'sem'");
            $this->db->query("ALTER TABLE `os` ADD COLUMN `classificacao` VARCHAR(50) NULL DEFAULT 'CORREÇÃO'");
            echo "Colunas adicionadas com sucesso!\n";
        } else {
            echo "Colunas ja existem!\n";
        }
    }
}
