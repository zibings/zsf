[CmdletBinding()]
param (
  [parameter(Mandatory=$false)] [string] $ProjectName
)

$EnvVariables = @{}
$HasEnvFile   = Test-Path -Path "./docker/.env"

if ($HasEnvFile) {
  Get-Content ./docker/.env | ForEach-Object {
    $name, $value = $_.split('=')

    $EnvVariables.Add($name, $value)
  }
}

if (!($ProjectName)) {
  if (!$HasEnvFile -or !$EnvVariables.ContainsKey('PROJECT_NAME')) {
    $ProjectName = Read-Host -Prompt "What is the name of your project"
  } else {
    $ProjectName = $EnvVariables.PROJECT_NAME
  }
}

Write-Host "Building docker container for '$ProjectName'.."

$webContainer = "$ProjectName-web"
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

docker exec -t $webContainer cp docker/siteSettings.json siteSettings.json
docker exec -t $webContainer vendor/bin/stoic-migrate up

Write-Host "Docker container initialized"