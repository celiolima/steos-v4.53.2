# Documentação: ProdutosController.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\controllers\api\v1\ProdutosController.php`
- **Tipo:** REST Controller (`REST_Controller`)
- **Módulo:** `api`

## 2. Propósito
CRUD RESTful de `produtos` (`/api/v1/produtos` e `/api/v1/produtos/(:num)`) com controle de acesso `vProduto`, `aProduto`, `eProduto` e `dProduto`.

## 3. Observações Técnicas
- Busca `search` sanitizada com `escape_like_str()` repassada a `Produtos_model::get()`.
- Retorna `HTTP 404` quando o ID consultado não existe.
- Exclusão limpa vínculos em `produtos_os` e `itens_de_vendas` antes de remover de `produtos`.
