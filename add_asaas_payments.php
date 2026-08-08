<?php
$conn = new mysqli('steosMySql', 'steos', 'steos', 'steos');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$formas = [
    'Boleto (Asaas)',
    'Pix / QR Code (Asaas)'
];

foreach ($formas as $f) {
    // Check if it exists
    $res = $conn->query("SELECT id FROM forma_pagamento WHERE nome = '$f'");
    if ($res->num_rows == 0) {
        $conn->query("INSERT INTO forma_pagamento (nome) VALUES ('$f')");
    }
}

echo "Adicionadas formas de pagamento do Asaas.\n";
