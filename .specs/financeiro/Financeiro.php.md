# Documentação: Financeiro.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\controllers\Financeiro.php`
- **Tipo:** Controller
- **Módulo:** financeiro

## 2. Propósito
Responsável pelo processamento e lógica pertinente à camada de Controller do módulo de financeiro.

## 3. Dependências
- **Models Carregados:**
  - `contas_model`
  - `os_model`
  - `gasolina_model`
  - `usuarios_model`
  - `financeiro_model`
  - `veiculos_model`
  - `classificacao_financeira_model`
  - `Os_model`
  - `lancamentos_contas_model`

## 4. Estrutura Interna
### Método: `__construct`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `__construct`.

### Método: `index`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `index`.

### Método: `lancamentos`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `lancamentos`.

### Método: `calendario`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `calendario`.

### Método: `adicionarReceita`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `adicionarReceita`.

### Método: `adicionarReceita_parc`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `adicionarReceita_parc`.

### Método: `adicionarDespesa`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `adicionarDespesa`.

### Método: `editar`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `editar`.

### Método: `excluirLancamento`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `excluirLancamento`.

### Método: `autoCompleteClienteFornecedor`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `autoCompleteClienteFornecedor`.

### Método: `autoCompleteClienteAddReceita`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `autoCompleteClienteAddReceita`.

### Método: `getThisYear`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getThisYear`.

### Método: `getThisWeek`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getThisWeek`.

### Método: `getLastSevenDays`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getLastSevenDays`.

### Método: `getThisMonth`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getThisMonth`.

### Método: `anexar`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `anexar`.

### Método: `excluirAnexo`
- **Parâmetros:** `$id = null`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `excluirAnexo`.

### Método: `downloadanexo`
- **Parâmetros:** `$id = null`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `downloadanexo`.

**Queries SQL:** Contem operacoes de banco de dados via Query Builder.


## 5. Fluxo de Dados
Recebe a requisição HTTP (GET/POST), valida via `form_validation`, delega a leitura/escrita para o respectivo Model, e encaminha os resultados em array `$data` para a View correspondente.

## 6. Rotas Relacionadas
Mapeamento automático via CodeIgniter: `site_url('financeiro/metodo')`.

## 7. Observações e Regras de Negócio
- Documentação autogerada para reconstrução.
- Certifique-se de manter os padrões do CodeIgniter 3.
