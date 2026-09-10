# LibreNMS Device Importer

## Create database

```bash
CREATE DATABASE librenms_plugin_db
GRANT ALL PRIVILEGES ON librenms_plugin_db.* TO [LIBRE_USER]@'localhost';
```

## Edit environment file

```bash
nano .env

PLUGIN_DB_HOST=[127.0.0.1]
PLUGIN_DB_DATABASE=librenms_plugin_db
PLUGIN_DB_USERNAME=[LIBRE_USER]
PLUGIN_DB_PASSWORD=[LIBRE_PASSWORD]
```

***Change the items in brackets [ ]  to  match your install.***

## Install using lnms command

lnms plugin:add daryl-peterson/librenms-device-importer @dev

## Add cron job

```bash
crontab -e

*/5  *    * * *   flock -n /tmp/plugin_queue.lock -c "/usr/bin/php /opt/librenms/artisan plugin:process-plugin-queue --tries=3" > /dev/null 2>&1
```

![](screenshots/screenshot-02.png)

