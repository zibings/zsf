[CmdletBinding()]

$ProjectName      = ""
$Commands         = @()
$EnvVariables     = @{}
$IsInteractive    = $false
$FoundProjectName = $false
$HasEnvFile       = Test-Path -Path "./docker/.env"

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

    $EnvVariables.Add($name, $value)
  }
}

if ([string]::IsNullOrWhiteSpace($ProjectName)) {
  if (!$HasEnvFile -or !$EnvVariables.ContainsKey('PROJECT_NAME')) {
    $ProjectName = Read-Host -Prompt "What is the name of your project"
  } else {
    $ProjectName = $EnvVariables.PROJECT_NAME
  }
}

$Command      = $Commands -Join " "
$WebContainer = "$ProjectName-web"

function InitDocker([string] $ProjectName, [string] $WebContainer) {
	$envContents  = @"
PROJECT_NAME=$($ProjectName)
"@

	Push-Location ./docker
	$envContents | Out-File -FilePath .env
	docker build .
	docker compose -p $ProjectName up -d
	Pop-Location

	Write-Host "Docker container build complete"

	Write-Host "Waiting 15s to let docker do its thang.. " -NoNewline
	Start-Sleep -Seconds 15
	Write-Host "DONE"

	Write-Host "Initializing container.."

	docker exec -t $WebContainer cp docker/siteSettings.json siteSettings.json
	docker exec -t $WebContainer composer update
	docker exec -t $WebContainer vendor/bin/stoic-migrate up

	Write-Host "Docker container initialized"

	Write-Host "Starting admin UI.. " -NoNewline

	Push-Location ./ui/admin
	cp ../../docker/admin-config.json public/config.json
	pnpm install
	Start-Job -Name ZsfUiAdmin -ScriptBlock { Invoke-Expression "pnpm dev" }
	Pop-Location

	Write-Host "DONE"
	Write-Host "Starting front UI.. " -NoNewline

	Push-Location ./ui/front
	cp ../../docker/front-config.json public/config.json
	pnpm install
	Start-Job -Name ZsfUiFront -ScriptBlock { Invoke-Expression "pnpm dev" }
	Pop-Location

	Write-Host "DONE"
}

function UpdateDocker([string] $ProjectName, [string] $WebContainer) {
	Write-Host "Updating container for '$ProjectName'.."

	docker exec -t $WebContainer vendor/bin/stoic-migrate up

	Write-Host "Finished updating"
}

function StopDocker([string] $ProjectName) {
	Write-Host "Stopping docker container.. "

	Push-Location docker
	docker compose -p $ProjectName stop
	Pop-Location

	Write-Host "Docker container stopped"
	Write-Host "Stopping admin UI.. " -NoNewline

	Stop-Job -Name ZsfUiAdmin

	Write-Host "DONE"
	Write-Host "Stopping front UI.. " -NoNewline

	Stop-Job -Name ZsfUiFront

	Write-Host "DONE"
}

function DownDocker([string] $ProjectName) {
	Write-Host "Removing docker container.. "

	Push-Location docker
	docker compose -p $ProjectName down
	Pop-Location

	Write-Host "Docker container removed"
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
