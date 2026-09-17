<?php

/**
 * LibreNMS Device Importer Migration Checker
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */

namespace DRP\DeviceImporter;

use DRP\DeviceImporter\PluginDb;
use Illuminate\Support\Facades\Artisan;

/**
 * LibreNMS Device Importer Migration Checker
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */
class MigrationChecker {

    /**
     * Checks for pending package migrations and optionally runs them.
     *
     * @param bool $runPending Whether to execute the migrations automatically if found.
     * @return array Array containing 'has_pending' (bool) and 'pending' (array of filenames)
     */
    public static function checkAndMigrate(bool $runPending = false): array {

        // 1. Define the absolute path to your package migrations folder
        $packageMigrationPath = DEVICE_IMPORTER_PATH . 'database/migrations';

        Log::debug("Checking migrations in path: $packageMigrationPath");
        if (!is_dir($packageMigrationPath)) {
            return ['has_pending' => false, 'pending' => []];
        }

        // 2. Setup the migrator for 'plugin_db'
        $migrator = app('migrator');
        $migrator->setConnection(PluginDb::getDbConnection());

        // 3. Scan migrations
        $migrationFiles = $migrator->getMigrationFiles([$packageMigrationPath]);
        Log::debug("Found migration files: ", ['files' => $migrationFiles]);

        $ranMigrations = (array) $migrator->getRepository()->getRan();
        Log::debug("Already ran migrations: ", ['files' => $ranMigrations]);

        $pendingMigrations = array_diff(array_keys($migrationFiles), $ranMigrations);
        Log::debug("Pending migrations: ", ['files' => $pendingMigrations]);

        $hasPending = !empty($pendingMigrations);

        // 4. Run if requested and migrations exist
        if ($hasPending && $runPending) {
            Artisan::call('migrate', [
                '--database' => PluginDb::getDbConnection(),
                '--path' => 'vendor/daryl-peterson/librenms-device-importer/database/migrations'
            ]);
        }

        return [
            'has_pending' => $hasPending,
            'pending' => array_values($pendingMigrations)
        ];
    }
}
