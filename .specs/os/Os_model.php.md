# Documentação: Os_model.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\models\Os_model.php`
- **Tipo:** Model
- **Módulo:** os

## 2. Propósito
Responsável pelo processamento e lógica pertinente à camada de Model do módulo de os.

## 3. Dependências
Nenhuma dependência explícita via `$this->load`.

## 4. Estrutura Interna
### Método: `__construct`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `__construct`.

### Método: `get`
- **Parâmetros:** `$table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array'`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `get`.

### Método: `getOs`
- **Parâmetros:** `$table, $fields, $where = [], $perpage = 0, $start = 0, $one = false, $array = 'array'`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getOs`.

### Método: `getOsComissao`
- **Parâmetros:** `$table, $fields, $where = [], $perpage = 0, $start = 0, $one = false, $tecnico = false, $array = 'array'`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getOsComissao`.

### Método: `getById`
- **Parâmetros:** `$id`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getById`.

### Método: `getOsNomeTecnicos`
- **Parâmetros:** `$id = null`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getOsNomeTecnicos`.

### Método: `getByIdCobrancas`
- **Parâmetros:** `$id`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getByIdCobrancas`.

### Método: `getByIdAssinatura`
- **Parâmetros:** `$id`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getByIdAssinatura`.

### Método: `getByIdAssinaturaExtr`
- **Parâmetros:** `$id`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getByIdAssinaturaExtr`.

### Método: `getProdutos`
- **Parâmetros:** `$id = null`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getProdutos`.

### Método: `getServicos`
- **Parâmetros:** `$id = null`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getServicos`.

### Método: `add`
- **Parâmetros:** `$table, $data, $returnId = false`
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

### Método: `autoCompleteProduto`
- **Parâmetros:** `$q`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `autoCompleteProduto`.

### Método: `autoCompleteProdutoSaida`
- **Parâmetros:** `$q`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `autoCompleteProdutoSaida`.

### Método: `autoCompleteCliente`
- **Parâmetros:** `$q`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `autoCompleteCliente`.

### Método: `autoCompleteEquipamentos`
- **Parâmetros:** `$q`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `autoCompleteEquipamentos`.

### Método: `autoCompleteUsuario`
- **Parâmetros:** `$q`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `autoCompleteUsuario`.

### Método: `autoCompleteTermoGarantia`
- **Parâmetros:** `$q`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `autoCompleteTermoGarantia`.

### Método: `autoCompleteServico`
- **Parâmetros:** `$q`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `autoCompleteServico`.

### Método: `anexar`
- **Parâmetros:** `$os, $anexo, $url, $thumb, $path`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `anexar`.

### Método: `getAnexos`
- **Parâmetros:** `$os`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getAnexos`.

### Método: `getAnotacoes`
- **Parâmetros:** `$os`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getAnotacoes`.

### Método: `getCobrancas`
- **Parâmetros:** `$id = null`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getCobrancas`.

### Método: `criarTextoWhats`
- **Parâmetros:** `$textoBase, $troca`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `criarTextoWhats`.

### Método: `valorTotalOS`
- **Parâmetros:** `$id = null`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `valorTotalOS`.

### Método: `isEditable`
- **Parâmetros:** `$id = null`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `isEditable`.

### Método: `getQrCode`
- **Parâmetros:** `$id, $pixKey, $emitente`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getQrCode`.

### Método: `faturarOs`
- **Parâmetros:** `$os_id, $dataLancamento, $dadosOs`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `faturarOs`.

**Queries SQL:** Contem operacoes de banco de dados via Query Builder.


## 5. Fluxo de Dados
Recebe as instruções do Controller, executa as operações CRUD no banco de dados via `Query Builder` (`$this->db`), e devolve arrays/objetos para o Controller.

## 6. Rotas Relacionadas
N/A

## 7. Observações e Regras de Negócio
- Documentação autogerada para reconstrução.
- Certifique-se de manter os padrões do CodeIgniter 3.
