Start-Job -Name ZsfNpmDev -ScriptBlock{cd admin && yarn install && npm run dev}

Write-Host "`nNow running on http://127.0.0.1:5173/"