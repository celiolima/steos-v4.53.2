# Documentação: VendasController.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\controllers\api\v1\VendasController.php`
- **Tipo:** REST Controller (`REST_Controller`)
- **Módulo:** `api`

## 2. Propósito
Consulta RESTful de vendas (`vendas`) e seus itens/cobranças vinculadas com exigência de permissão `vVenda`.

## 3. Endpoints
- `GET /api/v1/vendas`: listagem paginada com filtros `search`, `status`, `from` e `to`, calculando `totalProdutos`.
- `GET /api/v1/vendas/{id}`: detalhamento da venda (sem `senha` do cliente) com `produtos` e `cobrancas`.
