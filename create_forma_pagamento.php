<?php
$conn = new mysqli('steosMySql', 'steos', 'steos', 'steos');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->query("CREATE TABLE IF NOT EXISTS forma_pagamento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$conn->query("TRUNCATE TABLE forma_pagamento");

$formas = [
    'Dinheiro',
    'Pix',
    'Boleto',
    'Cartão de Crédito',
    'Cartão de Débito',
    'Cheque',
    'Cheque Pré-datado',
    'Depósito',
    'Transferência DOC',
    'Transferência TED',
    'Promissória'
];

foreach ($formas as $f) {
    $conn->query("INSERT INTO forma_pagamento (nome) VALUES ('$f')");
}

echo "Tabela forma_pagamento criada e populada.\n";
