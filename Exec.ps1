[CmdletBinding()]

$ProjectName      = ""
$UiFrontDocker    = $true
$UiAdminDocker    = $true
$Commands         = @()
$EnvVariables     = @{}
$IsInteractive    = $false
$FoundProjectName = $false
$DbEngine         = "mysql"
$HasEnvFile       = Test-Path -Path "./docker/.env"

$dbDsns = @{
	mysql = "mysql:host=db:3306;dbname=zsf"
	sqlsrv = "sqlsrv:Server=db;Database=zsf;Encrypt=0"
	pgsql = "pgsql:host=db;dbname=zsf"
}

$testDbDsns = @{
	mysql = "mysql:host=db:3306;dbname=zsf_test"
	sqlsrv = "sqlsrv:Server=db;Database=zsf_test;Encrypt=0"
	pgsql = "pgsql:host=db;dbname=zsf_test"
}

$dbUsers = @{
	mysql = "root"
	sqlsrv = "sa"
	pgsql = "postgres"
}

$args | ForEach-Object {
	if ($_ -eq "projectname") {
		$FoundProjectName = $true

		return
	}

	if ($_ -eq "-i") {
		$IsInteractive = $true

		return
	}

	if ($FoundProjectName) {
		$ProjectName      = $_
		$FoundProjectName = $false

		return
	}

	$Commands += $_
}

if ($HasEnvFile) {
  Get-Content ./docker/.env | ForEach-Object {
    $name, $value = $_.split('=')

		if (!$EnvVariables.ContainsKey($name)) {
			$EnvVariables.Add($name, $value)
		}
  }
}

function QueryForInput {
	param (
		[Parameter(Mandatory=$True)] [string] $Prompt,
		[string[]] $AllowedValues = @("Y", "n")
	)

	$answers = $AllowedValues | ForEach-Object { $_.ToUpper() }

	do {
		$response = (Read-Host "$Prompt ($($AllowedValues -Join '/'))").Trim().ToUpper()

		if ($answers -contains $response) {
			return $response
		}

		Write-Host "Invalid input, please try again"
	} while ($true)
}

if ([string]::IsNullOrWhiteSpace($ProjectName)) {
	if ($HasEnvFile) {
		if (!$EnvVariables.ContainsKey('PROJECT_NAME')) {
			$ProjectName = Read-Host -Prompt "What is the name of your project"
		} else {
			$ProjectName = $EnvVariables.PROJECT_NAME
		}

		if ($EnvVariables.ContainsKey('UI_FRONT_DOCKER') -and $EnvVariables.UI_FRONT_DOCKER -eq 'False') {
			$UiFrontDocker = $false
		}

		if ($EnvVariables.ContainsKey('UI_ADMIN_DOCKER') -and $EnvVariables.UI_ADMIN_DOCKER -eq 'False') {
			$UiAdminDocker = $false
		}

		if ($EnvVariables.ContainsKey('DB_ENGINE') -and @('mysql', 'sqlsrv', 'pgsql') -contains $EnvVariables.DB_ENGINE.ToLower()) {
			$DbEngine = $EnvVariables.DB_ENGINE.ToLower()
		}
	} else {
		$projectNamePrompt = Read-Host -Prompt "What is the name of your project"

		if ($projectNamePrompt.Length -lt 1) {
			Write-Host "You must enter a project name to continue"

			Exit
		}

		$ProjectName = $projectNamePrompt.ToLower()

		$adminUiPrompt = QueryForInput -Prompt "Use Docker for Admin UI"
		$frontUiPrompt = QueryForInput -Prompt "Use Docker for Front UI"
		$dbEnginePrompt = QueryForInput -Prompt "Which DB engine" -AllowedValues @("mysql", "sqlsrv", "pgsql")

		if ($adminUiPrompt -eq "N") {
			$UiAdminDocker = $False
		}

		if ($frontUiPrompt -eq "N") {
			$UiFrontDocker = $False
		}

		$DbEngine = $dbEnginePrompt.ToLower()
	}
}

