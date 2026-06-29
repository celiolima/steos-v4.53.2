<?php
$pdo = new PDO('mysql:host=steosMySql;dbname=steos', 'steos', 'steos');
$idOs = 2259;
$os = $pdo->query("SELECT * FROM os WHERE idOs = $idOs")->fetch(PDO::FETCH_OBJ);
$idContrato = $os->contratos_id;

$sql = "SELECT sc.*, s.nome FROM sistemas_contratos sc JOIN sistemas s ON s.idSistemas = sc.sistemas_id WHERE sc.contratos_id = $idContrato";
$sistemas_contrato = $pdo->query($sql)->fetchAll(PDO::FETCH_OBJ);

$matriz = [];
foreach($sistemas_contrato as $sc){
    $nomeSistema = trim(strtoupper($sc->nome));
    if(!isset($matriz[$nomeSistema])){
        $matriz[$nomeSistema] = [
            'locais' => [],
            'checks' => []
        ];
        $checksBase = $pdo->query("SELECT * FROM sistemas_checks WHERE sistemas_id = " . $sc->sistemas_id)->fetchAll(PDO::FETCH_OBJ);
        foreach($checksBase as $cb){
            $matriz[$nomeSistema]['checks'][] = $cb->descricao;
        }
    }
    $matriz[$nomeSistema]['locais'][] = $sc->local ? strtoupper($sc->local) : 'LOCAL NÃO DEFINIDO';
}

print_r($matriz);
