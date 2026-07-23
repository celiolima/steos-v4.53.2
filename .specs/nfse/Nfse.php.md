# Controller `Nfse.php`

## Objetivo
Módulo responsável pela orquestração e emissão de Notas Fiscais de Serviço Eletrônica (NFSe).

## Funcionalidades Principais
- Integração com gateways de prefeituras (quando aplicável).
- Recebimento de ordens e faturamentos fechados, preparando o lote de emissão.
- Armazenamento de XML/PDF da nota emitida atrelada ao Cliente/Contrato.

## Relacionamentos MVC
- Relaciona-se intimamente com o fechamento do Financeiro e `Servicos_nfse.php` para codificação municipal e tributária.