$Command      = $Commands -Join " "
$WebContainer = "$ProjectName-web"

function CreateCompose {
	param(
		[string] $DbEngine,
		[bool] $UiAdminDocker,
		[bool] $UiFrontDocker
	)

	$corePath = Join-Path -Path $PSScriptRoot -ChildPath docker/docker-compose-core.yml
	$coreCompose = [System.IO.File]::ReadAllText($corePath, [System.Text.Encoding]::UTF8)

	$includeLines = @("include:")

	switch ($DbEngine) {
		"mysql" { $includeLines += "  - docker-compose-db-mysql.yml" }
		"sqlsrv" { $includeLines += "  - docker-compose-db-sqlsrv.yml" }
		"pgsql" { $includeLines += "  - docker-compose-db-pgsql.yml" }
	}

	if ($UiAdminDocker) {
		$includeLines += "  - docker-compose-ui-admin.yml"
	}

	if ($UiFrontDocker) {
		$includeLines += "  - docker-compose-ui-front.yml"
	}

	$composeContents = @"
$($includeLines -join "`n")

$($coreCompose)
"@

	$composeContents = $composeContents -replace "`r`n", "`n"

	if (Test-Path -Path ./docker/docker-compose.yml) {
		Remove-Item -Path ./docker/docker-compose.yml -Force
	}

	$encoding = New-Object System.Text.UTF8Encoding($false)
	$composePath = Join-Path -Path $PSScriptRoot -ChildPath docker/docker-compose.yml

	[System.IO.File]::WriteAllText($composePath, $composeContents, $encoding)
}

