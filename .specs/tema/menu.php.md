# Documentação: menu.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\views\tema\menu.php`
- **Tipo:** View
- **Módulo:** tema

## 2. Propósito
Responsável pelo processamento e lógica pertinente à camada de View do módulo de tema.

## 3. Dependências
N/A

## 4. Estrutura Interna
- Renderiza a interface para o usuário.

## 5. Fluxo de Dados
Recebe o array `$data` do Controller e renderiza variáveis em HTML iterando sobre elas (ex: `foreach`). Envia dados de volta através de submissão de Forms ou chamadas AJAX.

## 6. Rotas Relacionadas
N/A

## 7. Observações e Regras de Negócio
- Renderiza os itens do menu lateral condicionados às chaves granulares de `Permission::checkPermission()`, incluindo `vOs`, `vContrato`, `vCobranca`, `vNfse` e `vLogsAsaas`.
- Sempre adicionar a respectiva verificação de permissão em novos itens de menu.
