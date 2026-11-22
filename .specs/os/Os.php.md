# Documentação: Os.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\controllers\Os.php`
- **Views Principais:** `os.php`, `adicionarOs.php`, `editarOs.php`, `visualizarOs.php`, `imprimirOs.php`, `imprimirOsTermica.php`, `adicionarChecklist.php`, `visualizarChecklist.php`, `emails/os.php`
- **Models:** `Os_model`, `Tecnicos_os_model`, `Equipamentos_os_model`, `Servicos_nfse_model`, `Clientes_model`

## 2. Fluxos Críticos de Negócio
- **Gestão e Filtros (`gerenciar`):** filtros separados de abertura (`data`, `data2`) e fechamento (`data_fech_de`, `data_fech_ate` sobre `os.dataFinal`), técnico, local, tipo (`Avulso`/`Contrato`) e preventivas (`manPrevnt`).
- **O.S. Vinculada a Contrato & Preventivas:** colunas `contratos_id`, `manutPreventiva`, `prioridade` (Select2 colorido) e `classificacao`; vinculação dinâmica de técnicos (`tecnicos_os`) e equipamentos (`equipamentos_os`).
- **Assinatura & Conclusão (`assinar`):** valida `((int)$dataOs->produtos_subTotal == 0 && (int)$dataOs->servicos_subTotal == 0)` antes de marcar `faturar = 1` / `Finalizado` e grava timestamp de `dataFinal`.
- **Checklists Digitais v2:** criação/visualização/impressão via `os_checklists` e `os_checklists_itens` com assinaturas Base64 em Canvas.
- **Emissão de NFS-e Asaas (`get_cobrancas_nfse`, `emitir_nfse`, `consultar_nfse`, `cancelar_nfse`):**
  - Detecta `useNationalPortal` em `Asaas::obterFiscalInfo()`: quando verdadeiro, envia `municipalServiceId = null`, `municipalServiceCode = null` e `municipalServiceName` exato.
  - Sanitiza parâmetros IBS/CBS com `asaas_sanitize_fiscal_codes()` separando `federalServiceTaxCode` (`140101`/`010101`) de `operationIndicatorCode` (`""` ou um dos 33 códigos oficiais, como `010106` para `99.03`).
  - `cancelar_nfse()` envia `{}` via `Asaas::cancelarNfse()` tratando retorno síncrono (`CANCELED`) e assíncrono (`PROCESSING_CANCELLATION` / `CANCELLATION_DENIED`), além de fallback via `consultarNfse()`.
