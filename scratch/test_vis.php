<?php
$pdo = new PDO('mysql:host=steosMySql;dbname=steos', 'steos', 'steos');
$idOs = 2258;
$result = $pdo->query("SELECT os.*, c.nomeCliente, u.nome FROM os JOIN clientes c ON c.idClientes = os.clientes_id JOIN usuarios u ON u.idUsuarios = os.usuarios_id WHERE idOs = $idOs")->fetch(PDO::FETCH_OBJ);
$checklist = $pdo->query("SELECT * FROM os_checklists WHERE os_id = $idOs")->fetch(PDO::FETCH_OBJ);

echo "Checklist ID: " . $checklist->idChecklist . "\n";
echo "Title: CHECKLIST EQUIPAMENTO Nº " . sprintf('%04d', $checklist->idChecklist) . "\n";
echo "Tecnico: " . (!empty($checklist->nome_tecnico) ? $checklist->nome_tecnico : $result->nome) . "\n";
