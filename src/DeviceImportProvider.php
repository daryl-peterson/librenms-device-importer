<?php

/**
 * Device import service provider.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       1.0.0
 */

namespace DRP\DeviceImporter;

use LibreNMS\Plugins;

use LibreNMS\Interfaces\Plugins\PluginManagerInterface;
use LibreNMS\Interfaces\Plugins\Hooks\DeviceOverviewHook as DeviceOverviewHookInterface;
use LibreNMS\Interfaces\Plugins\Hooks\MenuEntryHook as MenuEntryHookInterface;
use LibreNMS\Interfaces\Plugins\Hooks\SettingsHook as SettingsHookInterface;
use LibreNMS\Interfaces\Plugins\Hooks\SinglePageHook;
use Illuminate\Support\ServiceProvider;
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
 * @since       1.0.0
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
        $paths = [
            __DIR__ . '/..',
            __DIR__ . '/../resources/views'
        ];
        $this->loadViewsFrom($paths, 'device-importer');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $pluginManager = $this->app->make(PluginManagerInterface::class);
        $pluginManager->publishHook($pluginName, DeviceOverviewHookInterface::class, DeviceOverview::class);
        $pluginManager->publishHook($pluginName, MenuEntryHookInterface::class, Menu::class);
        $pluginManager->publishHook($pluginName, SinglePageHook::class, Page::class);
        $pluginManager->publishHook($pluginName, SettingsHookInterface::class, Settings::class);
    }
}
