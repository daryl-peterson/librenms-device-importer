
<?php

/**
 * Device Importer configuration.
 *
 * Published to config/device-importer.php. env() is only safe here, because
 * this file is evaluated once and its result is what config:cache stores.
 */

return [
    'db' => [
        'connection' => env('PLUGIN_DB_CONNECTION', PLUGIN_DB_CONNECTION),
        'database'   => env('PLUGIN_DB_DATABASE', PLUGIN_DB_DATABASE),
        'host'       => env('PLUGIN_DB_HOST', PLUGIN_DB_HOST),
        'port'       => env('PLUGIN_DB_PORT', PLUGIN_DB_PORT),
        'username'   => env('PLUGIN_DB_USERNAME', PLUGIN_DB_USERNAME),
        'password'   => env('PLUGIN_DB_PASSWORD', PLUGIN_DB_PASSWORD),
    ],
];
