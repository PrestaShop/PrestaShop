<#
.SYNOPSIS
    Rebuilds all the static assets, running npm ci as needed.

.EXAMPLE
    .\tools\assets\build.ps1
    .\tools\assets\build.ps1 front-classic
    .\tools\assets\build.ps1 all -Force
#>
[CmdletBinding()]
param(
    [ValidateSet('admin-default', 'admin-new-theme', 'front-core', 'front-classic', 'front-hummingbird', 'all')]
    [string] $Asset = 'all',

    # Force rebuild even if assets already exist
    [switch] $Force,

    # Internal: used when the script re-invokes itself for a single asset, to avoid duplicated summaries
    [switch] $NoSummary
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

$ProjectPath = (Resolve-Path (Join-Path $PSScriptRoot '..\..')).Path
$AdminDir = if ($env:ADMIN_DIR) { $env:ADMIN_DIR } else { 'admin-dev' }
$AdminPath = Join-Path $ProjectPath $AdminDir

if (-not (Test-Path -LiteralPath $AdminPath -PathType Container)) {
    throw "Could not find directory '$AdminPath'. Make sure to launch this script from the root directory of PrestaShop"
}

# Asset name -> folder to build in + file whose presence means "already built"
$Assets = [ordered]@{
    'admin-default'     = @{
        Label  = 'admin default theme'
        Path   = Join-Path $AdminPath 'themes\default'
        Marker = Join-Path $AdminPath 'themes\default\public\theme.css'
    }
    'admin-new-theme'   = @{
        Label  = 'admin new theme'
        Path   = Join-Path $AdminPath 'themes\new-theme'
        Marker = Join-Path $AdminPath 'themes\new-theme\public\theme.css'
    }
    'front-core'        = @{
        Label  = 'core theme assets'
        Path   = Join-Path $ProjectPath 'themes'
        Marker = Join-Path $ProjectPath 'themes\core.js'
    }
    'front-classic'     = @{
        Label  = 'classic theme assets'
        Path   = Join-Path $ProjectPath 'themes\classic\_dev'
        Marker = Join-Path $ProjectPath 'themes\classic\assets\css\theme.css'
    }
    'front-hummingbird' = @{
        Label  = 'hummingbird theme assets'
        Path   = Join-Path $ProjectPath 'themes\hummingbird'
        Marker = Join-Path $ProjectPath 'themes\hummingbird\assets\css\theme.css'
    }
}

function Invoke-Npm {
    # npm writes its warnings on stderr. In a background job Windows PowerShell turns every
    # stderr line into a NativeCommandError ErrorRecord, which $ErrorActionPreference = 'Stop'
    # would promote to a terminating error. Merge the streams and rely on the exit code only.
    param([Parameter(Mandatory, ValueFromRemainingArguments)] [string[]] $NpmArguments)

    $previous = $ErrorActionPreference
    $ErrorActionPreference = 'Continue'
    try {
        & npm @NpmArguments 2>&1 | ForEach-Object {
            # stderr lines come back as ErrorRecord; an empty one would stringify as its exception type name
            $line = if ($_ -is [System.Management.Automation.ErrorRecord]) { $_.Exception.Message } else { "$_" }
            if ($line.Trim()) { Write-Host $line }
        }
    }
    finally {
        $ErrorActionPreference = $previous
    }

    return $LASTEXITCODE
}

function Invoke-NpmBuild {
    param([Parameter(Mandatory)] [string] $Path)

    if (-not (Test-Path -LiteralPath $Path -PathType Container)) {
        throw "$Path folder not found"
    }

    Push-Location -LiteralPath $Path
    $lock = Join-Path $Path 'buildLock'
    try {
        $modules = Join-Path $Path 'node_modules'
        if (Test-Path -LiteralPath $modules) {
            Remove-Item -LiteralPath $modules -Recurse -Force
        }

        New-Item -ItemType File -Path $lock -Force | Out-Null

        $code = Invoke-Npm ci
        if ($code -ne 0) { throw "npm ci failed in $Path (exit code $code)" }

        $code = Invoke-Npm run build
        if ($code -ne 0) { throw "npm run build failed in $Path (exit code $code)" }
    }
    finally {
        if (Test-Path -LiteralPath $lock) { Remove-Item -LiteralPath $lock -Force }
        Pop-Location
    }
}

function Build-Asset {
    param([Parameter(Mandatory)] [string] $Name)

    $definition = $Assets[$Name]
    if (-not $Force -and (Test-Path -LiteralPath $definition.Marker)) {
        Write-Host "> $($definition.Label) already exists (use -Force to rebuild)"
        return
    }

    Write-Host ">>> Building $($definition.Label)..."
    Invoke-NpmBuild -Path $definition.Path
}

if ($Asset -ne 'all') {
    Build-Asset -Name $Asset
    if (-not $NoSummary) { Write-Host 'All done!' }
    return
}

# Build everything in parallel, one background job per asset
$jobs = foreach ($name in $Assets.Keys) {
    Start-Job -Name $name -ArgumentList $PSCommandPath, $name, $Force.IsPresent -ScriptBlock {
        param($script, $name, $force)
        & $script $name -NoSummary -Force:$force
    }
}

$failed = @()
foreach ($job in $jobs) {
    Receive-Job -Job $job -Wait -ErrorAction Continue
    if ($job.State -ne 'Completed') {
        $failed += $job.Name
        $reason = $job.ChildJobs[0].JobStateInfo.Reason
        if ($reason) { Write-Host "!!! $($job.Name): $($reason.Message)" }
    }
    Remove-Job -Job $job
}

if ($failed.Count -gt 0) {
    throw "Build failed for: $($failed -join ', ')"
}

Write-Host 'All done!'
