# Documentação: migration.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\config\migration.php`
- **Tipo:** Config
- **Módulo:** `configuracoes`

## 2. Parâmetros Ativos
- `$config['migration_enabled'] = true;`
- `$config['migration_type'] = 'timestamp';`
- `$config['migration_table'] = 'migrations';`
- `$config['migration_auto_latest'] = false;` (executado explicitamente no boot Docker ou CLI via `php index.php tools migrate`)
- `$config['migration_version'] = 20261122153000;` (`20261122153000_add_granular_permissions.php`)
- `$config['migration_path'] = APPPATH . 'database/migrations/';`

## 3. Fluxo Recomendado
Alterações de schema devem ser geradas via `.\steos-db-sync.ps1 sync "nome"` (ou `Tools::sync_migration`), mantendo `migration_version` e `migrations/schema_snapshot.json` sempre alinhados.
