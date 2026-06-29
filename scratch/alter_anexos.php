<?php
require_once __DIR__ . '/../application/.env';
$env = file_get_contents(__DIR__ . '/../application/.env');
$lines = explode("\n", $env);
$config = [];
foreach ($lines as $line) {
    if (strpos($line, '=') !== false) {
        list($k, $v) = explode('=', $line, 2);
        $config[trim($k)] = trim($v);
    }
}
$mysqli = new mysqli($config['DB_HOSTNAME'], $config['DB_USERNAME'], $config['DB_PASSWORD'], $config['DB_DATABASE'], $config['DB_PORT'] ?? 3306);
if ($mysqli->connect_errno) {
    die("Connect failed: " . $mysqli->connect_error);
}
$res = $mysqli->query("ALTER TABLE anexos ADD COLUMN contrato_id int(11) DEFAULT NULL;");
if ($res) echo "Added column contrato_id.\n"; else echo "Error: " . $mysqli->error . "\n";
$res = $mysqli->query("ALTER TABLE anexos ADD CONSTRAINT fk_anexos_contratos1 FOREIGN KEY (contrato_id) REFERENCES contratos (idContratos);");
if ($res) echo "Added FK.\n"; else echo "Error: " . $mysqli->error . "\n";
