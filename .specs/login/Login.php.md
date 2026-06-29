# Documentação: Login.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\controllers\Login.php`
- **Tipo:** Controller
- **Módulo:** login

## 2. Propósito
Responsável pelo processamento e lógica pertinente à camada de Controller do módulo de login.

## 3. Dependências
- **Views Carregadas:**
  - `steos/login`
- **Models Carregados:**
  - `steos_model`
  - `Steos_model`

## 4. Estrutura Interna
### Método: `__construct`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `__construct`.

### Método: `index`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `index`.

### Método: `sair`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `sair`.

### Método: `verificarLogin`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `verificarLogin`.

### Método: `chk_date`
- **Parâmetros:** `$data_banco`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `chk_date`.


## 5. Fluxo de Dados
Recebe a requisição HTTP (GET/POST), valida via `form_validation`, delega a leitura/escrita para o respectivo Model, e encaminha os resultados em array `$data` para a View correspondente.

## 6. Rotas Relacionadas
Mapeamento automático via CodeIgniter: `site_url('login/metodo')`.

## 7. Observações e Regras de Negócio
- Documentação autogerada para reconstrução.
- Certifique-se de manter os padrões do CodeIgniter 3.
