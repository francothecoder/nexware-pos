$ErrorActionPreference = 'Stop'

$appRoot = Split-Path -Parent $PSScriptRoot
Push-Location $appRoot

try {
    $script:HasFailures = $false
    $script:HasWarnings = $false

    function Write-Result {
        param(
            [Parameter(Mandatory = $true)][string]$Name,
            [Parameter(Mandatory = $true)][bool]$Passed,
            [Parameter(Mandatory = $true)][string]$Detail,
            [switch]$Warning
        )

        if ($Passed) {
            Write-Host "[PASS] $Name - $Detail" -ForegroundColor Green
        } elseif ($Warning) {
            $script:HasWarnings = $true
            Write-Host "[WARN] $Name - $Detail" -ForegroundColor Yellow
        } else {
            $script:HasFailures = $true
            Write-Host "[FAIL] $Name - $Detail" -ForegroundColor Red
        }
    }

    function Get-CommandVersion {
        param([Parameter(Mandatory = $true)][string]$Name, [string[]]$Arguments = @('--version'))

        $command = Get-Command $Name -ErrorAction SilentlyContinue
        if (-not $command) {
            return $null
        }

        $output = & $command.Source @Arguments 2>&1 | Out-String
        return $output.Trim()
    }

    Write-Host 'Nexware POS local environment check' -ForegroundColor Cyan
    Write-Host "Project: $appRoot"

    $phpVersion = Get-CommandVersion -Name 'php' -Arguments @('-r', 'echo PHP_VERSION;')
    $phpVersionMatch = [regex]::Match([string]$phpVersion, '\d+\.\d+\.\d+')
    $phpOkay = $phpVersionMatch.Success -and ([version]$phpVersionMatch.Value -ge [version]'8.3.0')
    Write-Result 'PHP 8.3+' $phpOkay $(if ($phpVersion) { $phpVersion } else { 'php is not on PATH' })

    foreach ($tool in @('composer', 'node', 'npm', 'git')) {
        $version = Get-CommandVersion -Name $tool
        Write-Result $tool ([bool]$version) $(if ($version) { ($version -split "`r?`n")[0] } else { "$tool is not on PATH" })
    }

    $nodeVersionText = Get-CommandVersion -Name 'node' -Arguments @('-p', 'process.versions.node')
    $nodeMatch = [regex]::Match([string]$nodeVersionText, '^(\d+)\.(\d+)\.(\d+)')
    $nodeOkay = $false
    if ($nodeMatch.Success) {
        $nodeMajor = [int]$nodeMatch.Groups[1].Value
        $nodeMinor = [int]$nodeMatch.Groups[2].Value
        $nodeOkay = ($nodeMajor -eq 20 -and $nodeMinor -ge 19) -or ($nodeMajor -eq 22 -and $nodeMinor -ge 12) -or $nodeMajor -ge 23
    }
    Write-Result 'Node.js for Vite 8' $nodeOkay $(if ($nodeVersionText) { "Node.js $nodeVersionText; require 20.19+ or 22.12+" } else { 'node is not on PATH' })

    $phpModules = @()
    if (Get-Command php -ErrorAction SilentlyContinue) {
        $phpModules = & php -m 2>&1
    }
    foreach ($extension in @('openssl', 'mbstring', 'fileinfo', 'zip', 'pdo_sqlite', 'sqlite3')) {
        $found = $phpModules | Where-Object { "$_".Trim() -ieq $extension }
        Write-Result "PHP extension $extension" ([bool]$found) $(if ($found) { 'enabled' } else { 'missing or PHP is unavailable' })
    }

    $envPath = Join-Path $appRoot '.env'
    $examplePath = Join-Path $appRoot '.env.example'
    Write-Result '.env file' (Test-Path $envPath) $(if (Test-Path $envPath) { 'present; contents were not displayed' } elseif (Test-Path $examplePath) { 'missing; .env.example is available' } else { '.env and .env.example are missing' }) -Warning:(-not (Test-Path $envPath))
    if (Test-Path $envPath) {
        $envText = Get-Content $envPath -Raw
        $keyMatch = [regex]::Match($envText, '(?m)^\s*APP_KEY\s*=\s*(.*?)\s*$')
        Write-Result 'APP_KEY configured' ($keyMatch.Success -and -not [string]::IsNullOrWhiteSpace($keyMatch.Groups[1].Value)) $(if ($keyMatch.Success -and -not [string]::IsNullOrWhiteSpace($keyMatch.Groups[1].Value)) { 'present; value not displayed' } else { 'missing or empty; setup-local.ps1 generates only an initial key' }) -Warning:(-not $keyMatch.Success -or [string]::IsNullOrWhiteSpace($keyMatch.Groups[1].Value))

        $dbMatch = [regex]::Match($envText, '(?m)^\s*DB_CONNECTION\s*=\s*([A-Za-z0-9_-]+)')
        Write-Result 'Configured database driver' ($dbMatch.Success) $(if ($dbMatch.Success) { "DB_CONNECTION=$($dbMatch.Groups[1].Value.Trim())" } else { 'DB_CONNECTION is not explicitly set; Laravel defaults to SQLite' }) -Warning:(-not $dbMatch.Success)

        $appIdMatch = [regex]::Match($envText, '(?m)^\s*NATIVEPHP_APP_ID\s*=\s*(\S+)')
        Write-Result 'NativePHP app ID configured' ($appIdMatch.Success) $(if ($appIdMatch.Success) { 'present; value not displayed or changed' } else { 'missing; preserve the existing app identifier when configuring it' }) -Warning:(-not $appIdMatch.Success)
    }

    $databasePath = Join-Path $appRoot 'database\database.sqlite'
    Write-Result 'SQLite database file' (Test-Path $databasePath) $(if (Test-Path $databasePath) { 'present; data was not read or modified' } else { 'missing; do not create or migrate until DB_CONNECTION is confirmed as sqlite' }) -Warning:(-not (Test-Path $databasePath))

    $git = Get-Command git -ErrorAction SilentlyContinue
    if ($git) {
        $branch = & git branch --show-current 2>$null
        Write-Result 'Git branch' ([bool]$branch) $(if ($branch) { $branch } else { 'not inside a Git checkout' }) -Warning:(-not [bool]$branch)

        $remoteNames = & git remote 2>$null
        Write-Result 'Git remote' ([bool]$remoteNames) $(if ($remoteNames) { ($remoteNames -join ', ') } else { 'no remotes configured' }) -Warning:(-not [bool]$remoteNames)

        $gitStatus = & git status --porcelain 2>$null
        Write-Result 'Git worktree' ($LASTEXITCODE -eq 0) $(if ($LASTEXITCODE -ne 0) { 'could not read status' } elseif (-not $gitStatus) { 'clean' } else { "$($gitStatus.Count) changed/untracked path(s); review before switching branches" }) -Warning:([bool]$gitStatus)

        $trackedEnv = & git ls-files --error-unmatch .env 2>$null
        Write-Result '.env excluded from Git' ($LASTEXITCODE -ne 0) $(if ($LASTEXITCODE -ne 0) { 'not tracked' } else { 'WARNING: .env is tracked; remove it from Git history only with careful review' })

        $ignoredEnv = & git check-ignore .env 2>$null
        Write-Result '.env ignore rule' ($LASTEXITCODE -eq 0) $(if ($LASTEXITCODE -eq 0) { 'ignored' } else { 'no matching ignore rule' }) -Warning:($LASTEXITCODE -ne 0)

        $ignoredDatabase = & git check-ignore database/database.sqlite 2>$null
        Write-Result 'SQLite data excluded from Git' ($LASTEXITCODE -eq 0) $(if ($LASTEXITCODE -eq 0) { 'ignored' } else { 'database/database.sqlite has no matching ignore rule' }) -Warning:($LASTEXITCODE -ne 0)

        $ignoredNativeFiles = & git check-ignore nativephp/android/app/release-key.keystore 2>$null
        Write-Result 'NativePHP build/signing files excluded from Git' ($LASTEXITCODE -eq 0) $(if ($LASTEXITCODE -eq 0) { 'nativephp build directory is ignored' } else { 'verify ignore rules for signing keys and generated Android files' }) -Warning:($LASTEXITCODE -ne 0)
    } else {
        Write-Result 'Git repository checks' $false 'Git is not on PATH' -Warning
    }

    $javaVersion = Get-CommandVersion -Name 'java' -Arguments @('-version')
    $javaMatch = [regex]::Match([string]$javaVersion, 'version "?(?<major>\d+)')
    $javaOkay = $javaMatch.Success -and ([int]$javaMatch.Groups['major'].Value -eq 17)
    Write-Result 'Java JDK 17' $javaOkay $(if ($javaVersion) { ($javaVersion -split "`r?`n")[0] } else { 'java is not on PATH' }) -Warning

    $androidHome = $env:ANDROID_HOME
    if (-not $androidHome) {
        $androidHome = $env:ANDROID_SDK_ROOT
    }
    $adbPath = if ($androidHome) { Join-Path $androidHome 'platform-tools\adb.exe' } else { $null }
    $adbAvailable = $adbPath -and (Test-Path $adbPath)
    Write-Result 'Android SDK platform-tools' $adbAvailable $(if ($adbAvailable) { $adbPath } else { 'ANDROID_HOME/ANDROID_SDK_ROOT or platform-tools\adb.exe is missing' }) -Warning
    if ($adbAvailable) {
        $adbDevices = & $adbPath devices 2>&1 | Out-String
        Write-Result 'ADB device connection' ($adbDevices -match '(?m)^.+\s+device\s*$') $(if ($adbDevices -match '(?m)^.+\s+device\s*$') { 'an authorized device or running emulator is connected' } else { 'no authorized Android device/emulator; connect one before native:run' }) -Warning
    }

    $sdkPlatforms = if ($androidHome) { Join-Path $androidHome 'platforms\android-36' } else { $null }
    Write-Result 'Android SDK Platform 36' ($sdkPlatforms -and (Test-Path $sdkPlatforms)) $(if ($sdkPlatforms -and (Test-Path $sdkPlatforms)) { 'installed' } else { 'not found; the checked-in Android configuration compiles against API 36' }) -Warning

    $sdkBuildTools = if ($androidHome) { Join-Path $androidHome 'build-tools' } else { $null }
    $hasBuildTools = $sdkBuildTools -and (Test-Path $sdkBuildTools) -and (Get-ChildItem $sdkBuildTools -Directory -ErrorAction SilentlyContinue | Select-Object -First 1)
    Write-Result 'Android SDK Build-Tools' ([bool]$hasBuildTools) $(if ($hasBuildTools) { 'at least one Build-Tools version is installed' } else { 'no Build-Tools version found' }) -Warning

    $ndkPath = if ($androidHome) { Join-Path $androidHome 'ndk\27.0.12077973' } else { $null }
    Write-Result 'Android NDK 27.0.12077973' ($ndkPath -and (Test-Path $ndkPath)) $(if ($ndkPath -and (Test-Path $ndkPath)) { 'installed' } else { 'not found; required by the checked-in Android Gradle configuration' }) -Warning

    $studioPaths = @(
        (Join-Path $env:ProgramFiles 'Android\Android Studio\bin\studio64.exe'),
        (Join-Path $env:LOCALAPPDATA 'Programs\Android Studio\bin\studio64.exe')
    )
    $studioInstalled = $studioPaths | Where-Object { $_ -and (Test-Path $_) } | Select-Object -First 1
    $studioVersionOkay = $false
    $studioVersionText = $null
    if ($studioInstalled) {
        $studioRoot = Split-Path (Split-Path $studioInstalled -Parent) -Parent
        $productInfoPath = Join-Path $studioRoot 'lib\product-info.json'
        if (Test-Path $productInfoPath) {
            $studioVersionText = (Get-Content $productInfoPath -Raw | ConvertFrom-Json).version
        }

        $studioVersionMatch = [regex]::Match([string]$studioVersionText, '^(?<version>\d{4}\.\d+\.\d+)')
        $studioVersionOkay = $studioVersionMatch.Success -and ([version]$studioVersionMatch.Groups['version'].Value -ge [version]'2024.2.1')
    }
    Write-Result 'Android Studio 2024.2.1+' $studioVersionOkay $(if ($studioVersionText) { "version $studioVersionText" } elseif ($studioInstalled) { "$studioInstalled; version could not be detected" } else { 'not found in common Windows install locations' }) -Warning

    $sevenZip = Get-Command '7z' -ErrorAction SilentlyContinue
    $sevenZipInstalled = [bool]$sevenZip -or (Test-Path (Join-Path $env:ProgramFiles '7-Zip\7z.exe'))
    Write-Result '7-Zip' $sevenZipInstalled $(if ($sevenZipInstalled) { 'available; required by NativePHP on Windows' } else { 'not found; required by NativePHP on Windows' }) -Warning

    if ($script:HasFailures) {
        Write-Host 'One or more required local-development checks failed.' -ForegroundColor Red
        exit 1
    }

    if ($script:HasWarnings) {
        Write-Host 'Core Laravel checks passed; review Android/environment warnings before building.' -ForegroundColor Yellow
    } else {
        Write-Host 'All checked requirements passed.' -ForegroundColor Green
    }
} finally {
    Pop-Location
}
