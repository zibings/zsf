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
