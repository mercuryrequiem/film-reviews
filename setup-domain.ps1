param(
    [string]$XamppRoot = 'C:\xampp',
    [string]$HostsFile = "$env:SystemRoot\System32\drivers\etc\hosts"
)

$ErrorActionPreference = 'Stop'
$siteName = 'film-reviews.local'
$apacheRoot = Join-Path $XamppRoot 'apache'
$httpd = Join-Path $apacheRoot 'bin\httpd.exe'
$confFile = Join-Path $apacheRoot 'conf\httpd.conf'
$documentRoot = (Join-Path $PSScriptRoot 'public').Replace('\', '/')
$marker = "# BEGIN lab2 $siteName"

$byteEncoding = [Text.Encoding]::GetEncoding(28591)
$utf8 = New-Object Text.UTF8Encoding($false)

function Get-ApacheConfiguration([string]$File) {
    $ErrorActionPreference = 'Continue'
    $PSNativeCommandUseErrorActionPreference = $false
    $lines = & $httpd -d ($apacheRoot.Replace('\', '/')) -f ($File.Replace('\', '/')) -S 2>&1
    $output = ($lines | ForEach-Object { $_.ToString() }) -join "`n"
    if ($LASTEXITCODE -ne 0) {
        throw "Apache configuration check failed:`n$output"
    }
    return $output
}

try {
    if (!(Test-Path -LiteralPath $httpd -PathType Leaf)) {
        throw "Apache was not found at $httpd. For a different XAMPP folder, pass -XamppRoot."
    }
    if (!(Test-Path -LiteralPath (Join-Path $documentRoot 'index.php') -PathType Leaf)) {
        throw "The project's public/index.php is missing."
    }

    $originalConf = [IO.File]::ReadAllBytes($confFile)
    $originalHosts = [IO.File]::ReadAllBytes($HostsFile)
    $confText = $byteEncoding.GetString($originalConf)
    $hostsText = $byteEncoding.GetString($originalHosts)
    if ($hostsText.Contains([char]0)) {
        throw 'The hosts file must be saved as plain text or UTF-8.'
    }
    $currentConfiguration = Get-ApacheConfiguration $confFile

    $siteBlock = @"
$marker
<VirtualHost *:80>
    ServerName $siteName
    DocumentRoot "$documentRoot"
    <Directory "$documentRoot">
        Require all granted
    </Directory>
</VirtualHost>
# END lab2 $siteName
"@
    $siteBlock = $siteBlock -replace '\r?\n', "`r`n"
    $encodedBlock = $byteEncoding.GetString($utf8.GetBytes($siteBlock))
    $alreadyConfigured = ($confText -replace '\r?\n', "`r`n").Contains($encodedBlock)
    if (!$alreadyConfigured -and $confText.Contains($marker)) {
        throw "$siteName was already set up for another folder or its configuration was edited."
    }
    if (!$alreadyConfigured) {
        $escapedName = [regex]::Escape($siteName)
        if ($currentConfiguration -match "(?im)^.*(?:\s|^)$escapedName(?=\s|$)") {
            throw "Apache already uses $siteName. No files were changed."
        }
        if ($currentConfiguration -match '(?m)^\s*127\.0\.0\.1:80\s') {
            throw 'Apache already uses an IP-specific virtual host on 127.0.0.1:80. Add the domain manually to that configuration.'
        }
    }

    $hasHostsEntry = $false
    foreach ($line in ($hostsText -split '\r?\n')) {
        $fields = (($line -split '#', 2)[0]).Trim() -split '\s+'
        if ($fields.Count -gt 1 -and $fields[1..($fields.Count - 1)] -contains $siteName) {
            if ($fields[0] -ne '127.0.0.1') {
                throw "The hosts file already maps $siteName to $($fields[0]). No files were changed."
            }
            $hasHostsEntry = $true
        }
    }

    $newConf = $originalConf
    if (!$alreadyConfigured) {
        $addition = $siteBlock
        if ($currentConfiguration -notmatch '(?m)^\s*\*:80\s') {
            if ($currentConfiguration -notmatch '(?m)^Main DocumentRoot: "([^"]+)"') {
                throw 'Could not determine the existing Apache document root.'
            }
            $mainRoot = $Matches[1]
            $addition = "<VirtualHost *:80>`r`n    ServerName localhost`r`n    DocumentRoot `"$mainRoot`"`r`n</VirtualHost>`r`n`r`n$siteBlock"
        }
        $newConf = $originalConf + $utf8.GetBytes("`r`n`r`n$addition`r`n")

        $candidate = Join-Path (Split-Path -Parent $confFile) ('.lab2-' + [guid]::NewGuid() + '.tmp')
        try {
            [IO.File]::WriteAllBytes($candidate, $newConf)
            $candidateConfiguration = Get-ApacheConfiguration $candidate
            foreach ($alias in [regex]::Matches($candidateConfiguration, '(?im)^\s*(?:wild )?alias\s+(\S+)')) {
                if ($siteName -like $alias.Groups[1].Value) {
                    throw "An existing Apache alias already matches $siteName. No files were changed."
                }
            }
        } finally {
            if (Test-Path -LiteralPath $candidate) {
                Remove-Item -LiteralPath $candidate
            }
        }
    }

    $wroteConf = $false
    try {
        if (!$alreadyConfigured) {
            [IO.File]::WriteAllBytes($confFile, $newConf)
            $wroteConf = $true
        }
        if (!$hasHostsEntry) {
            [IO.File]::WriteAllBytes($HostsFile, ($originalHosts + $utf8.GetBytes("`r`n127.0.0.1 $siteName`r`n")))
        }
    } catch {
        if ($wroteConf) {
            [IO.File]::WriteAllBytes($confFile, $originalConf)
        }
        throw
    }

    Write-Host "Ready. Restart Apache in XAMPP Control Panel and open http://$siteName/"
} catch {
    Write-Host "ERROR: $($_.Exception.Message)" -ForegroundColor Red
    if ($_.Exception -is [UnauthorizedAccessException] -or $_.Exception.InnerException -is [UnauthorizedAccessException]) {
        Write-Host 'Right-click setup-domain.bat and choose Run as administrator.'
    }
    exit 1
}
