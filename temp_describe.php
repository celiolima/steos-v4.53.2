<?php
define('ENVIRONMENT', 'development');
chdir(__DIR__);
require_once 'index.php';
$CI =& get_instance();
$CI->load->database();
$query = $CI->db->query('DESCRIBE contratos');
echo json_encode($query->result(), JSON_PRETTY_PRINT);
?>
