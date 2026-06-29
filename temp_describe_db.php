<?php
$env = file_get_contents('.env');
preg_match('/DB_USERNAME=(.*)/', $env, $u);
preg_match('/DB_PASSWORD=(.*)/', $env, $p);
preg_match('/DB_DATABASE=(.*)/', $env, $d);

$conn = new mysqli('127.0.0.1', trim($u[1]), trim($p[1]), trim($d[1]));
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("DESCRIBE contratos");
$rows = [];
while($row = $result->fetch_assoc()) {
    $rows[] = $row;
}
echo json_encode($rows, JSON_PRETTY_PRINT);
$conn->close();
?>
