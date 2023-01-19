[CmdletBinding()]

$EnvVariables = @{}
$HasEnvFile   = Test-Path -Path "./docker/.env"

if (!$HasEnvFile) {
	Write-Error "No .env file, you must initialize the container first"
}

Get-Content ./docker/.env | ForEach-Object {
	$name, $value = $_.split('=')

	$EnvVariables.Add($name, $value)
}

$ProjectName  = $EnvVariables.PROJECT_NAME
$webContainer = "$ProjectName-web"

Write-Host "Updating container for '$ProjectName'.."

docker exec -t $webContainer vendor/bin/stoic-migrate up

Write-Host "Finished updating"