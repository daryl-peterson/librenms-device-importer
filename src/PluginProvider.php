<?php

/**
 * Device import service provider.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */

namespace DRP\DeviceImporter;


use DRP\DeviceImporter\DbCheck;
use DRP\DeviceImporter\Hooks\DeviceOverview;
use DRP\DeviceImporter\Hooks\Menu;
use DRP\DeviceImporter\Hooks\Page;
use DRP\DeviceImporter\Hooks\Settings;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use LibreNMS\Interfaces\Plugins\Hooks\MenuEntryHook as MenuEntryHookInterface;
use LibreNMS\Interfaces\Plugins\Hooks\SettingsHook as SettingsHookInterface;
use LibreNMS\Interfaces\Plugins\Hooks\SinglePageHook;
use LibreNMS\Interfaces\Plugins\PluginManagerInterface;
use LibreNMS\Plugins;

/**
 * Device import service provider.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */
class PluginProvider extends ServiceProvider {

    public function register(): void {
    }

    public function boot(): void {
        $pluginName = 'device-importer';

        DbCheck::isReady();

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
            //$viewPath,

        ];
        $this->loadViewsFrom($paths, 'device-importer');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        //$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $pluginManager = $this->app->make(PluginManagerInterface::class);
        $pluginManager->publishHook($pluginName, MenuEntryHookInterface::class, Menu::class);
        $pluginManager->publishHook($pluginName, SinglePageHook::class, Page::class);
        $pluginManager->publishHook($pluginName, SettingsHookInterface::class, Settings::class);

        $this->clearCacheOnFirstRun();
    }

    /**
     * Clear cache on the first run of the plugin.
     *
     * This method checks for a hidden lock file to determine if it's the first run.
     * If it is, it clears the route, view, and cache, then creates the lock file.
     */
    protected function clearCacheOnFirstRun() {
        // Path to a hidden lock file inside your plugin folder
        $lockFile = __DIR__ . '/.installed';

        // If the file doesn't exist, this is the first run
        if (!file_exists($lockFile)) {
            try {
                // Programmatically run the equivalent of your 'lnms' commands
                Artisan::call('route:clear');
                Artisan::call('view:clear');
                Artisan::call('cache:clear');

                // Create the lock file so this code never triggers again
                file_put_contents($lockFile, date('Y-m-d H:i:s'));

                Log::info('Device Importer: First-run cache clear executed successfully.');
            } catch (\Exception $e) {
                Log::error('Device Importer: Failed to auto-clear cache: ' . $e->getMessage());
            }
        }
    }
}
