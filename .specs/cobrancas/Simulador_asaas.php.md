# Documentação: Simulador_asaas.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\controllers\Simulador_asaas.php`
- **Tipo:** Controller (`MY_Controller`)
- **Módulo:** `cobrancas`

## 2. Propósito
Ferramenta interna de homologação em ambiente local/Sandbox para disparar eventos simulados (`PAYMENT_CREATED` e `INVOICE_AUTHORIZED`) via `POST` para `asaas_webhook/receber` enviando o header `asaas-access-token` configurado.

> Uso exclusivo em desenvolvimento/homologação (`MY_Controller` exige login autenticado).
