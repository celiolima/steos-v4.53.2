# Documentação: Tools.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\controllers\Tools.php`
- **Tipo:** CLI Controller (`$this->input->is_cli_request()` obrigatório)
- **Módulo:** `tools`

## 2. Comandos CLI Disponíveis
- `php index.php tools migration "nome"` — Cria novo arquivo de migration timestampado.
- `php index.php tools migrate ["versao"]` — Executa migrations pendentes até `$config['migration_version']` (ou versão específica). Executado no boot Docker e no deploy de produção.
- `php index.php tools seeder "nome"` / `seed ["nome"]` — Gera e executa seeders (`Permissoes`, `Usuarios`, `Configuracoes`).
- `php index.php tools schema_snapshot` — Exporta o schema atual do MySQL 8.0 (tabelas/colunas/índices) para `migrations/schema_snapshot.json`.
- `php index.php tools diff_schema` — Compara o banco ativo contra `migrations/schema_snapshot.json` exibindo diferenças estruturais.
- `php index.php tools sync_migration ["nome"]` — Gera migration CI3 idempotente (`up`/`down`) a partir do diff, atualiza `application/config/migration.php` e regenera o snapshot (acionado pelo wrapper `steos-db-sync.ps1`).
