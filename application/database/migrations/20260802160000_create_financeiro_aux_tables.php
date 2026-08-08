<?php

class Migration_create_financeiro_aux_tables extends CI_Migration
{
    public function up()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `centro_gastos` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `nome` varchar(100) NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `grupo_financeiro` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `nome` varchar(100) NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Inserir dados padrão (Retrocompatibilidade)
        $centros = ['SERVICOS', 'VENDAS', 'OPERACIONAIS', 'RH', 'ADMINISTRATIVO', 'MARKETING', 'GASTOS FINANCEIROS', 'INVESTIMENTOS'];
        foreach ($centros as $c) {
            $this->db->query("INSERT IGNORE INTO `centro_gastos` (`nome`) VALUES ('$c')");
        }

        $grupos = ['Receita', 'Despesa'];
        foreach ($grupos as $g) {
            $this->db->query("INSERT IGNORE INTO `grupo_financeiro` (`nome`) VALUES ('$g')");
        }
    }

    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS `centro_gastos`;");
        $this->db->query("DROP TABLE IF EXISTS `grupo_financeiro`;");
    }
}
