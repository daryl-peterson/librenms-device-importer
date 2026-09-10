# LibreNMS Device Importer

## Create database

> CREATE DATABASE librenms_plugin_db
> 
> GRANT ALL PRIVILEGES ON librenms_plugin_db.* TO **[LIBRE USER]**@'localhost';



## Edit environment file

```
nano .env

PLUGIN_DB_HOST=127.0.0.1
PLUGIN_DB_DATABASE=librenms_plugin_db
PLUGIN_DB_USERNAME=**[LIBRE USER]**
PLUGIN_DB_PASSWORD=**[YOUR PASSWORD]**

```

## Install using lnms command

lnms plugin:add daryl-peterson/librenms-tickets @dev
