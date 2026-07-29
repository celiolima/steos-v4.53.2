<?php
$env = parse_ini_file('.env_dev'); // or however the env is loaded
$apiKey = $env['ASAAS_API_KEY'] ?? 'production_key';
// But I can just read it directly if I know it. Let's just read the .env file.
$envFile = file_exists('.env') ? '.env' : '.env_dev';
$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$apiKey = '';
foreach ($lines as $line) {
    if (strpos($line, 'ASAAS_API_KEY') === 0) {
        $apiKey = trim(explode('=', $line, 2)[1]);
        $apiKey = trim($apiKey, '"\'');
        break;
    }
}

$url = "https://api.asaas.com/v3/invoices/municipalServices?description=14.01";
if (strpos($apiKey, '$aact_') !== false) {
    // sandbox
    // wait, production key might start with $aact_ too.
    $url = "https://api.asaas.com/v3/invoices/municipalServices?description=14.01";
}

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'access_token: ' . $apiKey,
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
curl_close($ch);

echo $response;
