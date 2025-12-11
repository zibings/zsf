[CmdletBinding()]

$ProjectName      = ""
$UiFrontDocker    = $false
$UiAdminDocker    = $false
$SmtpDocker       = $true
$Commands         = @()
$EnvVariables     = @{}
$IsInteractive    = $false
$FoundProjectName = $false
$DbEngine         = "mysql"
$HasEnvFile       = Test-Path -Path "./docker/.env"

$dbDsns = @{
	mysql  = "mysql:host=db:3306;dbname=zsf"
	sqlsrv = "sqlsrv:Server=db;Database=zsf;Encrypt=0"
	pgsql  = "pgsql:host=db;dbname=zsf"
}

$testDbDsns = @{
	mysql  = "mysql:host=db:3306;dbname=zsf_test"
	sqlsrv = "sqlsrv:Server=db;Database=zsf_test;Encrypt=0"
	pgsql  = "pgsql:host=db;dbname=zsf_test"
}

$dbUsers = @{
	mysql  = "root"
	sqlsrv = "sa"
	pgsql  = "postgres"
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
		$ProjectName = $_
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

function GetDockerProjectStatus([string] $ProjectName) {
	$result = @{
		Exists  = $false
		Running = $false
	}

	$lsOutput = docker compose ls --filter "name=$ProjectName" --all --format json 2>$null | ConvertFrom-Json

	if ($null -eq $lsOutput) {
		return $result
	}

	$project = $lsOutput | Where-Object { $_.Name -eq $ProjectName }

	if ($null -ne $project) {
		$result.Exists = $true
		$result.Running = $project.Status -match "running"
	}

	return $result
}

$Command = $Commands -Join " "

if ($Command-ne "init" -and [string]::IsNullOrWhiteSpace($ProjectName)) {
	Write-Host "You must initialize your project before running this command."

	Exit
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

		if ($EnvVariables.ContainsKey('SMTP_DOCKER') -and $EnvVariables.SMTP_DOCKER -eq 'False') {
			$SmtpDocker = $false
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

		$smtpPrompt     = QueryForInput -Prompt "Use Docker for SMTP Server"
		$dbEnginePrompt = QueryForInput -Prompt "Which DB engine" -AllowedValues @("mysql", "sqlsrv", "pgsql")

		if ($smtpPrompt -eq "N") {
			$SmtpDocker = $False
		}

		$DbEngine = $dbEnginePrompt.ToLower()
	}
}

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

	if ($SmtpDocker) {
		$includeLines += "  - docker-compose-smtp.yml"
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

function StartDocker([string] $ProjectName, [string] $WebContainer) {
	Push-Location ./docker
	docker compose -f docker-compose.yml -p $ProjectName up -d
	Pop-Location

	UpdateDocker -ProjectName $ProjectName -WebContainer $WebContainer

	Write-Host "Docker container started"

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

function InitDocker([string] $ProjectName, [string] $WebContainer) {
	$envContents = @"
PROJECT_NAME=$($ProjectName)
UI_FRONT_DOCKER=$($UiAdminDocker)
UI_ADMIN_DOCKER=$($UiFrontDocker)
DB_ENGINE=$($DbEngine)
SMTP_DOCKER=$($SmtpDocker)
"@

	Push-Location ./docker
	$envContents | Out-File -FilePath .env
	Pop-Location

	CreateCompose -DbEngine $DbEngine -UiAdminDocker $UiAdminDocker -UiFrontDocker $UiFrontDocker
	StartDocker -ProjectName $ProjectName -WebContainer $WebContainer

	Write-Host "Waiting 25s to let docker do its thang.. " -NoNewline
	Start-Sleep -Seconds 25
	Write-Host "DONE"

	$DbContainer = "$ProjectName-db"

	switch ($DbEngine) {
		"mysql" { Invoke-Expression "docker exec -i $DbContainer sh -c `"mysql -u root -p'P@55word' < /docker-entrypoint-initdb.d/mysql-init.sql`""	}
		"sqlsrv" { Invoke-Expression "docker exec -it $DbContainer /opt/mssql-tools/bin/sqlcmd -S localhost -U sa -P 'P@55word' -i /docker-entrypoint-initdb.d/sqlsrv-init.sql" }
	}

	Write-Host "Initializing container.."

	if (!(Test-Path -Path siteSettings.json)) {
		docker exec -t $WebContainer cp docker/siteSettings.json siteSettings.json
	}

	if (Test-Path -Path migrations/db) {
		$removeExistingDb = QueryForInput -Prompt "Clear existing db migrations"

		if ($removeExistingDb -eq "Y") {
			Remove-Item migrations/db -Recurse -Force

			if (Test-Path -Path "migrations/db.$DbEngine") {
				Copy-Item -Path "migrations/db.$DbEngine" "migrations/db" -Recurse
			}
		}
	} else {
		if (Test-Path -Path "migrations/db.$DbEngine") {
			Copy-Item -Path "migrations/db.$DbEngine" "migrations/db" -Recurse
		}
	}

	if (!(Test-Path -Path vendor)) {
		Write-Host "Installing composer dependencies.. " -NoNewline
		docker exec -it $WebContainer composer update
		Write-Host "DONE"
	}

	UpdateDocker -ProjectName $ProjectName -WebContainer $WebContainer

	Write-Host "Docker container initialized"
}

function UpdateDocker([string] $ProjectName, [string] $WebContainer) {
	Write-Host "Updating container for '$ProjectName'.."

	docker exec -it $WebContainer php vendor/bin/stoic-configure -P"dbDsns.default"="$($testDbDsns[$DbEngine])" -P"dbUsers.default"="$($dbUsers[$DbEngine])" -P"dbPasses.default"="P@55word"
	docker exec -it $WebContainer php vendor/bin/stoic-migrate up

	docker exec -it $WebContainer php vendor/bin/stoic-configure -P"dbDsns.default"="$($dbDsns[$DbEngine])" -P"dbUsers.default"="$($dbUsers[$DbEngine])" -P"dbPasses.default"="P@55word"
	docker exec -it $WebContainer php vendor/bin/stoic-migrate up

	Write-Host "Finished updating"
}

function TestDocker([string] $ProjectName, [string] $WebContainer, [bool] $OutputLogs) {
	UpdateDocker -ProjectName $ProjectName -WebContainer $WebContainer

	Write-Host "Running automated tests against test db"

	docker exec -it $WebContainer php vendor/bin/stoic-configure -P"dbDsns.default"="$($testDbDsns[$DbEngine])" -P"dbUsers.default"="$($dbUsers[$DbEngine])" -P"dbPasses.default"="P@55word"

	if ($OutputLogs) {
		docker exec -it $WebContainer /bin/bash -c "export OUTPUT_LOGS='true' && php vendor/bin/phpunit"
	} else {
		docker exec -it $WebContainer php vendor/bin/phpunit
	}

	docker exec -it $WebContainer php vendor/bin/stoic-configure -P"dbDsns.default"="$($dbDsns[$DbEngine])" -P"dbUsers.default"="$($dbUsers[$DbEngine])" -P"dbPasses.default"="P@55word"

	Write-Host "Automated tests complete, db reset to development"
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
		Remove-Job -Name ZsfUiAdmin

		Write-Host "DONE"
	}

	if (!$UiFrontDocker) {
		Write-Host "Stopping front UI.. " -NoNewline

		Stop-Job -Name ZsfUiFront
		Remove-Job -Name ZsfUiFront

		Write-Host "DONE"
	}
}

function DownDocker([string] $ProjectName) {
	$downPrompt  = QueryForInput -Prompt "Are you sure?  This will remove all containers and images"

	if ($downPrompt -eq "N") {
		return
	}

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

$dockerStatus = GetDockerProjectStatus -ProjectName $ProjectName

if ($Command -eq "init") {
	if ($dockerStatus.Exists) {
		Write-Host "Project '$ProjectName' already exists, skipping init"
	} else {
		InitDocker -ProjectName $ProjectName -WebContainer $WebContainer
	}
} elseif ($Command -eq "start") {
	if (!$dockerStatus.Exists) {
		Write-Host "Project '$ProjectName' does not exist, run init first"
	} elseif ($dockerStatus.Running) {
		Write-Host "Project '$ProjectName' is already running"
	} else {
		StartDocker -ProjectName $ProjectName -WebContainer $WebContainer
	}
} elseif ($Command -eq "update") {
	UpdateDocker -ProjectName $ProjectName -WebContainer $WebContainer
} elseif ($Command -eq "test") {
	TestDocker -ProjectName $ProjectName -WebContainer $WebContainer -OutputLogs $False
} elseif ($Command -eq "test-verbose") {
	TestDocker -ProjectName $ProjectName -WebContainer $WebContainer -OutputLogs $True
} elseif ($Command -eq "stop") {
	if (!$dockerStatus.Exists) {
		Write-Host "Project '$ProjectName' does not exist, nothing to stop"
	} elseif (!$dockerStatus.Running) {
		Write-Host "Project '$ProjectName' is not running"
	} else {
		StopDocker -ProjectName $ProjectName
	}
} elseif ($Command -eq "down") {
	if (!$dockerStatus.Exists) {
		Write-Host "Project '$ProjectName' does not exist, nothing to remove"
	} else {
		DownDocker -ProjectName $ProjectName
	}
} else {
	$opts = "t"

	if ($IsInteractive) {
		$opts += "i"
	}

	Invoke-Expression "docker exec -$opts $WebContainer $Command"
}
