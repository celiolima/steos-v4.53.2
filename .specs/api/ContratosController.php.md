# Documentação: ContratosController.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\controllers\api\v1\ContratosController.php`
- **Tipo:** REST Controller (`REST_Controller`)
- **Módulo:** `api`

## 2. Propósito
Exposição RESTful do módulo de Contratos (`/api/v1/contratos` e `/api/v1/contratos/(:num)`).

## 3. Endpoints & Segurança ACL
- `GET /api/v1/contratos`: listagem paginada via `Contratos_model::getContratos()` com filtros `search`, `contrato`, `nome`, `status` e `clientes_id`. Exige permissão `vContrato` (`HTTP 401` caso negada).
- `GET /api/v1/contratos/{id}`: detalhes completos do contrato contendo `sistemas` (com `checks` sincronizados), `os`, `vendas`, `cobrancas` e `anexos`. Exige permissão `vContrato`.
