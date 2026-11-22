# Documentação: NfseController.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\controllers\api\v1\NfseController.php`
- **Tipo:** REST Controller (`REST_Controller`)
- **Módulo:** `api`

## 2. Propósito
Orquestra emissão, consulta, listagem e cancelamento de NFS-e via Asaas v3 respeitando o **Portal Nacional (`useNationalPortal = 1`)** e a **Reforma Tributária (IBS/CBS)**.

## 3. Endpoints
- `GET /api/v1/nfse` e `GET /api/v1/nfse/{idOs}`: listagem paginada ou detalhes da NFS-e da O.S. (`vLancamento` ou `vOs`).
- `GET /api/v1/nfse/servicos`: retorna registros de `servicos_nfse` e os 33 códigos oficiais `asaas_operation_indicator_codes()` (`vServico`).
- `POST /api/v1/nfse/emitir/{idOs}`: emite/agenda NFS-e usando `asaas_sanitize_fiscal_codes()` (separando `federalServiceTaxCode` de `operationIndicatorCode`), `nbsCode` e `taxSituationCode`/`taxClassificationCode` (`eOs`).
- `GET /api/v1/nfse/consultar/{idOs}`: sincroniza status/PDF/XML na O.S.
- `POST /api/v1/nfse/cancelar/{idOs}`: envia cancelamento (`POST /v3/invoices/{id}/cancel`, suportando `cancelOnlyOnAsaas`) tratando `PROCESSING_CANCELLATION`, `CANCELED` e `CANCELLATION_DENIED` (`eOs`).
