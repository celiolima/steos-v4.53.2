<#
.SYNOPSIS
    Utilitário de sincronização de schema e migrations do STEOS.
.DESCRIPTION
    Executa diff_schema, sync_migration, schema_snapshot ou migrate dentro do container
    Docker local `steos` (ou via PHP CLI local, caso o container não esteja ativo),
    mantendo os arquivos gerados sincronizados no workspace local.
.EXAMPLE
    .\steos-db-sync.ps1 diff
    .\steos-db-sync.ps1 sync add_campos_contrato
    .\steos-db-sync.ps1 snapshot
    .\steos-db-sync.ps1 migrate
#>
param(
    [Parameter(Position = 0)]
    [ValidateSet("diff", "sync", "snapshot", "migrate", "help")]
    [string]$Action = "diff",

    [Parameter(Position = 1)]
    [string]$MigrationName = "auto_sync_schema"
)

$ErrorActionPreference = "Stop"
$ProjectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $ProjectRoot

function Test-SteosContainerRunning {
    try {
        $running = docker ps --filter "name=^/steos$" --format "{{.Names}}" 2>$null
        return ($running -eq "steos")
    } catch {
        return $false
    }
}

$useDocker = Test-SteosContainerRunning

switch ($Action) {
    "help" {
        if ($useDocker) {
            docker exec steos php index.php tools help
        } else {
            php index.php tools help
        }
    }
    "snapshot" {
        if ($useDocker) {
            docker cp "$ProjectRoot\application\controllers\Tools.php" steos:/var/www/html/application/controllers/Tools.php
            docker cp "$ProjectRoot\application\config\migration.php" steos:/var/www/html/application/config/migration.php
            docker exec steos php index.php tools schema_snapshot
            docker cp steos:/var/www/html/migrations/schema_snapshot.json "$ProjectRoot\migrations\schema_snapshot.json"
        } else {
            php index.php tools schema_snapshot
        }
    }
    "diff" {
        if ($useDocker) {
            docker cp "$ProjectRoot\application\controllers\Tools.php" steos:/var/www/html/application/controllers/Tools.php
            if (Test-Path "$ProjectRoot\migrations\schema_snapshot.json") {
                docker cp "$ProjectRoot\migrations\schema_snapshot.json" steos:/var/www/html/migrations/schema_snapshot.json
            }
            docker exec steos php index.php tools diff_schema
        } else {
            php index.php tools diff_schema
        }
    }
    "sync" {
        if ($useDocker) {
            docker cp "$ProjectRoot\application\controllers\Tools.php" steos:/var/www/html/application/controllers/Tools.php
            docker cp "$ProjectRoot\application\config\migration.php" steos:/var/www/html/application/config/migration.php
            if (Test-Path "$ProjectRoot\migrations\schema_snapshot.json") {
                docker cp "$ProjectRoot\migrations\schema_snapshot.json" steos:/var/www/html/migrations/schema_snapshot.json
            }

            $output = docker exec steos php index.php tools sync_migration "$MigrationName"
            $output | ForEach-Object { Write-Host $_ }

            $match = ($output | Select-String -Pattern 'application/database/migrations/([^\s]+\.php)' | Select-Object -First 1)
            if ($match) {
                $fileName = $match.Matches[0].Groups[1].Value
                docker cp "steos:/var/www/html/application/database/migrations/$fileName" "$ProjectRoot\application\database\migrations\$fileName"
                docker cp "steos:/var/www/html/application/config/migration.php" "$ProjectRoot\application\config\migration.php"
                docker cp "steos:/var/www/html/migrations/schema_snapshot.json" "$ProjectRoot\migrations\schema_snapshot.json"
                Write-Host "Sincronizado para o repositorio local: application\database\migrations\$fileName" -ForegroundColor Green
            }
        } else {
            php index.php tools sync_migration "$MigrationName"
        }
    }
    "migrate" {
        if ($useDocker) {
            docker exec steos php index.php tools migrate
        } else {
            php index.php tools migrate
        }
    }
}
