<#
.SYNOPSIS
    Gerenciador automatizado dos ambientes de Desenvolvimento e Producao do STEOS.
.DESCRIPTION
    Comandos disponiveis:
      .\steos-env.ps1 status            - Mostra branch Git, containers locais e status da VPS ste01
      .\steos-env.ps1 start             - Ativa docker-compose.override.yml e sobe ambiente Dev local (Live-Reload + Asaas Sandbox)
      .\steos-env.ps1 stop              - Para os containers locais
      .\steos-env.ps1 pull-db           - Clona o banco MySQL real de Producao (VPS ste01) para o MySQL local de Dev
      .\steos-env.ps1 save ["msg"]      - Sincroniza migrations locais (se houver) e envia commit para origin/develop (sem deploy)
      .\steos-env.ps1 release ["msg"]   - Sincroniza migrations, atualiza develop, faz merge para main e dispara deploy automatico na VPS
.EXAMPLE
    .\steos-env.ps1 start
    .\steos-env.ps1 pull-db
    .\steos-env.ps1 save "feat: nova tela de contratos"
    .\steos-env.ps1 release "feat: libera modulo de contratos em producao"
#>
param(
    [Parameter(Position = 0)]
    [ValidateSet("status", "start", "stop", "pull-db", "save", "release", "help")]
    [string]$Action = "status",

    [Parameter(Position = 1)]
    [string]$Message = ""
)

$ErrorActionPreference = "Stop"
$ProjectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $ProjectRoot

$VpsHost = "82.29.58.84"
$VpsUser = "root"
$VpsSshKey = "$env:USERPROFILE\.ssh\id_ed25519_ste"
$GhCli = "C:\Program Files\GitHub CLI\gh.exe"

function Ensure-DevOverride {
    if (-not (Test-Path "$ProjectRoot\docker-compose.override.yml")) {
        if (-not (Test-Path "$ProjectRoot\docker-compose.dev.yml")) {
            throw "Arquivo docker-compose.dev.yml nao encontrado."
        }
        Copy-Item "$ProjectRoot\docker-compose.dev.yml" "$ProjectRoot\docker-compose.override.yml" -Force
        Write-Host "Gerado docker-compose.override.yml para ambiente local de Desenvolvimento." -ForegroundColor Cyan
    }
}

function Ensure-DockerNetwork {
    $net = docker network ls --filter "name=^steos$" --format "{{.Name}}" 2>$null
    if ($net -ne "steos") {
        Write-Host "Criando rede Docker local 'steos'..." -ForegroundColor Cyan
        docker network create steos | Out-Null
    }
}

function Sync-LocalMigrationsIfRunning {
    $running = docker ps --filter "name=^/steos$" --format "{{.Names}}" 2>$null
    if ($running -eq "steos") {
        Write-Host "Verificando alteracoes de tabelas no MySQL local..." -ForegroundColor Cyan
        $diffOut = docker exec steos php index.php tools diff_schema 2>&1
        if ($diffOut -match '\[\+ TABELA\]|\[\+ COLUNA\]|\[~ COLUNA\]') {
            Write-Host "Diferencas detectadas no schema local! Gerando migration automaticamente..." -ForegroundColor Yellow
            & "$ProjectRoot\steos-db-sync.ps1" sync "auto_sync_schema"
        } else {
            Write-Host "Nenhuma alteracao de tabela pendente." -ForegroundColor DarkGray
        }
    }
}

