# Documentação: editarPermissao.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\views\permissoes\editarPermissao.php`
- **Tipo:** View
- **Módulo:** permissoes

## 2. Propósito
Responsável pelo processamento e lógica pertinente à camada de View do módulo de permissoes.

## 3. Dependências
N/A

## 4. Estrutura Interna
- Carrega o mapa JSON salvo na tabela `permissoes` e preenche checkboxes para todas as **92 chaves granulares**.
- Restaura a renderização dos 4 checkboxes de `Pagamentos` (`vPagamento`, `aPagamento`, `ePagamento`, `dPagamento`) que haviam sido omitidos anteriormente, corrige erro de tag HTML `<label......>` e adiciona os novos blocos granulares (`Contas`, `CadFinanceiro`, `Comissões`, `Logs Asaas`, `Asaas Admin` e os 6 acordeões gerados em loop limpo).

## 5. Fluxo de Dados
Recebe o array `$data` do Controller e renderiza variáveis em HTML iterando sobre elas (ex: `foreach`). Envia dados de volta através de submissão de Forms ou chamadas AJAX.

## 6. Rotas Relacionadas
N/A

## 7. Observações e Regras de Negócio
- Documentação autogerada para reconstrução.
- Certifique-se de manter os padrões do CodeIgniter 3.
