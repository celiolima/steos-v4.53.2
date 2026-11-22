# Documentação: ClassificacaoFinanceira.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\controllers\ClassificacaoFinanceira.php`
- **Tipo:** Controller (`MY_Controller`)
- **Módulo:** `financeiro`
- **Rota Linux Case-Sensitive:** `classificacaofinanceira` -> `ClassificacaoFinanceira` em `application/config/routes.php`.

## 2. Propósito e Regras
- Gerencia categorias de classificação financeira (`classificacao_financeira`) associadas a um grupo financeiro (`nomeClassFin`, `grupoFinaceiro`).
- Valida `vLancamento`, `aLancamento`, `eLancamento` e `dLancamento`.
- Atualiza e exclui usando PK `idClassFin`.
