$ErrorActionPreference = 'Stop'

$appRoot = Split-Path -Parent $PSScriptRoot
Push-Location $appRoot

try {
    Write-Host 'Setting up the existing Nexware POS project. Existing .env and database files will be preserved.' -ForegroundColor Cyan

    $envPath = Join-Path $appRoot '.env'
    if (-not (Test-Path $envPath)) {
        $examplePath = Join-Path $appRoot '.env.example'
        if (-not (Test-Path $examplePath)) {
            throw '.env.example is missing; cannot create local configuration.'
        }

        Copy-Item $examplePath $envPath
        Write-Host 'Created .env from .env.example. Review local settings before use.' -ForegroundColor Yellow
    } else {
        Write-Host 'Preserving existing .env.' -ForegroundColor Green
    }

    Write-Host 'Installing locked Composer dependencies...' -ForegroundColor Cyan
    & composer install
    if ($LASTEXITCODE -ne 0) {
        throw 'composer install failed.'
    }

    $envText = Get-Content $envPath -Raw
    $keyMatch = [regex]::Match($envText, '(?m)^\s*APP_KEY\s*=\s*(.*?)\s*$')
    if (-not $keyMatch.Success -or [string]::IsNullOrWhiteSpace($keyMatch.Groups[1].Value)) {
        Write-Host 'APP_KEY is empty; generating the initial key only. Existing keys are never replaced.' -ForegroundColor Yellow
        & php artisan key:generate --no-interaction
        if ($LASTEXITCODE -ne 0) {
            throw 'Laravel could not generate an initial application key.'
        }
    } else {
        Write-Host 'Preserving the existing APP_KEY.' -ForegroundColor Green
    }

    if (Test-Path (Join-Path $appRoot 'package-lock.json')) {
        Write-Host 'Installing locked frontend dependencies with npm ci...' -ForegroundColor Cyan
        & npm ci
    } elseif (Test-Path (Join-Path $appRoot 'package.json')) {
        Write-Host 'No package-lock.json exists; installing using the existing package manifest.' -ForegroundColor Yellow
        & npm install
    } else {
        Write-Host 'No frontend package.json found; skipping frontend dependency installation.' -ForegroundColor Yellow
    }
    if ($LASTEXITCODE -ne 0) {
        throw 'Frontend dependency installation failed.'
    }

    if (Test-Path (Join-Path $appRoot 'package.json')) {
        Write-Host 'Building the existing Vite frontend...' -ForegroundColor Cyan
        & npm run build
        if ($LASTEXITCODE -ne 0) {
            throw 'npm run build failed.'
        }
    }

    Write-Host ''
    Write-Host 'Dependencies and frontend build are ready. No database migration was run.' -ForegroundColor Green
    Write-Host 'Review pending migrations with: php artisan migrate:status'
    Write-Host 'Only after reviewing them and confirming the target DB, apply with: php artisan migrate'
    Write-Host 'Start the web app with: .\scripts\start-local.ps1'
} finally {
    Pop-Location
}
