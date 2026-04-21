param(
    [string]$TargetUrl = "http://localhost:80",
    [string]$EnvPath = ".env",
    [int]$WaitSeconds = 25
)

$ErrorActionPreference = "Stop"

function Write-Info($message) {
    Write-Host "[cloudflare] $message" -ForegroundColor Cyan
}

if (-not (Get-Command cloudflared -ErrorAction SilentlyContinue)) {
    Write-Host "No se encontro 'cloudflared' en PATH." -ForegroundColor Red
    Write-Host "Instalalo desde: https://developers.cloudflare.com/cloudflare-one/connections/connect-networks/downloads/" -ForegroundColor Yellow
    exit 1
}

$projectRoot = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path
$envFile = Join-Path $projectRoot $EnvPath
$logFile = Join-Path $projectRoot ".cloudflared-quick.log"

if (-not (Test-Path $envFile)) {
    Write-Host "No existe el archivo .env en: $envFile" -ForegroundColor Red
    exit 1
}

Get-Process cloudflared -ErrorAction SilentlyContinue | Stop-Process -Force -ErrorAction SilentlyContinue
if (Test-Path $logFile) {
    Remove-Item $logFile -Force
}

Write-Info "Iniciando Cloudflare Quick Tunnel hacia $TargetUrl..."
$process = Start-Process -FilePath "cloudflared" `
    -ArgumentList @("tunnel", "--url", $TargetUrl, "--logfile", $logFile, "--loglevel", "info", "--no-autoupdate") `
    -WorkingDirectory $projectRoot `
    -PassThru `
    -WindowStyle Hidden

$publicUrl = $null
$deadline = (Get-Date).AddSeconds($WaitSeconds)

while ((Get-Date) -lt $deadline) {
    Start-Sleep -Milliseconds 500
    if (Test-Path $logFile) {
        $logContent = Get-Content $logFile -Raw
        $match = [regex]::Match($logContent, "https://[a-z0-9-]+\.trycloudflare\.com")
        if ($match.Success) {
            $publicUrl = $match.Value
            break
        }
    }
}

if (-not $publicUrl) {
    Write-Host "No se pudo detectar la URL publica. Revisa el log: $logFile" -ForegroundColor Yellow
    Write-Host "Proceso iniciado con PID $($process.Id)." -ForegroundColor Yellow
    exit 1
}

$envContent = Get-Content $envFile -Raw

if ($envContent -match "(?m)^APP_URL=") {
    $envContent = [regex]::Replace($envContent, "(?m)^APP_URL=.*$", "APP_URL=$publicUrl")
} else {
    $envContent = "APP_URL=$publicUrl`r`n$envContent"
}

$envContent = [regex]::Replace($envContent, "(?m)^VITE_DEV_SERVER_URL=.*$", "# VITE_DEV_SERVER_URL=")

Set-Content -Path $envFile -Value $envContent -Encoding UTF8

Write-Info "Tunnel activo: $publicUrl"
Write-Info "APP_URL actualizado en .env"
Write-Info "VITE_DEV_SERVER_URL desactivado para usar assets compilados"
Write-Info "PID cloudflared: $($process.Id)"
Write-Host ""
Write-Host "Siguiente paso recomendado:" -ForegroundColor Green
Write-Host "  php artisan optimize:clear"
