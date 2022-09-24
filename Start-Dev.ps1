Start-Job -Name ZsfNpmDev -ScriptBlock{cd admin && yarn && yarn upgrade && yarn dev}
Start-Job -Name ZsfPhpDev -ScriptBlock{cd web && php -S localhost:8080}

Write-Host "`nAdmin running on http://127.0.0.1:5173/"
Write-Host "API running on   http://127.0.0.1:8080/api/1/index.php?url="