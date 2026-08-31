## Edit composer.json append to end of file

```
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

https://github.com/WizballESY/librenms-device-photo/tree/main

https://github.com/murrant/librenms-example-plugin

## Clear Views

php artisan optimize:clear
php artisan view:clear



## Other Plugin samples

https://github.com/WizballESY/librenms-device-photo/tree/main
[https://github.com/murrant/librenms-example-plugin]()

## Git create tag and push

git tag v0.1.0-alpha.01
git push origin v0.1.0-alpha.01
