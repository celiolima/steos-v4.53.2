# Documentação: ClientContratosController.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\controllers\api\v1\client\ClientContratosController.php`
- **Tipo:** REST Controller (`REST_Controller`)
- **Módulo:** `api` (Portal do Cliente)

## 2. Propósito
Endpoint somente leitura para clientes autenticados consultarem seus contratos em `GET /api/v1/client/contratos` e `GET /api/v1/client/contratos/(:num)`.

## 3. Segurança Multi-Tenant
- Filtra listagem por `['clientes_id' => $clienteId]`, bloqueia IDs de outros clientes com `HTTP 403` e oculta `path` físico dos anexos antes de retornar `sistemas`, `os`, `vendas` e `cobrancas`.
