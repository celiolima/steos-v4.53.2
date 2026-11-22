# Documentação: CobrancasController.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\controllers\api\v1\CobrancasController.php`
- **Tipo:** REST Controller (`REST_Controller`)
- **Módulo:** `api`

## 2. Propósito
Consulta administrativa de cobranças (`cobrancas`) e QR Code Pix via gateway Asaas.

## 3. Endpoints
- `GET /api/v1/cobrancas`: filtros `pesquisa`/`search`, `os_id`, `vendas_id`, `tipo`, `status`, `data_de`, `data_ate` (exige permissão `vCobranca`).
- `GET /api/v1/cobrancas/{id}`: detalhamento da cobrança com remoção do campo `senha` do cliente.
- `GET /api/v1/cobrancas/{id}/pix`: consulta QR Code Pix dinâmico via `Asaas::getPixQrCode($charge_id)`.
