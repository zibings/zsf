Write-Host "Cleaning output files.. " -NoNewLine

$toDelete = @{
  "assets"      = "dir"
  "data"        = "dir"
  "images"      = "dir"
  "layout"      = "dir"
  "themes"      = "dir"
  "config.json" = "file"
  "favicon.ico" = "file"
  "index.html"  = "file"
}

$toDelete.Keys | ForEach-Object {
  if (Test-Path "./web/$_") {
    if ($toDelete[$_] -eq "dir") {
      Remove-Item "./web/$_" -Recurse
    } else {
      Remove-Item "./web/$_"
    }
  }
}

Write-Host "DONE"

cd admin
yarn
yarn upgrade
yarn zsf
cd ..