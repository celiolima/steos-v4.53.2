<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Helper Sênior para comunicação nativa com a API v3 do Asaas.
 * Libera o STEOS de wrappers legados e garante acesso total a todos os endpoints v3 (Boletos, Pix, Cartão, Webhooks, Assinaturas, NFS-e).
 */
if (! function_exists('asaas_api_request')) {
    function asaas_api_request($endpoint, $method = 'GET', $data = null, $apiKey = null, $isProduction = null)
    {
        $ci = &get_instance();
        if ($apiKey === null || $isProduction === null) {
            $ci->load->config('payment_gateways');
            $asaasConfig = $ci->config->item('payment_gateways')['Asaas'] ?? [];
            if ($apiKey === null) {
                $apiKey = $asaasConfig['credentials']['api_key'] ?? '';
            }
            if ($isProduction === null) {
                $isProduction = !empty($asaasConfig['production']) && $asaasConfig['production'] === true;
            }
        }

        if (empty($apiKey)) {
            throw new \Exception('Chave de API (api_key) do Asaas não configurada em Config/payment_gateways.php');
        }

        $baseUrl = $isProduction 
            ? 'https://api.asaas.com' 
            : 'https://sandbox.asaas.com/api';

        // Garante que o endpoint comece com a barra se não tiver
        if ($endpoint[0] !== '/') {
            $endpoint = '/' . $endpoint;
        }

        $url = $baseUrl . $endpoint;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_TIMEOUT, 45);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);

        $headers = [
            'access_token: ' . $apiKey,
            'Content-Type: application/json',
            'User-Agent: Steos-Sistema/4.53.2'
        ];

        if ($data !== null && in_array(strtoupper($method), ['POST', 'PUT'])) {
            $payload = is_string($data) ? $data : json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            $headers[] = 'Content-Length: ' . strlen($payload);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Em ambientes locais/Docker de homologação
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err) {
            throw new \Exception('Erro cURL de conexão com Asaas: ' . $err);
        }

        $decoded = json_decode($response);

        if ($httpCode >= 400 || (isset($decoded->errors) && !empty($decoded->errors))) {
            $errorMsg = 'Erro na API Asaas (' . $httpCode . ')';
            if (isset($decoded->errors[0]->description)) {
                $errorMsg .= ': ' . $decoded->errors[0]->description;
            } elseif (is_string($response) && !empty($response)) {
                $errorMsg .= ': ' . substr($response, 0, 200);
            }
            throw new \Exception($errorMsg);
        }

        return $decoded;
    }
}
