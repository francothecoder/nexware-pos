param(
    [ValidateRange(1, 65535)]
    [int]$Port = 8000
)

$ErrorActionPreference = 'Stop'

$appRoot = Split-Path -Parent $PSScriptRoot
Push-Location $appRoot

try {
    if (-not (Test-Path (Join-Path $appRoot 'artisan'))) {
        throw 'Laravel artisan entry point was not found.'
    }
    if (-not (Test-Path (Join-Path $appRoot '.env'))) {
        throw '.env is missing. Run .\scripts\setup-local.ps1 first; it will preserve any existing configuration.'
    }

    Write-Host "Starting Nexware POS at http://127.0.0.1:$Port" -ForegroundColor Cyan
    Write-Host 'Press Ctrl+C to stop the Laravel development server.'
    & php artisan serve --host=127.0.0.1 --port=$Port
    if ($LASTEXITCODE -ne 0) {
        throw 'Laravel development server stopped with an error.'
    }
} finally {
    Pop-Location
}