function InitDocker([string] $ProjectName, [string] $WebContainer) {
	$envContents  = @"
PROJECT_NAME=$($ProjectName)
UI_FRONT_DOCKER=$($UiAdminDocker)
UI_ADMIN_DOCKER=$($UiFrontDocker)
DB_ENGINE=$($DbEngine)
"@

	CreateCompose -DbEngine $DbEngine -UiAdminDocker $UiAdminDocker -UiFrontDocker $UiFrontDocker

	Push-Location ./docker
	$envContents | Out-File -FilePath .env
	docker compose -f docker-compose.yml -p $ProjectName up -d
	Pop-Location

	Write-Host "Docker container build complete"

	Write-Host "Waiting 30s to let docker do its thang.. " -NoNewline
	Start-Sleep -Seconds 30
	Write-Host "DONE"

	$DbContainer = "$ProjectName-db"

	switch ($DbEngine) {
		"mysql" { Invoke-Expression "docker exec -i $DbContainer sh -c `"mysql -u root -p'P@55word' < /docker-entrypoint-initdb.d/mysql-init.sql`""	}
		"sqlsrv" { Invoke-Expression "docker exec -it $DbContainer /opt/mssql-tools/bin/sqlcmd -S localhost -U sa -P 'P@55word' -i /docker-entrypoint-initdb.d/sqlsrv-init.sql" }
		"pgsql" { Invoke-Expression "docker exec -it $DbContainer export PGPASSWORD='P@55word' psql -h localhost -U postgres -c `"CREATE DATABASE zsf_test`"" }
	}

	Write-Host "Initializing container.."

	if (!(Test-Path -Path siteSettings.json)) {
 		docker exec -t $WebContainer cp docker/siteSettings.json siteSettings.json
	}

	if (Test-Path -Path migrations/db) {
		Remove-Item migrations/db -Recurse -Force
	}

	Copy-Item -Path "migrations/db.$DbEngine" "migrations/db" -Recurse

	docker exec -it $WebContainer composer update

	docker exec -it $WebContainer php vendor/bin/stoic-configure -PdbDsn="$($testDbDsns[$DbEngine])" -PdbUser="$($dbUsers[$DbEngine])"
	docker exec -it $WebContainer php vendor/bin/stoic-migrate up

	docker exec -it $WebContainer php vendor/bin/stoic-configure -PdbDsn="$($dbDsns[$DbEngine])" -PdbUser="$($dbUsers[$DbEngine])"
	docker exec -it $WebContainer php vendor/bin/stoic-migrate up

	Write-Host "Docker container initialized`n"

	Write-Host "Configuring admin UI.. " -NoNewline
	cp ./docker/admin-config.json ./ui/admin/public/config.json
	Write-Host "DONE"

	Write-Host "Configuring front UI.. " -NoNewline
	cp ./docker/front-config.json ./ui/front/public/config.json
	Write-Host "DONE"

	if (!$UiAdminDocker) {
		Write-Host "Starting admin UI.. " -NoNewline

		Push-Location ./ui/admin
		pnpm install
		Start-Job -Name ZsfUiAdmin -ScriptBlock { Invoke-Expression "pnpm dev" }
		Pop-Location

		Write-Host "DONE"
	}

	if (!$UiFrontDocker) {
		Write-Host "Starting front UI.. " -NoNewline

		Push-Location ./ui/front
		pnpm install
		Start-Job -Name ZsfUiFront -ScriptBlock { Invoke-Expression "pnpm dev" }
		Pop-Location

		Write-Host "DONE"
	}
}

function UpdateDocker([string] $ProjectName, [string] $WebContainer) {
	Write-Host "Updating container for '$ProjectName'.."

	docker exec -it $WebContainer php vendor/bin/stoic-configure -PdbDsn="$($testDbDsns[$DbEngine])" -PdbUser="$($dbUsers[$DbEngine])"
	docker exec -it $WebContainer php vendor/bin/stoic-migrate up

	docker exec -it $WebContainer php vendor/bin/stoic-configure -PdbDsn="$($dbDsns[$DbEngine])" -PdbUser="$($dbUsers[$DbEngine])"
	docker exec -it $WebContainer php vendor/bin/stoic-migrate up

	Write-Host "Finished updating"
}

function StopDocker([string] $ProjectName) {
	Write-Host "Stopping docker container.. "

	Push-Location docker
	docker compose -p $ProjectName stop
	Pop-Location

	Write-Host "Docker container stopped"

	if (!$UiAdminDocker) {
		Write-Host "Stopping admin UI.. " -NoNewline

		Stop-Job -Name ZsfUiAdmin

		Write-Host "DONE"
	}

	if (!$UiFrontDocker) {
		Write-Host "Stopping front UI.. " -NoNewline

		Stop-Job -Name ZsfUiFront

		Write-Host "DONE"
	}
}

function DownDocker([string] $ProjectName) {
	Write-Host "Removing docker container.. "

	Push-Location docker
	docker compose -p $ProjectName down
	Pop-Location

	Write-Host "Docker container removed"

	if (!$UiAdminDocker) {
		Write-Host "Stopping admin UI.. " -NoNewline

		Stop-Job -Name ZsfUiAdmin

		Write-Host "DONE"
	}

	if (!$UiFrontDocker) {
		Write-Host "Stopping front UI.. " -NoNewline

		Stop-Job -Name ZsfUiFront

		Write-Host "DONE"
	}

	Remove-Item -Path docker/.env -Force
}

Write-Host "Executing command on '$ProjectName' project: $Command"

if ($Command -eq "init") {
	InitDocker -ProjectName $ProjectName -WebContainer $WebContainer
} elseif ($Command -eq "update") {
	UpdateDocker -ProjectName $ProjectName -WebContainer $WebContainer
} elseif ($Command -eq "stop") {
	StopDocker -ProjectName $ProjectName
} elseif ($Command -eq "down") {
	DownDocker -ProjectName $ProjectName
} else {
	$opts = "t"

	if ($IsInteractive) {
		$opts += "i"
	}

	Invoke-Expression "docker exec -$opts $WebContainer $Command"
}
