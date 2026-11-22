# Documentação: editarOs.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\views\os\editarOs.php`
- **Tipo:** View
- **Módulo:** `os`

## 2. Estrutura de Abas & Ações
- **Botões Superiores:** Faturar, Preencher Checklist (se `manutPreventiva == 1`), Visualizar, Papel A4, Cupom Não Fiscal, WhatsApp, E-mail, Garantia, Nova O.S. e botão dinâmico de **NFS-e** (refletindo status `SCHEDULED`, `SYNCHRONIZED`, `PROCESSING`, `PROCESSING_CANCELLATION`, `AUTHORIZED`, `ERROR` e `CANCELLATION_DENIED`).
- **Abas:** Detalhes da O.S., Desconto, Produtos, Serviços, Anexos, Anotações, Assinatura, **Faturas (`#tab8`)** e **Notas (`#tab9`)** (exibição dinâmica condicional).
- **Modal NFS-e (`#modal-nfse`):**
  - Carrega serviços cadastrados (`servicos_nfse`) e cobranças Asaas vinculadas via AJAX (`os/get_cobrancas_nfse`).
  - Preenche automaticamente `municipal_service_code`, `codigo_nbs`, `federal_service_tax_code` (`cTribNac`), `aliquota_iss`, `tax_situation_code` (`CST IBS/CBS`), `tax_classification_code` (`cClassTrib`) e seleciona `operation_indicator_code` no `<select>` com os 33 códigos oficiais do Asaas.
  - Chamadas AJAX POST (`cancelar_nfse`, etc.) enviam `data: { idOs: idOs }` com token CSRF via `assets/js/csrf.js` e exibem erro detalhado caso haja expiração de sessão (`HTTP 403`).
- **Permissões Granulares NFS-e na O.S.:** Botão `#btn-nfse`, aba `#tabNotas` e atualização (`#btn-consultar-nfse`/`.btn-consultar-nfse-tab`) aceitam `vNfse || aNfse || eNfse || dNfse`; emissão (`#btn-submit-nfse`) exige `aNfse`; cancelamento (`#btn-cancelar-nfse`/`.btn-cancelar-nfse-tab`) exige `dNfse`.
- **Plugins & Scripts:** jQuery Datetimepicker, jQuery UI Autocomplete, Trumbowyg, jQuery Signature, Select2 customizado (`formatColor` para prioridade), MaskMoney e SweetAlert2.
