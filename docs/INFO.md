## Edit composer.json

`"repositories": [
    {
        "type": "path",
        "url": "/home/daryl/Dropbox/Websites/librenms-device-importer",
        "options": {
            "symlink": true
        }
    }
]`

## Run Udate

composer require daryl-peterson/librenms-device-importer:dev-main



## Force Install

FORCE=1 composer install
