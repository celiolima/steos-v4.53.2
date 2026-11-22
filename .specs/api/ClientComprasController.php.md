# Documentação: ClientComprasController.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\controllers\api\v1\client\ClientComprasController.php`
- **Tipo:** REST Controller (`REST_Controller`)
- **Módulo:** `api` (Portal do Cliente)

## 2. Propósito
Listagem e detalhamento de compras (`vendas`) para o cliente autenticado em `GET /api/v1/client/compras(/id)` com QR Code Pix calculado via `Vendas_model::getQrCode()`.

## 3. Segurança Multi-Tenant
- Validação rigorosa de propriedade `(int) $result->clientes_id === (int) $clientLogged->usuario->idClientes` (`HTTP 403` contra IDOR) e paginação via query param `page`.
