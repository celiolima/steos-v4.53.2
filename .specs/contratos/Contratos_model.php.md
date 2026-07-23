# Model `Contratos_model.php`

## Objetivo
Abstração do Query Builder para manipular e persistir as relações complexas dos contratos no banco de dados MySQL.

## Estrutura do Banco Atrelada
- `contratos`
- `sistemas_contratos`
- `sistemas_contratos_checks`

## Métodos Críticos
- `add()`, `edit()`, `delete()`: Funções CRUD padrão.
- `getOsByContrato($id)`: Traz todas as Ordens de Serviço (preventivas ou corretivas) atreladas ao contrato.
- `getSistemasByContrato($id)`: Lista todos os ativos sob gestão (Sistemas/Equipamentos) do contrato específico, gerando a soma da base de faturamento.
- `getCobrancasByContrato($id)`: Retorna o histórico de cobranças recorrentes (faturas Asaas).
- `getAnexos($id)`: Recupera os PDFs digitalizados.
- `autoCompleteSistema($q)`: Ajax lookup para vincular sistemas.
