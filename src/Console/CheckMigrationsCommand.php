<?php

/**
 * LibreNMS Device Importer Check Migrations Command
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */

namespace DRP\DeviceImporter\Console;

use DRP\DeviceImporter\MigrationChecker;
use Illuminate\Console\Command;


/**
 * LibreNMS Device Importer Check Migrations Command
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 *
 */
class CheckMigrationsCommand extends Command {

    // The signature your package users will type in their terminal
    protected $signature = 'device-importer:check-migrations {--run : Automatically run migrations if pending ones are found}';
    protected $description = 'Checks if there are any unrun database migrations.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $this->info('Scanning package migrations for plugin_db...');

        // Call the reusable service
        $result = MigrationChecker::checkAndMigrate($this->option('run'));

        if (!$result['has_pending']) {
            $this->components->info('Everything is up to date! No pending package migrations found on plugin_db.');
            return Command::SUCCESS;
        }

        $this->components->warn("Found " . count($result['pending']) . " pending package migration(s):");
        foreach ($result['pending'] as $migration) {
            $this->line(" - {$migration}");
        }

        if ($this->option('run')) {
            $this->info("\nPackage migrations successfully executed on plugin_db.");
        }

        return Command::SUCCESS;
    }
}
