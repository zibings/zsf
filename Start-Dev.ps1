Start-Job -Name ZsfNpmDev -ScriptBlock{cd admin && yarn install && npm run dev}
Start-Job -Name ZsfPhpDev -ScriptBlock{cd web && php -S localhost:8080}

Write-Host "`nNow running on http://127.0.0.1:5173/"