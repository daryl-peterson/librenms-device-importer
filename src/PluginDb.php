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

/**
 * Standard PHP imports.
 */

use DRP\DeviceImporter\Helper;
use DRP\DeviceImporter\Log;
use DRP\DeviceImporter\PluginCache;
use DRP\DeviceImporter\PluginData;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
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

    const PLUGIN_DB_HOST        = '127.0.0.1';
    const PLUGIN_DB_PORT        = '3306';
    const PLUGIN_DB_CONNECTION  = 'plugin_db';
    const PLUGIN_DB_DATABASE    = 'librenms_plugin_db';
    const PLUGIN_DB_USERNAME    = 'plugin_user';
    const PLUGIN_DB_PASSWORD    = 'plugin_password';



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
        $this->pluginCache->forget(self::CACHE_RESULT_KEY);
        $this->pluginCache->forget(self::CACHE_DATE_KEY);
        $this->pluginCache->forget(self::CACHE_DB_ERROR);
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

        //if (!$this->checkDbUser() || !$this->checkConnection() || !$this->checkTables()) {
        if (!$this->checkConnection()) {
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
    public function checkConnection(): bool {
        try {
            $conn = self::getDbConnection();
            \DB::connection($conn)->getPdo();
            return true;
        } catch (\Exception $e) {
            //$this->pluginCache->set(self::CACHE_DB_ERROR, $e->getMessage());
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
        $trouble = false;

        try {
            //$settings = PluginData::getSettings();
            $conn = PluginDb::PLUGIN_DB_CONNECTION;


            if (Config::has("database.connections.{$conn}")) {
                return;
            }

            $config = self::getDbConnectionConfig();

            // 1. Inject the Database Connection Array into memory
            Config::set("database.connections.{$conn}", $config);
            $info = Config::get("database.connections.{$conn}");

            // Mask the password for logging purposes
            if (isset($info['password'])) {
                $info['password'] = '********';
            }

            if ($trouble) {
                Log::info(
                    "Database connection info being injected: ",
                    ["database.connections.{$conn}" => $info]
                );
            }

            $config = [
                'connection' => $conn,
                'driver'     => 'database',
                'table'      => 'jobs',
                'queue'      => 'plugin_queue',
                'retry_after' => 90,
            ];

            // 2. Inject the Queue Connection mapping that points to the DB above

            Config::set('queue.connections.plugin_queue', $config);
            $info = Config::get('queue.connections.plugin_queue');

            if ($trouble) {
                Log::info(
                    "Queue connection info injected: ",
                    ['queue.connections.plugin_queue' => $info]
                );
            }


            $config = [
                'driver' => 'database-uuids',
                'database' => $conn,
                'table' => 'failed_jobs',
            ];

            Config::set('queue.failed', $config);
            $info = Config::get('queue.failed');


            if ($trouble) {
                Log::info(
                    "Failed jobs queue connection info injected: ",
                    ['queue.failed' => $info]
                );
            }
        } catch (Throwable $th) {
            Log::error("Error injecting plugin database configuration: " . $th->getMessage());
        }
    }

    /**
     * Check if the migrations table exists and install it if necessary.
     *
     * @param bool $bypassCache Whether to bypass the cached result and perform a fresh check.
     * @return void
     * @since 0.0.1
     */
    public static function checkMigrationsTable(bool $bypassCache = false) {

        $conn = self::getDbConnection();
        $objCache = new PluginCache();
        $ttl = Helper::days(1);

        if ($objCache->has(PluginCache::DB_CHECK_DATE) && !$bypassCache) {
            return;
        }

        try {

            // Check if the migrations table exists before attempting to install migrations
            if (! Schema::connection($conn)->hasTable('migrations')) {
                Artisan::call('migrate:install', [
                    '--database' => $conn,
                ]);
            }

            $result = Artisan::call('migrate', [
                '--database' => $conn,
                // Ensures migrations run without interactive prompts
                '--force' => true,
                // Optional: isolates to just your plugin files
                '--path'     => 'vendor/daryl-peterson/librenms-device-importer/database/migrations',
            ]);

            $output = Artisan::output();
            Log::debug("Migrations output: " . PHP_EOL . $output);

            $objCache->set(PluginCache::DB_CHECK_RESULT, true);
        } catch (Throwable $th) {
            Log::error("Error checking migrations table: " . $th->getMessage());
            $objCache->set(PluginCache::DB_CHECK_RESULT, false);
        }
        $objCache->set(PluginCache::DB_CHECK_DATE, now(), $ttl);
    }

    public static function hasPendingMigrations(): bool {
        $conn = self::getDbConnection();
        $migrations = Artisan::call('migrate:status', [
            '--database' => $conn,
            '--path'     => 'vendor/daryl-peterson/librenms-device-importer/database/migrations',
        ]);
        $output = Artisan::output();
        return str_contains($output, 'No');
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
        $result = $instance->checkConnection();
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
     * @return string The database connection name.
     * @since 0.0.1
     */
    public static function getDbConnection(): string {
        return env('PLUGIN_DB_CONNECTION', self::PLUGIN_DB_CONNECTION);
    }
    /**
     * Get the database name.
     *
     * @return string The database name.
     * @since 0.0.1
     */
    public static function getDbName(): string {
        return env('PLUGIN_DB_DATABASE', self::PLUGIN_DB_DATABASE);
    }


    public static function getDbConnectionConfig() {
        $settings = PluginData::getSettings();

        $config = [
            'driver'    => 'mysql',
            'host'      => $settings['host'] ?? PluginDb::PLUGIN_DB_HOST,
            'database'  => $settings['database'] ?? PluginDb::PLUGIN_DB_DATABASE,
            'username'  => $settings['username'] ?? PluginDb::PLUGIN_DB_USERNAME,
            'password'  => $settings['password'] ?? PluginDb::PLUGIN_DB_PASSWORD,
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
        ];
        return $config;
    }
}
