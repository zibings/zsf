# Zibings Site Framework, Redux
Usage instructions:

```
- Copy 'composer.json' file into project directory root
- Run 'composer update' in project directory to download dependencies
- Copy remaining ZSF files into root directory
- Change the name of the appropriate database migrations folder to simply be 'db' (vs 'db.sqlsrv', for example)
- Run 'vendor/bin/stoic-configure' to setup your configuration file
- Run 'vendor/bin/stoic-migrate' to initialize your database
- Create things
```

## Generating OpenAPI Spec
We now include a script to generate OpenAPI specs and have included them for the new v1.1 of the API.  To see usage, execute the following command:

```
php scripts/generate-openapi.php -h
```

Once generated, you can place this inside of `web` so that it can be utilized by things such as AXIOS.

## Docker Container Notes
Dependencies required for local development:

* Docker
* NodeJS (v >= 16)

To initialize the container and run the services:

```powershell
./Exec.ps1 init
```

This will make the `~/web` folder accessible via [http://localhost:8080/](http://localhost:8080/) and the MySQL 'adminer' interface available at [http://localhost:8081/](http://localhost:8081/).  To use the 'adminer' interface, use the following connection info:

```
host: db
user: root
pass: P@55word
name: zsf
```

To run the migration command and any other common 'update' procedures:

```powershell
./Exec.ps1 update
```

To run custom commands on the container:

```powershell
./Exec.ps1 php scripts/generate-openapi.php -v 1.1 -o ./web/sui/openapi.yaml -f yaml
```

To run an interactive script on the container:

```powershell
./Exec.ps1 -i php scripts/add-user.php
```

To build the front-end and deploy it to the `~/web` folder:

```powershell
./Exec.ps1 build
```