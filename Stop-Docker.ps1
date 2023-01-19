Write-Host "Stopping docker container.. "

Push-Location docker
docker compose stop
Pop-Location

Write-Host "Docker container stopped"