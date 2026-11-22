# Documentação: Servicos_nfse.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\controllers\Servicos_nfse.php`
- **Model:** `Servicos_nfse_model.php` (`tabela servicos_nfse`)
- **Views:** `servicos_nfse.php`, `adicionarServicoNfse.php`, `editarServicoNfse.php`

## 2. Responsabilidades e Regras Fiscais (Portal Nacional + Reforma Tributária IBS/CBS)
- CRUD controlado pelas permissões `vServico`, `aServico`, `eServico`, `dServico` e endpoint JSON `getAllJson()`.
- `buildServicoNfseDataFromPost()`: centraliza tratamento dos campos `codigo_servico_municipal` (LC 116), `codigo_nbs`, `codigo_tributacao_nacional` (`cTribNac`), `cst_ibs_cbs` (padrão `000`), `class_trib_ibs_cbs` (padrão `000001`) e `ind_operacao` (`cIndOp`).
- Usa `asaas_sanitize_fiscal_codes()` (`asaas_helper.php`) para impedir gravação de códigos `cTribNac` (como `140101`) em `ind_operacao` (aceita apenas vazio ou um dos 33 códigos oficiais do endpoint `/v3/fiscalInfo/operationIndicatorCodes`, incluindo `010106` para Locação `99.03`).
- `parseAliquota()`: converte texto percentual (vírgula ou ponto) sem erro de multiplicação por 100.
- `resolverAsaasServiceId()`: preenche `asaas_service_id = null` quando a conta Asaas opera via Portal Nacional (`useNationalPortal = 1`).
