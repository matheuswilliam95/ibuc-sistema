param(
    [ValidateSet('Start', 'Prepare', 'Test', 'Serve', 'Stop')]
    [string]$Action = 'Start',
    [string]$PostgresBin = "$env:LOCALAPPDATA/IBUC/postgresql-17.12/pgsql/bin",
    [int]$Port = 55432,
    [int]$HttpPort = 8001
)

$ErrorActionPreference = 'Stop'
$taskRuntime = Join-Path $env:LOCALAPPDATA 'IBUC/structure-development'
$taskData = Join-Path $taskRuntime 'data'
$taskCredentials = Join-Path $taskRuntime 'credentials.xml'
$taskApp = Split-Path $PSScriptRoot -Parent
if ($Port -lt 1024 -or $Port -gt 65535) { throw 'Escolha uma porta local entre 1024 e 65535.' }
if (-not (Test-Path -LiteralPath (Join-Path $PostgresBin 'pg_ctl.exe'))) {
    throw 'Informe -PostgresBin com os binários PostgreSQL 17.12. Consulte docs/organizacao/T06-ESTRUTURA.md.'
}
New-Item -ItemType Directory -Path $taskRuntime -Force | Out-Null
# Restrict the runtime to this Windows account. Credentials use Windows DPAPI.
$taskIdentity = [System.Security.Principal.WindowsIdentity]::GetCurrent().Name
& icacls.exe $taskRuntime '/inheritance:r' '/grant:r' "${taskIdentity}:(OI)(CI)F" | Out-Null
if ($LASTEXITCODE -ne 0) { throw 'Não foi possível restringir o diretório local.' }

function Invoke-Pg([string]$Name, [string[]]$Arguments) {
    & (Join-Path $PostgresBin "$Name.exe") @Arguments
    if ($LASTEXITCODE -ne 0) { throw "Falha do PostgreSQL: $Name." }
}

if ($Action -eq 'Stop') {
    Invoke-Pg 'pg_ctl' @('-D', $taskData, 'stop', '-m', 'fast', '-w')
    exit
}

if (-not (Test-Path -LiteralPath $taskCredentials)) {
    if (Test-Path -LiteralPath (Join-Path $taskData 'PG_VERSION')) { throw 'Cluster existente sem custódia local; não redefinir suas credenciais.' }
    $taskSecrets = @{}
    foreach ($role in @('ibuc_local_admin', 'ibuc_structure_demo', 'ibuc_structure_test')) {
        $plain = [Convert]::ToBase64String([System.Security.Cryptography.RandomNumberGenerator]::GetBytes(36))
        $taskSecrets[$role] = [pscredential]::new($role, (ConvertTo-SecureString $plain -AsPlainText -Force))
    }
    $taskSecrets | Export-Clixml -LiteralPath $taskCredentials
}
$taskSecrets = Import-Clixml -LiteralPath $taskCredentials

if (-not (Test-Path -LiteralPath (Join-Path $taskData 'PG_VERSION'))) {
    $taskPwFile = Join-Path $taskRuntime 'initdb-password.tmp'
    try {
        [IO.File]::WriteAllText($taskPwFile, $taskSecrets['ibuc_local_admin'].GetNetworkCredential().Password)
        Invoke-Pg 'initdb' @('-D', $taskData, '-U', 'ibuc_local_admin', '--auth=scram-sha-256', '--encoding=UTF8', '--locale=C', "--pwfile=$taskPwFile")
    } finally {
        if (Test-Path -LiteralPath $taskPwFile) { Remove-Item -LiteralPath $taskPwFile }
    }
}

