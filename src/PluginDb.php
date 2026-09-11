<?php

/**
 * LibreNMS Device Importer Database Check.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */

namespace DRP\DeviceImporter;

use DRP\DeviceImporter\DbTables;
use DRP\DeviceImporter\PluginCache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PDO;
use Throwable;


/**
 * LibreNMS Device Importer Database Check.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */
class PluginDb {
    use TraitHidePrivates;

    const PLUGIN_DB_CONNECTION = 'plugin_db';
    const PLUGIN_DB_DATABASE = 'librenms_plugin_db';
    const PLUGIN_DB_USERNAME = 'plugin_user';
    const CACHE_RESULT_KEY = 'db_check_result';
    const CACHE_DATE_KEY = 'db_check_date';
    const CACHE_DB_ERROR = 'db_error';

    private PluginCache $pluginCache;

    /**
     * Constructor.
     *
     * @since 0.0.1
     */
    public function __construct() {
        self::setDefaults();
        $this->pluginCache = new PluginCache();
    }

    /**
     * Run the database check.
     *
     * @return boolean
     * @since 0.0.1
     */
    public static function run(): bool {
        $instance = new self();
        return $instance->check();
    }

    /**
     * Check the database and return the result.
     *
     * @return boolean
     * @since 0.0.1
     */
    public function check(): bool {
        if (!$this->checkDbUser() || !$this->checkConnection() || !$this->checkTables()) {
            return false;
        }
        return true;
    }

    /**
     * Check database user.
     *
     * @return boolean
     * @since 0.0.1
     */
    private function checkDbUser(): bool {
        $user = config('database.connections.plugin_db.username');
        if ($user === self::PLUGIN_DB_USERNAME) {
            $this->pluginCache->set(
                self::CACHE_DB_ERROR,
                'Invalid database user'
            );
            return false;
        }
        return true;
    }

    /**
     * Check database connection.
     *
     * @return boolean
     * @since 0.0.1
     */
    private function checkConnection(): bool {
        try {
            $conn = self::getDbConnection();
            \DB::connection($conn)->getPdo();
            return true;
        } catch (\Exception $e) {
            $this->pluginCache->set(self::CACHE_DB_ERROR, $e->getMessage());
            return false;
        }
    }

    /**
     * Set defaults for connection.
     *
     * @return void
     * @since 0.0.1
     */
    public static function setDefaults() {
        $conn = self::getDbConnection();

        // 1. Define the separate database connection
        config(["database.connections.{$conn}" => [
            'driver' => 'mysql',
            'host' => env('PLUGIN_DB_HOST', '127.0.0.1'),
            'port' => env('PLUGIN_DB_PORT', '3306'),
            'database' => env('PLUGIN_DB_DATABASE', self::PLUGIN_DB_DATABASE),
            'username' => env('PLUGIN_DB_USERNAME', self::PLUGIN_DB_USERNAME),
            'password' => env('PLUGIN_DB_PASSWORD', 'secret'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
        ]]);

        // 2. Define the queue connection using that database
        config(['queue.connections.plugin_queue' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => 90,
            'connection' => $conn, // Points to the connection above
        ]]);

        config(['queue.failed' => [
            'driver' => 'database-uuids',
            'database' => $conn, // Points to your separate database connection
            'table' => 'failed_jobs',
        ]]);   # Code Here
    }

    /**
     * Check if the migrations table exists and install it if necessary.
     *
     * @return void
     * @since 0.0.1
     */
    public static function checkMigrationsTable() {
        $conn = self::getDbConnection();

        try {
            // Check if the migrations table exists before attempting to install migrations
            if (! Schema::connection($conn)->hasTable('migrations')) {
                Artisan::call('migrate:install', [
                    '--database' => $conn,
                ]);
            }
        } catch (Throwable $th) {
            doErrorMsg($th); //throw $th;
        }
    }


    /**
     * Make sure required tables exist.
     *
     * @return boolean
     * @since 0.0.1
     */
    private function checkTables(): bool {
        $required = ['jobs', 'failed_jobs'];
        try {

            DbTables::createTables();

            $conn = self::getDbConnection();
            $tables = DB::connection($conn)
                ->getPdo()
                ->query('SHOW TABLES')
                ->fetchAll(PDO::FETCH_COLUMN);

            foreach ($required as $table) {
                if (!in_array($table, $tables)) {
                    $this->pluginCache->set(self::CACHE_DB_ERROR, "Missing table: $table");
                    return false;
                }
            }
            return true;
        } catch (\Exception $e) {
            $this->pluginCache->set(self::CACHE_DB_ERROR, $e->getMessage());
            return false;
        }
    }

    /**
     * Check if the database is ready.
     *
     * @param bool $bypassCache Whether to bypass the cached result and perform a fresh check.
     * @return bool
     * @since 0.0.1
     */
    public static function isReady(bool $bypassCache = false): bool {
        $objCache = new PluginCache();

        $has = $objCache->has(self::CACHE_RESULT_KEY);
        if ($has && !$bypassCache) {

            $result = $objCache->get(self::CACHE_RESULT_KEY);
            return (bool) $result;
        }

        $instance = new self();
        $result = $instance->checkDbUser() && $instance->checkConnection() && $instance->checkTables();
        if ($result) {
            $objCache->set(self::CACHE_DB_ERROR, null);
        }

        $objCache->set(self::CACHE_RESULT_KEY, $result);
        $objCache->set(self::CACHE_DATE_KEY, time());
        return $result;
    }

    /**
     * Get the last database error message.
     *
     * @return string|null
     * @since 0.0.1
     */
    public static function getError(): ?string {
        $objCache = new PluginCache();
        if ($objCache->has(self::CACHE_DB_ERROR)) {
            return $objCache->get(self::CACHE_DB_ERROR);
        }
        return null;
    }

    /**
     * Get the database connection name.
     *
     * @return string
     * @since 0.0.1
     */
    public static function getDbConnection(): string {
        return env('PLUGIN_DB_CONNECTION', self::PLUGIN_DB_CONNECTION);
    }
    /**
     * Get the database name.
     *
     * @return string
     * @since 0.0.1
     */
    public static function getDbName(): string {
        return env('PLUGIN_DB_DATABASE', self::PLUGIN_DB_DATABASE);
    }
}
