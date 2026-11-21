<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Tools extends CI_Controller
{
    /** @var \Faker\Generator */
    public $faker;

    /** @var Seeder */
    public $seeder;

    public function __construct()
    {
        parent::__construct();

        // can only be called from the command line
        if (! $this->input->is_cli_request()) {
            exit('Direct access is not allowed. This is a command line tool, use the terminal');
        }

        $this->load->dbforge();

        $this->load->library('Seeder');

        // initiate faker
        $this->faker = Faker\Factory::create();

        // initiate seeder
        $this->seeder = Seeder::create();
    }

    public function index()
    {
        $this->help();
    }

    public function message($to = 'World')
    {
        echo "Hello {$to}!" . PHP_EOL;
    }

    public function help()
    {
        $result = "The following are the available command line interface commands\n\n";
        $result .= "php index.php tools migration \"file_name\"         Create new migration file\n";
        $result .= "php index.php tools migrate [\"version_number\"]    Run all migrations. The version number is optional.\n";
        $result .= "php index.php tools seeder \"file_name\"            Creates a new seed file.\n";
        $result .= "php index.php tools seed \"file_name\"              Run the specified seed file.\n";
        $result .= "php index.php tools schema_snapshot                 Save current MySQL schema to migrations/schema_snapshot.json\n";
        $result .= "php index.php tools diff_schema                     Show schema differences against migrations/schema_snapshot.json\n";
        $result .= "php index.php tools sync_migration [\"file_name\"]    Generate idempotent CI3 migration from schema diff\n";

        echo $result . PHP_EOL;
    }

    public function schema_snapshot()
    {
        $schema = $this->get_current_db_schema();
        $snapshotPath = FCPATH . 'migrations/schema_snapshot.json';

        if (!is_dir(dirname($snapshotPath))) {
            mkdir(dirname($snapshotPath), 0755, true);
        }

        $json = json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if (file_put_contents($snapshotPath, $json . PHP_EOL) === false) {
            fwrite(STDERR, "Erro ao gravar snapshot em {$snapshotPath}" . PHP_EOL);
            exit(1);
        }

        $tableCount = count($schema['tables']);
        echo "Snapshot salvo com sucesso ({$tableCount} tabelas) em: {$snapshotPath}" . PHP_EOL;
    }

    public function diff_schema()
    {
        $snapshotPath = FCPATH . 'migrations/schema_snapshot.json';
        if (!file_exists($snapshotPath)) {
            echo "Snapshot nao encontrado em {$snapshotPath}. Execute: php index.php tools schema_snapshot" . PHP_EOL;
            return;
        }

        $oldSchema = json_decode(file_get_contents($snapshotPath), true);
        if (!is_array($oldSchema) || !isset($oldSchema['tables'])) {
            fwrite(STDERR, "Snapshot invalido em {$snapshotPath}" . PHP_EOL);
            exit(1);
        }

        $newSchema = $this->get_current_db_schema();
        $diff = $this->compute_schema_diff($oldSchema['tables'], $newSchema['tables']);

        if ($diff['total_changes'] === 0) {
            echo "Nenhuma diferenca de schema detectada em relacao a migrations/schema_snapshot.json." . PHP_EOL;
            return;
        }

        echo "Diferencas detectadas no banco '{$newSchema['database']}':" . PHP_EOL;
        foreach ($diff['new_tables'] as $table) {
            echo "  [+ TABELA] {$table}" . PHP_EOL;
        }
        foreach ($diff['new_columns'] as $item) {
            echo "  [+ COLUNA] {$item['table']}.{$item['column']} ({$item['definition']['COLUMN_TYPE']})" . PHP_EOL;
        }
        foreach ($diff['modified_columns'] as $item) {
            echo "  [~ COLUNA] {$item['table']}.{$item['column']}: {$item['old']['COLUMN_TYPE']} -> {$item['new']['COLUMN_TYPE']}" . PHP_EOL;
        }
        foreach ($diff['removed_tables'] as $table) {
            echo "  [- AVISO TABELA REMOVIDA] {$table} (nao gerada automaticamente por seguranca)" . PHP_EOL;
        }
        foreach ($diff['removed_columns'] as $item) {
            echo "  [- AVISO COLUNA REMOVIDA] {$item['table']}.{$item['column']} (nao gerada automaticamente por seguranca)" . PHP_EOL;
        }
    }

    public function sync_migration($name = 'auto_sync_schema')
    {
        $snapshotPath = FCPATH . 'migrations/schema_snapshot.json';
        if (!file_exists($snapshotPath)) {
            fwrite(STDERR, "Snapshot base nao encontrado em {$snapshotPath}. Execute primeiro: php index.php tools schema_snapshot" . PHP_EOL);
            exit(1);
        }

        $oldSchema = json_decode(file_get_contents($snapshotPath), true);
        if (!is_array($oldSchema) || !isset($oldSchema['tables'])) {
            fwrite(STDERR, "Snapshot base invalido em {$snapshotPath}" . PHP_EOL);
            exit(1);
        }

        $newSchema = $this->get_current_db_schema();
        $diff = $this->compute_schema_diff($oldSchema['tables'], $newSchema['tables']);

        $actionable = count($diff['new_tables']) + count($diff['new_columns']) + count($diff['modified_columns']);
        if ($actionable === 0) {
            echo "Nenhuma adicao ou modificacao de tabela/coluna detectada para gerar migration." . PHP_EOL;
            return;
        }

        $safeName = trim(preg_replace('/[^a-z0-9_]+/', '_', strtolower((string) $name)), '_');
        if ($safeName === '') {
            $safeName = 'auto_sync_schema';
        }

        $timestamp = (new DateTime())->format('YmdHis');
        $className = 'Migration_' . $safeName;
        $migrationFile = APPPATH . "database/migrations/{$timestamp}_{$safeName}.php";

        $upStatements = [];
        $downStatements = [];

        foreach ($diff['new_tables'] as $table) {
            $createRow = $this->db->query("SHOW CREATE TABLE `{$table}`")->row_array();
            $createSql = $createRow['Create Table'] ?? '';
            if ($createSql !== '') {
                $createSql = preg_replace('/^CREATE TABLE /i', 'CREATE TABLE IF NOT EXISTS ', $createSql);
                $escapedCreate = var_export($createSql, true);
                $upStatements[] = "        if (!\$this->db->table_exists('{$table}')) {\n            \$this->db->query({$escapedCreate});\n        }";
                $downStatements[] = "        if (\$this->db->table_exists('{$table}')) {\n            \$this->db->query('DROP TABLE IF EXISTS `{$table}`');\n        }";
            }
        }

        foreach ($diff['new_columns'] as $item) {
            $table = $item['table'];
            $col = $item['column'];
            $colSql = $this->build_column_sql_definition($item['definition']);
            $alterUp = var_export("ALTER TABLE `{$table}` ADD COLUMN `{$col}` {$colSql}", true);
            $upStatements[] = "        if (!\$this->db->field_exists('{$col}', '{$table}')) {\n            \$this->db->query({$alterUp});\n        }";
            $downStatements[] = "        if (\$this->db->field_exists('{$col}', '{$table}')) {\n            \$this->db->query(\"ALTER TABLE `{$table}` DROP COLUMN `{$col}`\");\n        }";
        }

        foreach ($diff['modified_columns'] as $item) {
            $table = $item['table'];
            $col = $item['column'];
            $newSql = $this->build_column_sql_definition($item['new']);
            $oldSql = $this->build_column_sql_definition($item['old']);
            $alterUp = var_export("ALTER TABLE `{$table}` MODIFY COLUMN `{$col}` {$newSql}", true);
            $alterDown = var_export("ALTER TABLE `{$table}` MODIFY COLUMN `{$col}` {$oldSql}", true);
            $upStatements[] = "        if (\$this->db->field_exists('{$col}', '{$table}')) {\n            \$this->db->query({$alterUp});\n        }";
            $downStatements[] = "        if (\$this->db->field_exists('{$col}', '{$table}')) {\n            \$this->db->query({$alterDown});\n        }";
        }

        $upBody = implode("\n\n", $upStatements);
        $downBody = implode("\n\n", array_reverse($downStatements));

        $content = <<<PHP
<?php

defined('BASEPATH') or exit('No direct script access allowed');

class {$className} extends CI_Migration
{
    public function up()
    {
{$upBody}
    }

    public function down()
    {
{$downBody}
    }
}

PHP;

        if (file_put_contents($migrationFile, $content) === false) {
            fwrite(STDERR, "Erro ao gravar migration em {$migrationFile}" . PHP_EOL);
            exit(1);
        }

        // Atualiza $config['migration_version'] em application/config/migration.php
        $migrationConfigPath = APPPATH . 'config/migration.php';
        if (file_exists($migrationConfigPath)) {
            $cfg = file_get_contents($migrationConfigPath);
            $updatedCfg = preg_replace('/(\$config\[\'migration_version\'\]\s*=\s*)\d+(\s*;)/', '${1}' . $timestamp . '${2}', $cfg);
            if ($updatedCfg !== null) {
                file_put_contents($migrationConfigPath, $updatedCfg);
            }
        }

        // Registra a nova versao na tabela migrations local, pois a alteracao ja existe no banco local
        if ($this->db->table_exists('migrations')) {
            $count = $this->db->count_all('migrations');
            if ($count > 0) {
                $this->db->update('migrations', ['version' => $timestamp]);
            } else {
                $this->db->insert('migrations', ['version' => $timestamp]);
            }
        }

        // Atualiza snapshot local
        $json = json_encode($newSchema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        file_put_contents($snapshotPath, $json . PHP_EOL);

        echo "Migration gerada com sucesso: {$migrationFile}" . PHP_EOL;
        echo "Versao atualizada em application/config/migration.php e migrations/schema_snapshot.json ({$timestamp})." . PHP_EOL;
    }

    protected function get_current_db_schema()
    {
        $dbName = $this->db->database;

        $tablesQuery = $this->db->query(
            "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_TYPE = 'BASE TABLE' ORDER BY TABLE_NAME",
            [$dbName]
        );

        $colsQuery = $this->db->query(
            "SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, EXTRA, COLUMN_KEY
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = ?
             ORDER BY TABLE_NAME, ORDINAL_POSITION",
            [$dbName]
        );

        $tables = [];
        foreach ($tablesQuery->result_array() as $row) {
            $tName = $row['TABLE_NAME'];
            if ($tName === 'migrations') {
                continue;
            }
            $tables[$tName] = ['columns' => []];
        }

        foreach ($colsQuery->result_array() as $col) {
            $tName = $col['TABLE_NAME'];
            if ($tName === 'migrations' || !isset($tables[$tName])) {
                continue;
            }
            $cName = $col['COLUMN_NAME'];
            $tables[$tName]['columns'][$cName] = [
                'COLUMN_TYPE'    => $col['COLUMN_TYPE'],
                'IS_NULLABLE'    => $col['IS_NULLABLE'],
                'COLUMN_DEFAULT' => $col['COLUMN_DEFAULT'],
                'EXTRA'          => $col['EXTRA'],
                'COLUMN_KEY'     => $col['COLUMN_KEY'],
            ];
        }

        return [
            'generated_at' => gmdate('c'),
            'database'     => $dbName,
            'tables'       => $tables,
        ];
    }

    protected function compute_schema_diff(array $oldTables, array $newTables)
    {
        $newTableNames = [];
        $removedTableNames = [];
        $newColumns = [];
        $modifiedColumns = [];
        $removedColumns = [];

        foreach ($newTables as $tableName => $tableDef) {
            if (!isset($oldTables[$tableName])) {
                $newTableNames[] = $tableName;
                continue;
            }

            $oldCols = $oldTables[$tableName]['columns'] ?? [];
            $newCols = $tableDef['columns'] ?? [];

            foreach ($newCols as $colName => $colDef) {
                if (!isset($oldCols[$colName])) {
                    $newColumns[] = [
                        'table'      => $tableName,
                        'column'     => $colName,
                        'definition' => $colDef,
                    ];
                } else {
                    $oldNorm = $this->normalize_column_def($oldCols[$colName]);
                    $newNorm = $this->normalize_column_def($colDef);
                    if ($oldNorm !== $newNorm) {
                        $modifiedColumns[] = [
                            'table'  => $tableName,
                            'column' => $colName,
                            'old'    => $oldCols[$colName],
                            'new'    => $colDef,
                        ];
                    }
                }
            }

            foreach ($oldCols as $colName => $colDef) {
                if (!isset($newCols[$colName])) {
                    $removedColumns[] = [
                        'table'  => $tableName,
                        'column' => $colName,
                    ];
                }
            }
        }

        foreach ($oldTables as $tableName => $tableDef) {
            if (!isset($newTables[$tableName])) {
                $removedTableNames[] = $tableName;
            }
        }

        return [
            'new_tables'       => $newTableNames,
            'removed_tables'   => $removedTableNames,
            'new_columns'      => $newColumns,
            'modified_columns' => $modifiedColumns,
            'removed_columns'  => $removedColumns,
            'total_changes'    => count($newTableNames) + count($removedTableNames) + count($newColumns) + count($modifiedColumns) + count($removedColumns),
        ];
    }

    protected function normalize_column_def(array $def)
    {
        return [
            'COLUMN_TYPE'    => strtolower((string) ($def['COLUMN_TYPE'] ?? '')),
            'IS_NULLABLE'    => strtoupper((string) ($def['IS_NULLABLE'] ?? 'YES')),
            'COLUMN_DEFAULT' => $def['COLUMN_DEFAULT'] ?? null,
            'EXTRA'          => strtolower(trim(str_replace('DEFAULT_GENERATED', '', (string) ($def['EXTRA'] ?? '')))),
        ];
    }

    protected function build_column_sql_definition(array $def)
    {
        $parts = [$def['COLUMN_TYPE']];
        $isNullable = strtoupper((string) ($def['IS_NULLABLE'] ?? 'YES')) === 'YES';
        $parts[] = $isNullable ? 'NULL' : 'NOT NULL';

        $default = $def['COLUMN_DEFAULT'] ?? null;
        if ($default !== null) {
            $upperDef = strtoupper((string) $default);
            if (strpos($upperDef, 'CURRENT_TIMESTAMP') === 0 || $upperDef === 'NULL') {
                $parts[] = 'DEFAULT ' . $upperDef;
            } else {
                $parts[] = "DEFAULT " . $this->db->escape($default);
            }
        } elseif ($isNullable) {
            $parts[] = 'DEFAULT NULL';
        }

        $extra = trim(str_replace('DEFAULT_GENERATED', '', (string) ($def['EXTRA'] ?? '')));
        if ($extra !== '' && stripos($extra, 'auto_increment') !== false) {
            $parts[] = 'AUTO_INCREMENT';
        } elseif ($extra !== '') {
            $parts[] = $extra;
        }

        return implode(' ', $parts);
    }

    public function migration($name)
    {
        $this->make_migration_file($name);
    }

    public function migrate($version = null)
    {
        $this->load->library('migration');

        if ($version != null) {
            if ($this->migration->version($version) === false) {
                show_error($this->migration->error_string());
            } else {
                echo 'Migrations run successfully' . PHP_EOL;
            }

            return;
        }

        if ($this->migration->latest() === false) {
            show_error($this->migration->error_string());
        } else {
            echo 'Migrations run successfully' . PHP_EOL;
        }
    }

    public function seeder($name)
    {
        $this->make_seed_file($name);
    }

    public function seed($name = null)
    {
        if ($name) {
            $this->seeder->call($name);

            echo 'Seeds run successfully' . PHP_EOL;

            return;
        }

        $seeds = [
            'Permissoes',
            'Usuarios',
            'Configuracoes',
        ];

        foreach ($seeds as $seed) {
            $this->seeder->call($seed);
        }

        echo 'Seeds run successfully' . PHP_EOL;
    }

    protected function make_migration_file($name)
    {
        $date = new DateTime();
        $timestamp = $date->format('YmdHis');

        $path = APPPATH . "database/migrations/$timestamp" . '_' . "$name.php";

        $my_migration = fopen($path, 'w') or exit('Unable to create migration file!');

        $migration_stub_path = APPPATH . 'database/stubs/migration.stub';

        $migration_stub = file_get_contents($migration_stub_path) or exit('Unable to open migration stub!');

        $migration_stub = preg_replace('/{name}/', $name, $migration_stub);

        fwrite($my_migration, $migration_stub);

        fclose($my_migration);

        echo "$path migration has successfully been created." . PHP_EOL;
    }

    protected function make_seed_file($name)
    {
        $className = ucfirst($name);

        $path = APPPATH . "database/seeds/$className.php";

        $my_seed = fopen($path, 'w') or exit('Unable to create seed file!');

        $seed_stub_path = APPPATH . 'database/stubs/seed.stub';

        $seed_stub = file_get_contents($seed_stub_path) or exit('Unable to open seed stub!');

        $seed_stub = preg_replace('/{name}/', $className, $seed_stub);

        fwrite($my_seed, $seed_stub);

        fclose($my_seed);

        echo "$path seeder has successfully been created." . PHP_EOL;
    }
}
