# Controller `Servicos_nfse.php`

## Objetivo
Auxiliar na categorização e validação das exigências tributárias municipais relacionadas aos serviços prestados, suportando a emissão da Nota Fiscal de Serviço (`Nfse.php`).

## Responsabilidades
- Manutenção da lista de códigos de serviços LC 116/03 e CNAE aplicáveis.
- Validação das alíquotas de ISS, retenções (IR, INSS, CSLL, COFINS, PIS) antes da transmissão do lote para o webservice da prefeitura.
