# Documentação: Asaas.php (Payment Gateway Library)

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\libraries\Gateways\Asaas.php`
- **Tipo:** Gateway Library (`BasePaymentGateway`)
- **SDK / Helper:** `CodePhix\Asaas\Asaas` + `asaas_api_request()` (`asaas_helper.php`)

## 2. Funcionalidades Principais
- **Cobranças e Clientes:** `criarOuRetornarClienteAsaasId()`, `gerarCobranca()`, `atualizarDados()` (chama `\Asaas_webhook::executarBaixaEConciliacao`), `confirmarPagamento()`, `cancelar()`, `getPixQrCode($chargeId)`.
- **NFS-e Portal Nacional & Municipal:**
  - `emitirNfse($dadosPayload)` (`POST /v3/invoices`)
  - `consultarNfse($invoiceId)` (`GET /v3/invoices/{id}`)
  - `cancelarNfse($invoiceId, $cancelOnlyOnAsaas = false)` (`POST /v3/invoices/{id}/cancel`, enviando `{}` ou `{"cancelOnlyOnAsaas": true}`)
  - `obterFiscalInfo()` (`GET /v3/fiscalInfo` — detecta `useNationalPortal`)
  - `listarNfse(array $params = [])` (`GET /v3/invoices`)
  - `obterServicosMunicipais($description = null)` (apenas contas ABRASF sem Portal Nacional)
  - `obterConfiguracoesNfse()`: retorna defaults `municipal_service_code`, `codigo_nbs`, `aliquota_iss`, `tax_situation_code`, `tax_classification_code`, `operation_indicator_code`.
