<?php

/**
 * LibreNMS Device Importer Database Helper
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */

namespace DRP\DeviceImporter;

use Illuminate\Support\Facades\DB;

use DRP\DeviceImporter\Contracts\DbHelperInterface;
use Illuminate\Support\Facades\Facade;


/**
 * LibreNMS Device Importer Database Helper
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */
class DbHelper extends Facade implements DbHelperInterface {
    use TraitHidePrivates;

    private PluginCache $cache;

    private static bool $booted = false;



    public static function getFacadeAccessor(): string {
        return DbHelperInterface::class;
    }

    /**
     * Get the database connection for the plugin.
     *
     * @return string
     */
    public function getConnection(): string {
        return PLUGIN_DB_CONNECTION;
    }

    /**
     * Get the name of the database.
     *
     * @return string
     */
    public function getDbName(): string {
        return env('DB_DATABASE');
    }

    /**
     * Get the last error message, if any.
     *
     * @return string|null
     */
    public function getError(): ?string {
        return DB::connection(PLUGIN_DB_CONNECTION)->getPdo()->errorInfo()[2] ?? null;
    }

    /**
     * Boot the database connection or perform any necessary initialization.
     *
     * @return void
     */
    public function boot(): void {
        if (self::$booted) {
            return;
        }

        // Marked before the work below so a nested call cannot recurse.
        self::$booted = true;
        $this->cache = new PluginCache();
    }
}
