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
            : 'https://api-sandbox.asaas.com';

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

if (! function_exists('asaas_operation_indicator_codes')) {
    /**
     * Retorna a tabela oficial de códigos Indicadores de Operação (cIndOp) da API v3 do Asaas
     * (/v3/fiscalInfo/operationIndicatorCodes) para Reforma Tributária (IBS/CBS).
     */
    function asaas_operation_indicator_codes()
    {
        return [
            '010101' => 'Presencial, com retirada no estabelecimento do fornecedor',
            '010102' => 'Presencial, com retirada fora do estabelecimento do fornecedor',
            '010103' => 'Não presencial, com entrega ou disponibilização em endereço fornecido',
            '010104' => 'Licitação promovida pelo poder público de bem apreendido ou abandonado ou leilao judicial',
            '010105' => 'Constatação de irregularidade pela falta de documentação fiscal ou pelo acobertamento por documentação inidôneo',
            '010106' => 'Locação de bem móvel material em aquisições realizadas de forma centralizada por contribuinte sujeito ao regime regular do IBS e da CBS que possui mais de um estabelecimento e que não estejam sujeitas a vedação à apropriação de créditos',
            '010201' => 'Entrega ou disponibilização do bem',
            '020101' => 'Execução de operações com bem imóvel, bem imaterial, inclusive direito, relacionado a bem imóvel',
            '020201' => 'Execução de serviços sobre bem imóvel',
            '020301' => 'Execução dos serviços de administração e intermediação de bens imóveis',
            '030101' => 'Execução de serviços diversos exclusivamente prestados fisicamente sobre a pessoa ou integralmente fruídos presencialmente por pessoa física',
            '030102' => 'Execução de serviços diversos exclusivamente prestados fisicamente sobre a pessoa ou integralmente fruídos presencialmente por pessoa física',
            '030103' => 'Execução de serviços diversos exclusivamente prestados fisicamente sobre a pessoa ou integralmente fruídos presencialmente por pessoa física',
            '030104' => 'Execução de serviços diversos exclusivamente prestados fisicamente sobre a pessoa ou integralmente fruídos presencialmente por pessoa física',
            '040101' => 'Execução de serviços de planejamento, organização e administração de feiras, exposições, congressos, espetáculos, exibições e congêneres',
            '050101' => 'Execução de serviços diversos fisicamente prestados sobre bem móvel material',
            '050102' => 'Execução de serviços diversos fisicamente prestados sobre bem móvel material',
            '050103' => 'Execução de serviços diversos fisicamente prestados sobre bem móvel material',
            '050104' => 'Execução de serviços diversos fisicamente prestados sobre bem móvel material',
            '050201' => 'Execução de serviços portuários',
            '060101' => 'Execução de serviços de transporte de passageiros',
            '070101' => 'Execução de serviços de transporte de carga',
            '070102' => 'Execução de serviços de transporte de carga',
            '080101' => 'Execução de serviços de exploração de via',
            '100101' => 'Execução de operações de cessão de espaço para prestação de serviços publicitários',
            '100102' => 'Execução de operações de cessão de espaço para prestação de serviços publicitários',
            '100201' => 'Execução de operações de cessão de espaço para prestação de serviços publicitários',
            '100301' => 'Execução dos demais serviços em operações não especificadas anteriormente ou, nos serviços de que trata o inc. III, estes sejam, ainda que parcialmente, prestados à distância (2)',
            '100302' => 'Execução dos demais serviços em operações não especificadas anteriormente ou, nos serviços de que trata o inc. III, estes sejam, ainda que parcialmente, prestados à distância (2)',
            '100401' => 'Execução dos demais serviços em operações não especificadas anteriormente ou, nos serviços de que trata o inc. III, estes sejam, ainda que parcialmente, prestados à distância',
            '100501' => 'Execução de demais operações não especificadas anteriormente com bens móveis imateriais, inclusive direitos',
            '100502' => 'Execução de demais operações não especificadas anteriormente com bens móveis imateriais, inclusive direitos',
            '100601' => 'Execução de demais operações não especificadas anteriormente com bens móveis imateriais, inclusive direitos',
        ];
    }
}

if (! function_exists('asaas_default_federal_service_tax_code')) {
    /**
     * Infere o Código de Tributação Nacional (cTribNac / federalServiceTaxCode) padrão
     * a partir do código de serviço municipal (LC 116/2003). Locação (99.03) não utiliza.
     */
    function asaas_default_federal_service_tax_code($municipalServiceCodeRaw)
    {
        $raw = (string) $municipalServiceCodeRaw;
        if (strpos($raw, '99.03') !== false) {
            return '';
        }
        if (preg_match('/(?:^|[\s|.])14\.?01(?:$|[\s|-])/', $raw)) {
            return '140101';
        }
        if (preg_match('/(?:^|[\s|.])01\.?01(?:$|[\s|-])/', $raw)) {
            return '010101';
        }

        return '';
    }
}

if (! function_exists('asaas_sanitize_fiscal_codes')) {
    /**
     * Valida e normaliza operationIndicatorCode (cIndOp) e federalServiceTaxCode (cTribNac).
     * Evita enviar códigos de Tributação Nacional (ex: 140101) no campo operationIndicatorCode.
     */
    function asaas_sanitize_fiscal_codes($municipalServiceCodeRaw, $operationIndicatorCode = '', $federalServiceTaxCode = '')
    {
        $raw = trim((string) $municipalServiceCodeRaw);
        $opCode = trim((string) $operationIndicatorCode);
        $fedCode = trim((string) $federalServiceTaxCode);
        $isLocacao9903 = strpos($raw, '99.03') !== false;
        $validOpCodes = asaas_operation_indicator_codes();

        if ($opCode !== '' && ! isset($validOpCodes[$opCode])) {
            if (! $isLocacao9903 && $fedCode === '' && preg_match('/^\d{6}$/', $opCode)) {
                $fedCode = $opCode;
            }
            $opCode = $isLocacao9903 ? '010106' : '';
        }

        if ($opCode === '' && $isLocacao9903) {
            $opCode = '010106';
        }

        if ($fedCode === '') {
            $fedCode = asaas_default_federal_service_tax_code($raw);
        }

        return [
            'operationIndicatorCode' => $opCode,
            'federalServiceTaxCode' => $fedCode,
        ];
    }
}
