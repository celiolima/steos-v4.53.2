<?php
$conn = new mysqli('steosMySql', 'steos', 'steos', 'steos');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->query("TRUNCATE TABLE grupo_financeiro");
$grupos = ['FIXO INDIRETO', 'FIXO DIRETO', 'VARIAVEL DIRETO', 'VARIAVEL INDIRETO', 'RECEITA'];
foreach ($grupos as $g) {
    $conn->query("INSERT INTO grupo_financeiro (nome) VALUES ('$g')");
}
echo "Grupos inseridos.\\n";
function desc($conn, $table) {
    $res = $conn->query("DESCRIBE $table");
    if ($res) {
        echo "$table columns: ";
        while ($row = $res->fetch_assoc()) echo $row['Field'] . ", ";
        echo "\\n";
    }
}
desc($conn, 'bancos');
desc($conn, 'classificacao_financeira');
desc($conn, 'centro_gastos');
desc($conn, 'grupo_financeiro');