switch ($Action) {
    "help" {
        Get-Help $MyInvocation.MyCommand.Path
    }
    "status" {
        $branch = (git rev-parse --abbrev-ref HEAD).Trim()
        $envLabel = if ($branch -eq "main") { "PRODUCAO (main)" } elseif ($branch -eq "develop") { "DESENVOLVIMENTO (develop)" } else { "BRANCH ($branch)" }
        Write-Host "=== AMBIENTE GIT ===" -ForegroundColor Cyan
        Write-Host "Branch atual: $envLabel"
        git status -sb

        Write-Host "`n=== CONTAINERS LOCAIS (DEV) ===" -ForegroundColor Cyan
        try {
            docker ps --filter "name=steos" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
        } catch {
            Write-Host "Docker Desktop local indisponivel no momento." -ForegroundColor Yellow
        }

        if (Test-Path $VpsSshKey) {
            Write-Host "`n=== CONTAINERS VPS PRODUCAO (ste01 - $VpsHost) ===" -ForegroundColor Cyan
            ssh -i $VpsSshKey -o BatchMode=yes -o ConnectTimeout=6 "$VpsUser@$VpsHost" "docker ps --filter 'name=steos' --format 'table {{.Names}}\t{{.Status}}\t{{.Ports}}'"
        }

        if (Test-Path $GhCli) {
            Write-Host "`n=== ULTIMOS DEPLOYS (GITHUB ACTIONS) ===" -ForegroundColor Cyan
            & $GhCli run list --repo celiolima/steos-v4.53.2 --limit 3
        }
    }
    "start" {
        docker info *>$null
        if ($LASTEXITCODE -ne 0) {
            throw "Docker Desktop nao esta em execucao. Inicie o Docker Desktop no Windows e rode .\steos-env.ps1 start novamente."
        }
        Ensure-DockerNetwork
        Ensure-DevOverride
        Write-Host "Subindo ambiente de DESENVOLVIMENTO (Live-Reload + Asaas Sandbox)..." -ForegroundColor Cyan
        docker compose up -d
        if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
        Write-Host "`nAmbiente de Desenvolvimento online!" -ForegroundColor Green
        Write-Host "  STEOS Dev:   http://localhost:8080/index.php/login"
        Write-Host "  Conecte Dev: http://localhost:8080/index.php/mine"
        Write-Host "  phpMyAdmin:  http://localhost:9080"
    }
    "stop" {
        docker compose stop
    }
    "pull-db" {
        if (-not (Test-Path $VpsSshKey)) {
            throw "Chave SSH da VPS nao encontrada em $VpsSshKey"
        }
        $mysqlLocal = docker ps --filter "name=^/steos_mysql$" --format "{{.Names}}" 2>$null
        if ($mysqlLocal -ne "steos_mysql") {
            throw "Container local steos_mysql nao esta rodando. Execute .\steos-env.ps1 start primeiro."
        }

        $tmpGz = Join-Path $env:TEMP "steos_prod_clone_$(Get-Date -Format 'yyyyMMdd_HHmmss').sql.gz"
        $tmpSql = Join-Path $env:TEMP "steos_prod_clone_$(Get-Date -Format 'yyyyMMdd_HHmmss').sql"
        try {
            Write-Host "Baixando dump comprimido do MySQL de Producao (VPS $VpsHost)..." -ForegroundColor Cyan
            cmd /c "ssh -i `"$VpsSshKey`" -o BatchMode=yes $VpsUser@$VpsHost `"docker exec steos_mysql mysqldump -u root -psteos --single-transaction --routines --triggers --default-character-set=utf8mb4 steos | gzip -c`" > `"$tmpGz`""
            if ($LASTEXITCODE -ne 0 -or -not (Test-Path $tmpGz) -or ((Get-Item $tmpGz).Length -lt 512)) {
                throw "Falha ao baixar dump comprimido da VPS."
            }

            Write-Host "Descompactando dump sem BOM para restauracao..." -ForegroundColor Cyan
            $inStream = [System.IO.File]::OpenRead($tmpGz)
            $gzStream = New-Object System.IO.Compression.GZipStream($inStream, [System.IO.Compression.CompressionMode]::Decompress)
            $outStream = [System.IO.File]::Create($tmpSql)
            try { $gzStream.CopyTo($outStream) } finally { $outStream.Dispose(); $gzStream.Dispose(); $inStream.Dispose() }

            Write-Host "Restaurando dump no MySQL local de Desenvolvimento (steos_mysql)..." -ForegroundColor Cyan
            cmd /c "docker exec -i steos_mysql mysql -u root -psteos --default-character-set=utf8mb4 --init-command=`"SET NAMES utf8mb4; SET SESSION FOREIGN_KEY_CHECKS=0; SET SESSION sql_mode='';`" steos < `"$tmpSql`""
            if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

            Write-Host "Rodando migrations locais e atualizando schema_snapshot.json..." -ForegroundColor Cyan
            & "$ProjectRoot\steos-db-sync.ps1" migrate
            & "$ProjectRoot\steos-db-sync.ps1" snapshot
            Write-Host "Banco local sincronizado com Producao! (Asaas local continua em SANDBOX)" -ForegroundColor Green
        } finally {
            Remove-Item $tmpGz, $tmpSql -Force -ErrorAction SilentlyContinue
        }
    }
    "save" {
        $branch = (git rev-parse --abbrev-ref HEAD).Trim()
        if ($branch -ne "develop") {
            Write-Host "Alternando para a branch develop..." -ForegroundColor Cyan
            git checkout develop
        }
        Sync-LocalMigrationsIfRunning
        $changes = git status --porcelain
        if ($changes) {
            if ([string]::IsNullOrWhiteSpace($Message)) {
                $Message = "wip(dev): atualizacao em desenvolvimento $(Get-Date -Format 'yyyy-MM-dd HH:mm')"
            }
            git add -A
            git commit -m $Message
            if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
        } else {
            Write-Host "Nenhuma alteracao local pendente para commit." -ForegroundColor DarkGray
        }
        git push -u origin develop
        if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
        Write-Host "Codigo salvo em origin/develop (Producao NAO foi alterada)." -ForegroundColor Green
    }
    "release" {
        $branch = (git rev-parse --abbrev-ref HEAD).Trim()
        if ($branch -ne "develop") {
            git checkout develop
        }
        Sync-LocalMigrationsIfRunning
        $changes = git status --porcelain
        if ($changes) {
            if ([string]::IsNullOrWhiteSpace($Message)) {
                $Message = "release: publicacao de alteracoes validadas $(Get-Date -Format 'yyyy-MM-dd HH:mm')"
            }
            git add -A
            git commit -m $Message
            if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
        }
        git push -u origin develop
        if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

        Write-Host "Fazendo merge de develop -> main e disparando deploy em producao..." -ForegroundColor Cyan
        git checkout main
        if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
        git pull origin main
        git merge develop --no-edit
        if ($LASTEXITCODE -ne 0) {
            git checkout develop
            throw "Conflito de merge ao integrar develop em main. Resolva manualmente."
        }
        git push origin main
        if ($LASTEXITCODE -ne 0) {
            git checkout develop
            exit $LASTEXITCODE
        }
        git checkout develop
        Write-Host "Push na main realizado! Retornado automaticamente para a branch develop." -ForegroundColor Green

        if (Test-Path $GhCli) {
            Start-Sleep -Seconds 4
            & $GhCli run list --repo celiolima/steos-v4.53.2 --limit 2
        }
    }
}
