<?php

/**
 * LibreNMS Device Importer Plugin Service Provider.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */

namespace DRP\DeviceImporter;

/**
 * Standard PHP imports.
 */

use Throwable;

/**
 * Laravel and application imports.
 */

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

/**
 * LibreNMS imports.
 */

use LibreNMS\Interfaces\Plugins\Hooks\MenuEntryHook as MenuEntryHookInterface;
use LibreNMS\Interfaces\Plugins\Hooks\SettingsHook as SettingsHookInterface;
use LibreNMS\Interfaces\Plugins\Hooks\SinglePageHook;
use LibreNMS\Interfaces\Plugins\PluginManagerInterface;
use LibreNMS\Plugins;


/**
 * Plugin imports.
 */

use DRP\DeviceImporter\Console\CheckMigrationsCommand;
use DRP\DeviceImporter\Console\ProcessPluginQueue;
use DRP\DeviceImporter\PluginDb;
use DRP\DeviceImporter\Hooks\Menu;
use DRP\DeviceImporter\Hooks\Page;
use DRP\DeviceImporter\Hooks\Settings;
use DRP\DeviceImporter\Log;

define(
    'DEVICE_IMPORTER_PATH',
    'vendor/daryl-peterson/librenms-device-importer/'
);

/**
 * LibreNMS Device Importer Plugin Service Provider.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */
class PluginProvider extends ServiceProvider {



    public function boot(): void {
        try {
            PluginDb::setDefaults();
            $pluginName = 'device-importer';

            // Ensure the migrations table exists before loading migrations
            PluginDb::checkMigrationsTable();
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

            /*
            * Compatibility view path.
            *
            * LibreNMS local plugins commonly reference views like:
            * device-importer::resources.views.page
            *
            * Package views can also be referenced as:
            * device-importer::page
            */
            $paths = [
                __DIR__ . '/..',
                __DIR__ . '/../resources/views',
            ];
            $this->loadViewsFrom($paths, 'device-importer');
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');


            $pluginManager = $this->app->make(PluginManagerInterface::class);
            $pluginManager->publishHook($pluginName, MenuEntryHookInterface::class, Menu::class);
            $pluginManager->publishHook($pluginName, SinglePageHook::class, Page::class);
            $pluginManager->publishHook($pluginName, SettingsHookInterface::class, Settings::class);

            if ($this->app->runningInConsole()) {
                $this->commands([
                    ProcessPluginQueue::class,
                    CheckMigrationsCommand::class,
                ]);
            }

            // Wait until LibreNMS completely boots up

            $this->app->booted(function () {
                $schedule = $this->app->make(Schedule::class);

                // Trigger your scheduled task safely
                $schedule
                    ->command('device-importer:process-plugin-queue')
                    ->everyTwoMinutes();
            });


            $this->clearCacheOnFirstRun();
        } catch (Throwable $th) {
            Log::error("Error booting plugin provider: " . $th->getMessage());
        }
    }

    /**
     * Clear cache on the first run of the plugin.
     *
     * This method checks for a hidden lock file to determine if it's the first run.
     * If it is, it clears the route, view, and cache, then creates the lock file.
     *
     * @since 0.0.0.1
     */
    protected function clearCacheOnFirstRun() {


        // Path to a hidden lock file inside your plugin folder
        $lockFile = __DIR__ . '/.installed';

        // If the file doesn't exist, this is the first run
        if (file_exists($lockFile)) {
            return;
        }

        try {

            // Programmatically run the equivalent of your 'lnms' commands
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            Artisan::call('cache:clear');

            // Create the lock file so this code never triggers again
            file_put_contents($lockFile, date('Y-m-d H:i:s'));
        } catch (Throwable $th) {
            Log::error("Error clearing cache on first run: " . $th->getMessage());
        }
    }
}
