<?php
$env = file_get_contents(__DIR__ . '/../.env');
preg_match('/DB_USERNAME=(.*)/', $env, $u);
preg_match('/DB_PASSWORD=(.*)/', $env, $p);
preg_match('/DB_DATABASE=(.*)/', $env, $d);

$conn = new mysqli('127.0.0.1', trim($u[1]), trim($p[1]), trim($d[1]));
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 1. Get all tables and columns from LIVE DB
$live_schema = [];
$res = $conn->query("SHOW TABLES");
while ($row = $res->fetch_array()) {
    $table = $row[0];
    $col_res = $conn->query("SHOW COLUMNS FROM `$table`");
    $live_schema[$table] = [];
    while ($col = $col_res->fetch_assoc()) {
        $live_schema[$table][$col['Field']] = $col['Type'];
    }
}

// 2. Parse 20121031100537_create_base.php
$base_content = file_get_contents(__DIR__ . '/../application/database/migrations/20121031100537_create_base.php');
preg_match_all('/CREATE TABLE `([^`]+)` \((.*?)\) ENGINE=/s', $base_content, $matches, PREG_SET_ORDER);

$base_schema = [];
foreach ($matches as $match) {
    $tbl = $match[1];
    $block = $match[2];
    $base_schema[$tbl] = [];
    preg_match_all('/^\s*`([^`]+)`\s+([a-zA-Z0-9_\(\)]+)/m', $block, $col_matches);
    foreach ($col_matches[1] as $idx => $colName) {
        $base_schema[$tbl][$colName] = $col_matches[2][$idx];
    }
}

// 3. Parse dados_steos.sql
$dump_content = file_get_contents(__DIR__ . '/../dados_banco_steos/dados_steos.sql');
preg_match_all('/CREATE TABLE `([^`]+)` \((.*?)\) ENGINE=/s', $dump_content, $dump_matches, PREG_SET_ORDER);

$dump_schema = [];
foreach ($dump_matches as $match) {
    $tbl = $match[1];
    $block = $match[2];
    $dump_schema[$tbl] = [];
    preg_match_all('/^\s*`([^`]+)`\s+([a-zA-Z0-9_\(\)]+)/m', $block, $col_matches);
    foreach ($col_matches[1] as $idx => $colName) {
        $dump_schema[$tbl][$colName] = $col_matches[2][$idx];
    }
}

echo "=== TABELAS EXCLUSIVAS OU FALTANTES ===\n";
echo "Tabelas no BD Ao Vivo (Total " . count($live_schema) . "):\n";
$all_tables = array_unique(array_merge(array_keys($live_schema), array_keys($base_schema), array_keys($dump_schema)));
sort($all_tables);

foreach ($all_tables as $tbl) {
    if ($tbl === 'migrations') continue;
    $in_live = isset($live_schema[$tbl]) ? "SIM (" . count($live_schema[$tbl]) . " cols)" : "NÃO";
    $in_base = isset($base_schema[$tbl]) ? "SIM (" . count($base_schema[$tbl]) . " cols)" : "NÃO";
    $in_dump = isset($dump_schema[$tbl]) ? "SIM (" . count($dump_schema[$tbl]) . " cols)" : "NÃO";
    
    if ($in_live !== $in_base || $in_live !== $in_dump || (isset($live_schema[$tbl]) && isset($base_schema[$tbl]) && count($live_schema[$tbl]) !== count($base_schema[$tbl]))) {
        echo sprintf(" - %-26s | BD Ao Vivo: %-12s | create_base.php: %-12s | dados_steos.sql: %-12s\n", $tbl, $in_live, $in_base, $in_dump);
    }
}

echo "\n=== DETALHAMENTO DE COLUNAS FALTANTES EM `create_base.php` COMPARADO AO BD AO VIVO ===\n";
foreach ($live_schema as $tbl => $live_cols) {
    if ($tbl === 'migrations') continue;
    if (!isset($base_schema[$tbl])) {
        echo " [TABELA COMPLETA FALTANDO EM create_base.php]: `$tbl`\n";
        foreach ($live_cols as $c => $t) {
            echo "    + `$c` ($t)\n";
        }
        continue;
    }
    $missing_in_base = array_diff(array_keys($live_cols), array_keys($base_schema[$tbl]));
    if (!empty($missing_in_base)) {
        echo " [TABELA `$tbl`] Colunas no BD ao vivo mas FALTANDO em `create_base.php`:\n";
        foreach ($missing_in_base as $mc) {
            echo "    + `$mc` (" . $live_cols[$mc] . ")\n";
        }
    }
}

echo "\n=== DETALHAMENTO DE COLUNAS EM `dados_steos.sql` COMPARADO AO BD AO VIVO ===\n";
foreach ($live_schema as $tbl => $live_cols) {
    if ($tbl === 'migrations') continue;
    if (!isset($dump_schema[$tbl])) {
        echo " [TABELA COMPLETA FALTANDO EM dados_steos.sql]: `$tbl`\n";
        continue;
    }
    $missing_in_dump = array_diff(array_keys($live_cols), array_keys($dump_schema[$tbl]));
    if (!empty($missing_in_dump)) {
        echo " [TABELA `$tbl`] Colunas no BD ao vivo mas FALTANDO em `dados_steos.sql`:\n";
        foreach ($missing_in_dump as $mc) {
            echo "    + `$mc` (" . $live_cols[$mc] . ")\n";
        }
    }
}

$conn->close();
?>
