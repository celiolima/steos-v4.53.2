# Documentação: asaas_helper.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\helpers\asaas_helper.php`
- **Tipo:** Helper (`CI`)
- **Módulo:** `nfse` / `cobrancas`

## 2. Funções Principais
- `asaas_api_request($endpoint, $method = 'GET', $data = null, $apiKey = null, $isProduction = null)`:
  - Comunicação HTTP cURL nativa com `https://api.asaas.com` (Produção) ou `https://api-sandbox.asaas.com` (Sandbox).
  - Para `POST` e `PUT`, mesmo com `$data === null`, envia corpo JSON `'{}'` com header `Content-Length` explícito (exigido pelo endpoint `/cancel` do Asaas).
  - Decodifica retorno como `stdClass` (`json_decode($response)` sem `true`) e lança `\Exception` com mensagem detalhada em HTTP `>= 400`.
- `asaas_operation_indicator_codes()`:
  - Retorna os **33 códigos oficiais** de `operationIndicatorCode` (`cIndOp`) conforme `GET /v3/fiscalInfo/operationIndicatorCodes` (ex.: `010106` para Locação `99.03`).
- `asaas_default_federal_service_tax_code($municipalServiceCodeRaw)`:
  - Infere `140101` (`14.01`), `010101` (`0101`/`01.01`) ou `''` (`99.03`).
- `asaas_sanitize_fiscal_codes($municipalServiceCodeRaw, $operationIndicatorCode, $federalServiceTaxCode)`:
  - Evita erro HTTP `400` (`Código indicador de operação inválido`) movendo códigos `cTribNac` de 6 dígitos (como `140101`) para `federalServiceTaxCode` quando digitados em `operationIndicatorCode`, validando contra os 33 códigos oficiais e aplicando `010106` para `99.03`.
