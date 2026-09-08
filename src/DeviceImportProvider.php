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


use DRP\DeviceImporter\Hooks\DeviceOverview;
use DRP\DeviceImporter\Hooks\Menu;
use DRP\DeviceImporter\Hooks\Page;
use DRP\DeviceImporter\Hooks\Settings;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use LibreNMS\Interfaces\Plugins\Hooks\DeviceOverviewHook as DeviceOverviewHookInterface;
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
class DeviceImportProvider extends ServiceProvider {

    public function register(): void {
    }

    public function boot(): void {
        $pluginName = 'device-importer';

        // 1. Define the separate database connection
        config(['database.connections.plugin_db' => [
            'driver' => 'mysql',
            'host' => env('PLUGIN_DB_HOST', '127.0.0.1'),
            'port' => env('PLUGIN_DB_PORT', '3306'),
            'database' => env('PLUGIN_DB_DATABASE', 'librenms_plugin_db'),
            'username' => env('PLUGIN_DB_USERNAME', 'plugin_user'),
            'password' => env('PLUGIN_DB_PASSWORD', 'secret'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
        ]]);

        // 2. Define the queue connection using that database
        config(['queue.connections.plugin_database_queue' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => 90,
            'connection' => 'plugin_db', // Points to the connection above
        ]]);

        config(['queue.failed' => [
            'driver' => 'database-uuids',
            'database' => 'plugin_db', // Points to your separate database connection
            'table' => 'failed_jobs',
        ]]);



        $hasRedis = checkRedis();
        if (! $hasRedis) {
            $obj = new PluginSettings();
            $obj->set('redis', false);
        } else {
            config(['queue.default' => 'redis']);
        }

        /*
         * Compatibility view path.
         *
         * LibreNMS local plugins commonly reference views like:
         * device-importer::resources.views.page
         *
         * Package views can also be referenced as:
         * device-importer::page
         */

        $rootPath = base_path();
        $viewPath = $rootPath . '/vendor/daryl-peterson/librenms-device-importer/resources/views';
        $paths = [
            __DIR__ . '/..',
            __DIR__ . '/../resources/views',
            $viewPath,

        ];
        Log::debug('View paths: ' . PHP_EOL . print_r($paths, true));


        $this->loadViewsFrom($paths, 'device-importer');
        //$this->loadViewsFrom(__DIR__ . '/../resources/views', 'librenms-device-importer');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $paths = View::getFinder()->getPaths();
        Log::debug('Current view paths: ' . PHP_EOL . print_r($paths, true));


        $pluginManager = $this->app->make(PluginManagerInterface::class);

        Log::debug('Plugin Manager: ' . PHP_EOL . print_r($pluginManager, true));
        $pluginManager->publishHook($pluginName, DeviceOverviewHookInterface::class, DeviceOverview::class);
        $pluginManager->publishHook($pluginName, MenuEntryHookInterface::class, Menu::class);
        $pluginManager->publishHook($pluginName, SinglePageHook::class, Page::class);
        $pluginManager->publishHook($pluginName, SettingsHookInterface::class, Settings::class);
    }
}
