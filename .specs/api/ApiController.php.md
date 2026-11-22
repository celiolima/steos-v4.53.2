# Documentação: ApiController.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\controllers\api\v1\ApiController.php`
- **Tipo:** REST Controller (`REST_Controller`)
- **Módulo:** `api`

## 2. Propósito
Disponibiliza indicadores do painel inicial, calendário operacional de O.S. (com cores de manutenção preventiva), dados do emitente e logs de auditoria via API v1.

## 3. Endpoints e Métodos
- `index_get()` (`GET /api/v1`): contadores globais (`os`, `clientes`, `produtos`, `servicos`, `garantias`, `vendas`, `contratos`, `equipamentos`, `veiculos`, `tecnicos`, `cobrancas`) e alertas (`osAbertas`, `osAndamento`, `estoqueBaixo`) quando `vOs` está liberada.
- `calendario_get()` (`GET /api/v1/calendario`): consulta via `Steos_model::calendario($start, $end, $status, $tecnicos)`, retornando `events` compatíveis com FullCalendar (incluindo `A Sair | Aguard Conclusão` em verde `#00a65a` ou dourado `#AEB404` quando `manutPreventiva == 1`) e `allOs`.
- `emitente_get()` (`GET /api/v1/emitente`): dados do emitente e `appName`.
- `audit_get()` (`GET /api/v1/audit`): listagem paginada da tabela `logs` (exige permissão `cAuditoria`).
