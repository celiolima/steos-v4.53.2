# Controller `Sistemas.php`

## Objetivo
Módulo destinado ao cadastro e manutenção do catálogo de Sistemas e Equipamentos mestre (ex: Sistema de CFTV Intelbras, Sistema de Alarme Bosch, Licença de Software BPO).

## Integração (Dependências)
- **Base de Cálculo:** Estes itens são os "ativos" puxados pelo Módulo de Contratos (`Contratos.php`). Ao atrelar um sistema cadastrado aqui a um cliente, o valor dele passa a compor a base monetária do faturamento mensal (tabela `sistemas_contratos`).
- **Checklists Preventivos:** Cada sistema possui um padrão de "Checks" (verificações técnicas) atreladas (`sistemas_checks`). Quando o sistema é vendido via contrato, esses checks são clonados para `sistemas_contratos_checks`, para que o técnico os cumpra presencialmente todo mês.

## Tabelas de Banco
- `sistemas`
- `sistemas_checks`
