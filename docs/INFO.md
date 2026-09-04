
# LibreNMS Developement Notes

## Add to plugin to Composer

```bash
composer config --global repositories.example-plugin '{"type": "path", "url": "/full/path/to/librenms-example-plugin", "symlink": true}'
```

## Execute lmns command to add it to framework

```bash
lnms plugin:add daryl-peterson/librenms-tickets @dev
```

## Edit composer.json append to end of file

```bash
"repositories": [
    {
        "type": "path",
        "url": "/home/daryl/Dropbox/Websites/librenms-device-importer",
        "options": {
            "symlink": true
        }
    }
]
```

## Remove Lock file

rm /opt/librenms/composer.lock

## Run Udate

composer require daryl-peterson/librenms-device-importer:dev-main

## Force Install

FORCE=1 composer install

see <https://github.com/WizballESY/librenms-device-photo/tree/main>

see <https://github.com/murrant/librenms-example-plugin>

## Clear Views

php artisan optimize:clear
php artisan view:clear

## Other Plugin samples

see <https://github.com/WizballESY/librenms-device-photo/tree/main>
see <https://github.com/murrant/librenms-example-plugin>

## Git create tag and push

git tag v0.1.0-alpha.01
git push origin v0.1.0-alpha.01

## Migrate

./lnms migrate --path=vendor/daryl-peterson/librenms-device-importer/database/migrations
