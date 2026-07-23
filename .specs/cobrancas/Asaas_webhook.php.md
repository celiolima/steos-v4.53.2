# Controller `Asaas_webhook.php`

## Objetivo
Atua como um receptor passivo (Webhook endpoint) para as notificações assíncronas do Gateway de Pagamento Asaas V3.

## Funcionalidade
- A API do Asaas faz requisições POST para este controller informando alterações de status em faturas (Pagamento Confirmado, Vencido, Estornado, etc).
- O webhook processa o JSON recebido (validando eventuais cabeçalhos de segurança).
- Automaticamente **atualiza o banco de dados do STEOS**, cruzando o ID da cobrança recebida com o ID gravado no sistema, e realiza a baixa do contas a receber, quitando Ordens de Serviço ou Parcelas de Contrato, sem intervenção humana.

## Segurança
- Deve operar sem bloqueios de sessão, pois a requisição vem diretamente dos servidores do Asaas, mas deve possuir token de validação no cabeçalho ou payload.
