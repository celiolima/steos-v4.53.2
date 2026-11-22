# Documentação: `Permissoes.php` & Visão Geral do Sistema ACL (92 Chaves Granulares)

## 1. Identificação
- **Controller:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\controllers\Permissoes.php`
- **Views Principais:** `permissoes.php`, `adicionarPermissao.php`, `editarPermissao.php`
- **Library ACL:** `application/libraries/Permission.php` (`$this->permission->checkPermission($idPermissao, $chave)`)
- **Tabela & Migration:** `permissoes` (`20261122153000_add_granular_permissions.php` / `migrations/sql/026_add_granular_permissions.sql`)

## 2. Arquitetura do ACL Granular (92 Chaves)
- Cada perfil em `permissoes` armazena na coluna `permissoes` um objeto JSON com **92 chaves** (`'1'` = permitido, `null`/`0` = negado).
- Em `Permissoes.php`, os métodos `adicionar()` e `editar()` coletam todas as 92 chaves de POST através do helper privado **`coletarPermissoesPost()`**, eliminando duplicação de código e garantindo sincronia entre criação e edição.
- Convenção obrigatória de prefixos:
  - `v<Modulo>`: Visualizar / Listar / Imprimir / Baixar anexo
  - `a<Modulo>`: Adicionar / Emitir
  - `e<Modulo>`: Editar / Movimentar / Gerar O.S. Preventivas / Ativar Assinatura
  - `d<Modulo>`: Excluir / Cancelar
  - `r<Relatorio>`: Relatórios
  - `c<Config>`: Configurações Administrativas & Ferramentas Técnicas

## 3. Grupos de Permissões (92 Chaves)
- **57 chaves originais:** Clientes (`v/a/e/dCliente`), Produtos (`v/a/e/dProduto`), Serviços (`v/a/e/dServico`), Ordens de Serviço (`v/a/e/dOs`), Vendas (`v/a/e/dVenda`), Arquivos (`v/a/e/dArquivo`), Pagamentos (`v/a/e/dPagamento` — checkboxes restaurados em `editarPermissao.php`), Lançamentos Financeiros (`v/a/e/dLancamento`), Garantias (`v/a/e/dGarantia`), Cobranças (`v/a/e/dCobranca`), Veículos (`v/a/e/dVeiculo`), Relatórios (`rCliente`, `rProduto`, `rServico`, `rOs`, `rVenda`, `rFinanceiro`) e Configurações (`cUsuario`, `cEmitente`, `cPermissao`, `cBackup`, `cAuditoria`, `cEmail`, `cSistema`).
- **35 novas chaves granulares (`20261122153000`):**
  - **Contratos:** `vContrato`, `aContrato`, `eContrato`, `dContrato`
  - **NFS-e & Serviços Fiscais:** `vNfse`, `aNfse`, `eNfse`, `dNfse`
  - **Equipamentos:** `vEquipamento`, `aEquipamento`, `eEquipamento`, `dEquipamento`
  - **Sistemas & Checklists:** `vSistema`, `aSistema`, `eSistema`, `dSistema`
  - **Modelos de Documentos:** `vModelo`, `aModelo`, `eModelo`, `dModelo`
  - **Técnicos:** `vTecnico`, `aTecnico`, `eTecnico`, `dTecnico`
  - **Contas Bancárias:** `vConta`, `aConta`, `eConta`, `dConta`
  - **Cadastros Financeiros Auxiliares** (*Bancos, Centro de Gastos, Classificação Financeira, Forma de Pagamento, Grupo Financeiro*): `vCadFinanceiro`, `aCadFinanceiro`, `eCadFinanceiro`, `dCadFinanceiro`
  - **Relatório de Comissões:** `rComissao`
  - **Logs Webhooks Asaas:** `vLogsAsaas`
  - **Ferramentas/Admin Asaas** (*Simulador Asaas + Sincronizar Assinaturas Órfãs*): `cAsaas`

## 4. Proteção Anti-Lockout e Herança de Perfis
- A migration `20261122153000` garante `'1'` em **todas as 92 chaves** para `idPermissao = 1` ou `ADMINISTRADOR` (incluindo `*Pagamento`).
- Perfis não-administradores herdam apenas equivalentes seguros do perfil anterior (ex.: `v/a/e/dNfse`, `v/a/e/dConta` e `v/a/e/dCadFinanceiro` herdam `*Lancamento`; `vContrato..dContrato` e `a/e/dSistema` iniciam como `null` pelo princípio de menor privilégio).

## 5. Regra Permanente de Governança
- **Todo novo módulo, controller, ação AJAX sensível ou endpoint da API v1 criado/expandido no STEOS DEVE implementar sua respectiva granulação ACL quando pertinente** (chaves, migration idempotente sem lockout, atualização de `Permissoes.php`/views, guards no backend/frontend/API e atualização em `.specs/`).