& (Join-Path $PostgresBin 'pg_ctl.exe') '-D' $taskData 'status' *> $null
if ($LASTEXITCODE -ne 0) {
    Invoke-Pg 'pg_ctl' @('-D', $taskData, '-l', (Join-Path $taskRuntime 'postgres.log'), '-o', "-h 127.0.0.1 -p $Port", 'start', '-w')
}
$env:PGPASSWORD = $taskSecrets['ibuc_local_admin'].GetNetworkCredential().Password
try {
    $taskPgArgs = @('-h', '127.0.0.1', '-p', "$Port", '-U', 'ibuc_local_admin', '-d', 'postgres', '-v', 'ON_ERROR_STOP=1', '-tAc')
    $taskServerData = & (Join-Path $PostgresBin 'psql.exe') @taskPgArgs 'SHOW data_directory'
    if ($LASTEXITCODE -ne 0 -or [IO.Path]::GetFullPath($taskServerData) -ne [IO.Path]::GetFullPath($taskData)) {
        throw 'A conexão não corresponde ao cluster dedicado. Nenhuma base será preparada.'
    }
    foreach ($role in @('ibuc_structure_demo', 'ibuc_structure_test')) {
        $present = & (Join-Path $PostgresBin 'psql.exe') @taskPgArgs "SELECT 1 FROM pg_roles WHERE rolname = '$role'"
        if ($LASTEXITCODE -ne 0) { throw 'Não foi possível conferir o cluster dedicado.' }
        if ($present -ne '1') {
            $taskRolePassword = $taskSecrets[$role].GetNetworkCredential().Password
            # Password is sent over stdin, never in argv, output or a repository file.
            $taskRoleOutput = "CREATE ROLE $role LOGIN PASSWORD '$taskRolePassword' NOSUPERUSER NOCREATEDB NOCREATEROLE;" | & (Join-Path $PostgresBin 'psql.exe') @($taskPgArgs[0..9]) 2>&1
            if ($LASTEXITCODE -ne 0) { throw 'Falha ao criar papel local.' }
        }
        $present = & (Join-Path $PostgresBin 'psql.exe') @taskPgArgs "SELECT 1 FROM pg_database WHERE datname = '$role'"
        if ($LASTEXITCODE -ne 0) { throw 'Falha ao conferir base local.' }
        if ($present -ne '1') { Invoke-Pg 'createdb' @('-h', '127.0.0.1', '-p', "$Port", '-U', 'ibuc_local_admin', '-O', $role, $role) }
        # Prevent application/test roles from connecting to one another's database.
        Invoke-Pg 'psql' @($taskPgArgs[0..9] + @('-c', "REVOKE CONNECT ON DATABASE $role FROM PUBLIC; GRANT CONNECT ON DATABASE $role TO $role;"))
    }
} finally { Remove-Item Env:PGPASSWORD -ErrorAction SilentlyContinue }

Write-Host "PostgreSQL dedicado: 127.0.0.1:$Port; bases demo/test separadas; dados em $taskRuntime."
if ($Action -eq 'Start') { exit }
Set-Location -LiteralPath $taskApp
$taskDatabase = if ($Action -eq 'Test') { 'ibuc_structure_test' } else { 'ibuc_structure_demo' }
$env:APP_ENV = if ($Action -eq 'Test') { 'testing' } else { 'local' }
$env:DB_URL = ''
$env:DB_CONNECTION = 'pgsql'
$env:DB_HOST = '127.0.0.1'
$env:DB_PORT = "$Port"
$env:DB_DATABASE = $taskDatabase
$env:DB_USERNAME = $taskDatabase
$env:DB_PASSWORD = $taskSecrets[$taskDatabase].GetNetworkCredential().Password
$env:DB_SSLMODE = 'disable'
$env:CACHE_STORE = 'array'
$env:QUEUE_CONNECTION = 'sync'
$env:SESSION_DRIVER = if ($Action -eq 'Test') { 'array' } else { 'file' }
$env:IBUC_STRUCTURE_DEMO = 'true'
$env:MAIL_MAILER = 'array'
if (Test-Path -LiteralPath 'bootstrap/cache/config.php') { throw 'Remova o cache de configuração local antes de usar o ambiente isolado.' }
if ($Action -eq 'Test') {
    $env:IBUC_POSTGRES_TESTS = '1'
    & php artisan test
} elseif ($Action -eq 'Prepare') {
    & php artisan migrate --force
    if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
    & php artisan db:seed --class=StructureDemoSeeder --force
} else {
    & php artisan serve --host=127.0.0.1 "--port=$HttpPort" --no-reload
}
exit $LASTEXITCODE
