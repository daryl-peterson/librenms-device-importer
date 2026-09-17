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

use App\Models\Plugin;
use Throwable;
use DateTimeImmutable;

/**
 * Laravel and application imports.
 */

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


/**
 * Plugin database imports.
 */

use DRP\DeviceImporter\Helper;
use DRP\DeviceImporter\Log;
use DRP\DeviceImporter\PluginCache;

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

    /**
     * Plugin cache instance
     */
    private static ?PluginCache $pluginCache = null;

    /**
     * Initialization flag
     */
    private static bool $initialized = false;

    /**
     * Database name
     */
    private static ?string $dbName = null;

    /**
     * Database host
     */
    private static ?string $dbHost = null;

    /**
     * Database port
     */
    private static ?string $dbPort = null;

    /**
     * Database connection name
     */
    private static ?string $dbConnection = null;
    /**
     * Database username
     */
    private static ?string $dbUsername = null;

    /**
     * Database password
     */
    private static ?string $dbPassword = null;

    /**
     * Run the plugin database initialization and checks.
     *
     * @return void
     * @since 0.0.1
     */
    public static function run(): void {
        if (!self::checkConnection()) {
            return;
        }
        self::checkMigrationsTable();
    }

    /**
     * Check database connection.
     *
     * @return boolean
     * @since 0.0.1
     */
    public static function checkConnection(): bool {
        $ttlPass = Helper::hours(1);
        $ttlError = Helper::minutes(5);

        if (!self::$initialized) {
            self::initProperties();
        }

        try {
            $result = self::$pluginCache->get(PluginCache::DB_PASS, false);
            if ($result) {
                return true;
            }

            if (self::$pluginCache->has(PluginCache::DB_ERROR)) {
                return false;
            }

            $conn = self::getDbConnection();
            $result = DB::connection($conn)->getPdo();

            self::$pluginCache->set(
                PluginCache::DB_PASS,
                true,
                $ttlPass
            );

            return true;
        } catch (\Exception $e) {
            self::$pluginCache->set(
                PluginCache::DB_PASS,
                false,
                $ttlError
            );
            self::$pluginCache->set(
                PluginCache::DB_ERROR,
                'Unable to connect to database',
                $ttlError
            );
            return false;
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
        $ttl_days = Helper::days(1);
        $ttl_mins = Helper::minutes(5);

        if (!self::$initialized) {
            self::initProperties();
        }

        // Check database connection before proceeding
        if (!self::checkConnection()) {
            return;
        }

        $conn = self::getDbConnection();

        if (self::$pluginCache->has(PluginCache::DB_MIGRATION_CHECK) && !$bypassCache) {
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

            self::$pluginCache->set(
                PluginCache::DB_MIGRATION_CHECK,
                true,
                $ttl_days
            );
        } catch (Throwable $th) {
            Log::error("Error checking migrations table: " . $th->getMessage());
            self::$pluginCache->set(
                PluginCache::DB_MIGRATION_CHECK,
                true,
                $ttl_mins
            );
        }
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
     * Get the last database error message.
     *
     * @return string|null
     * @since 0.0.1
     */
    public static function getError(): ?string {
        if (self::$pluginCache->has(PluginCache::DB_ERROR)) {
            return self::$pluginCache->get(PluginCache::DB_ERROR);
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
        if (!isset(self::$dbConnection) || empty(self::$dbConnection)) {
            self::initProperties();
        }
        return (string) self::$dbConnection;
    }
    /**
     * Get the database name.
     *
     * @return string The database name.
     * @since 0.0.1
     */
    public static function getDbName(): string {
        if (!isset(self::$dbName) || empty(self::$dbName)) {
            self::initProperties();
        }
        return (string) self::$dbName;
    }

    /**
     * Initialize the plugin database and queue configuration.
     *
     * @return void
     * @since 0.0.1
     */
    public static function initConfig(): void {
        if (self::$initialized) {
            return;
        }

        self::initProperties();

        // 1. Configure the database connection for the plugin
        $config = [
            'driver'     => 'mysql',
            'database'   => self::$dbName,
            'host'       => self::$dbHost,
            'port'       => self::$dbPort,
            'username'   => self::$dbUsername,
            'password'   => self::$dbPassword,
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
        ];
        Config::set("database.connections." . self::$dbConnection, $config);

        // 2. Inject the Queue Connection mapping that points to the DB above
        $config = [
            'connection' => self::$dbConnection,
            'driver'     => 'database',
            'table'      => 'jobs',
            'queue'      => 'plugin_queue',
            'retry_after' => 90,
        ];

        Config::set('queue.connections.plugin_queue', $config);

        $config = [
            'driver' => 'database-uuids',
            'database' => self::$dbConnection,
            'table' => 'failed_jobs',
        ];

        Config::set('queue.failed', $config);
        self::$initialized = true;
    }

    /**
     * Initialize the plugin database properties from environment variables.
     *
     * @return void
     * @since 0.0.1
     */
    private static function initProperties(): void {
        self::$pluginCache = new PluginCache();
        self::$dbConnection = env('PLUGIN_DB_CONNECTION', self::PLUGIN_DB_CONNECTION);
        self::$dbName = env('PLUGIN_DB_DATABASE', self::PLUGIN_DB_DATABASE);
        self::$dbHost = env('PLUGIN_DB_HOST', self::PLUGIN_DB_HOST);
        self::$dbPort = env('PLUGIN_DB_PORT', self::PLUGIN_DB_PORT);
        self::$dbUsername = env('PLUGIN_DB_USERNAME', self::PLUGIN_DB_USERNAME);
        self::$dbPassword = env('PLUGIN_DB_PASSWORD', self::PLUGIN_DB_PASSWORD);
    }

    private static function getDate(): string {
        $date = new DateTimeImmutable();
        return $date->format('Y-m-d H:i:s');
    }
}
