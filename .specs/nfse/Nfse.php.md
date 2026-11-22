# Documentação: Nfse.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\controllers\Nfse.php`
- **View Associada:** `application/views/nfse/nfse.php`
- **Tipo:** Controller (`MY_Controller`)
- **Módulo:** `nfse`

## 2. Funcionalidades e Regras de Negócio
- Exige permissão `vNfse` para listagem paginada. Na view `nfse.php`, botões de atualização exigem `v/a/e/dNfse` e cancelamento exige `dNfse`.
- `listar()`: consulta tabela `os` com `asaas_invoice_status` preenchido, aplicando filtros por `pesquisa` (cliente, documento, número NFS-e ou `idOs`), `status` e período (`data_de` / `data_ate`), exibindo link PDF, XML, atualização (`os/consultar_nfse`) e cancelamento (`os/cancelar_nfse`).
- **Status suportados na interface:** `SCHEDULED`, `SYNCHRONIZED`, `PROCESSING`, `AUTHORIZED`, `PROCESSING_CANCELLATION`, `CANCELED`/`CANCELLED`, `CANCELLATION_DENIED` e `ERROR`.
- **Emissão / Consulta / Cancelamento:** centralizados em `Os.php` (`emitir_nfse`, `consultar_nfse`, `cancelar_nfse`) via `Gateways/Asaas.php` e webhooks `INVOICE_*` em `Asaas_webhook.php`.
