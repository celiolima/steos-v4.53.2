<?php
$_SERVER['SERVER_ADDR'] = '127.0.0.1'; // Mock para evitar erros de log no index.php
require_once 'index.php';
$CI =& get_instance();
$CI->db->query("ALTER TABLE servicos_nfse ADD COLUMN asaas_service_id VARCHAR(50) NULL AFTER codigo_servico_municipal");
echo "Coluna asaas_service_id adicionada com sucesso.\n";
?>
