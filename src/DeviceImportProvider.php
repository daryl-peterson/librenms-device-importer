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

use LibreNMS\Plugins;

use LibreNMS\Interfaces\Plugins\PluginManagerInterface;
use LibreNMS\Interfaces\Plugins\Hooks\DeviceOverviewHook as DeviceOverviewHookInterface;
use LibreNMS\Interfaces\Plugins\Hooks\MenuEntryHook as MenuEntryHookInterface;
use LibreNMS\Interfaces\Plugins\Hooks\SettingsHook as SettingsHookInterface;
use LibreNMS\Interfaces\Plugins\Hooks\SinglePageHook;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use DRP\DeviceImporter\Hooks\DeviceOverview;
use DRP\DeviceImporter\Hooks\Menu;
use DRP\DeviceImporter\Hooks\Page;
use DRP\DeviceImporter\Hooks\Settings;



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

        $hasRedis = checkRedis();
        if (! $hasRedis) {
            $obj = new ImportSettings();
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
