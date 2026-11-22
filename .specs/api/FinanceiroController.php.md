# Documentação: FinanceiroController.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\controllers\api\v1\FinanceiroController.php`
- **Tipo:** REST Controller (`REST_Controller`)
- **Módulo:** `api`

## 2. Propósito
Exposição RESTful somente leitura dos lançamentos financeiros (`lancamentos`) e estatísticas consolidadas (`vLancamento`).

## 3. Endpoints
- `GET /api/v1/financeiro`: listagem paginada com filtros por `tipo`, `baixado`, `vencimento_de`, `vencimento_ate`, `centro_de_gastos`, `classificacao_fin`, `grupo_finaceiro` e `forma_pgto`.
- `GET /api/v1/financeiro/{id}`: detalhamento do lançamento com `anexos` (`Financeiro_model::getAnexos`).
- `GET /api/v1/financeiro/resumo`: totais de receitas/despesas realizadas e pendentes via `Financeiro_model::getEstatisticasFinanceiro2()`.
