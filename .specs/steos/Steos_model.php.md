# Documentação: Steos_model.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\models\Steos_model.php`
- **Tipo:** Model
- **Módulo:** steos

## 2. Propósito
Responsável pelo processamento e lógica pertinente à camada de Model do módulo de steos.

## 3. Dependências
Nenhuma dependência explícita via `$this->load`.

## 4. Estrutura Interna
### Método: `__construct`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `__construct`.

### Método: `get`
- **Parâmetros:** `$table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array'`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `get`.

### Método: `getById`
- **Parâmetros:** `$id`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getById`.

### Método: `alterarSenha`
- **Parâmetros:** `$senha`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `alterarSenha`.

### Método: `pesquisar`
- **Parâmetros:** `$termo`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `pesquisar`.

### Método: `add`
- **Parâmetros:** `$table, $data`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `add`.

### Método: `edit`
- **Parâmetros:** `$table, $data, $fieldID, $ID`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `edit`.

### Método: `delete`
- **Parâmetros:** `$table, $fieldID, $ID`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `delete`.

### Método: `count`
- **Parâmetros:** `$table`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `count`.

### Método: `getOsAbertas`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getOsAbertas`.

### Método: `getOsAguardandoPecas`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getOsAguardandoPecas`.

### Método: `getOsAndamento`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getOsAndamento`.

### Método: `calendario`
- **Parâmetros:** `$start, $end, $status = null, $tecnicos = null`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `calendario`.

### Método: `getProdutosMinimo`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getProdutosMinimo`.

### Método: `getOsEstatisticas`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getOsEstatisticas`.

### Método: `getEstatisticasFinanceiro`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getEstatisticasFinanceiro`.

### Método: `getEstatisticasFinanceiroMes`
- **Parâmetros:** `$year`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getEstatisticasFinanceiroMes`.

### Método: `getEstatisticasFinanceiroDia`
- **Parâmetros:** `$year`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getEstatisticasFinanceiroDia`.

### Método: `getEstatisticasFinanceiroMesInadimplencia`
- **Parâmetros:** `$year`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getEstatisticasFinanceiroMesInadimplencia`.

### Método: `getEmitente`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getEmitente`.

### Método: `addEmitente`
- **Parâmetros:** `$nome, $cnpj, $ie, $cep, $logradouro, $numero, $bairro, $cidade, $uf, $telefone, $email, $logo`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `addEmitente`.

### Método: `editEmitente`
- **Parâmetros:** `$id, $nome, $cnpj, $ie, $cep, $logradouro, $numero, $bairro, $cidade, $uf, $telefone, $email`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `editEmitente`.

### Método: `editLogo`
- **Parâmetros:** `$id, $logo`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `editLogo`.

### Método: `editImageUser`
- **Parâmetros:** `$id, $imageUserPath`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `editImageUser`.

### Método: `check_credentials`
- **Parâmetros:** `$email`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `check_credentials`.

### Método: `saveConfiguracao`
- **Parâmetros:** `$data`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `saveConfiguracao`.

**Queries SQL:** Contem operacoes de banco de dados via Query Builder.


## 5. Fluxo de Dados
Recebe as instruções do Controller, executa as operações CRUD no banco de dados via `Query Builder` (`$this->db`), e devolve arrays/objetos para o Controller.

## 6. Rotas Relacionadas
N/A

## 7. Observações e Regras de Negócio
- Documentação autogerada para reconstrução.
- Certifique-se de manter os padrões do CodeIgniter 3.
