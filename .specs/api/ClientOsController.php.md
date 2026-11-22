# Documentação: ClientOsController.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\controllers\api\v1\client\ClientOsController.php`
- **Tipo:** REST Controller (`REST_Controller`)
- **Módulo:** `api` (Portal do Cliente)

## 2. Propósito
Permite ao cliente autenticado consultar últimas O.S./compras (`GET /api/v1/client`), listar/visualizar suas O.S. (`GET /api/v1/client/os(/id)`) e abrir novas O.S. (`POST /api/v1/client/os`).

## 3. Segurança Multi-Tenant
- `os_get($id)` valida explicitamente `(int) $os->clientes_id === (int) $clientLogged->usuario->idClientes` (retornando `HTTP 403` em acesso cruzado / IDOR) e remove dados internos de estoque e caminhos físicos de anexos.
- Paginação via parâmetros `perPage` e `page`.
