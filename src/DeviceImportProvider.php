<?php

namespace DRP\DeviceImporter;

use Illuminate\Support\ServiceProvider;
use LibreNMS\Interfaces\Plugins\PluginManagerInterface;
use LibreNMS\Interfaces\Plugins\Hooks\DeviceOverviewHook as DeviceOverviewHookInterface;
use LibreNMS\Interfaces\Plugins\Hooks\MenuEntryHook as MenuEntryHookInterface;
use LibreNMS\Interfaces\Plugins\Hooks\SettingsHook as SettingsHookInterface;
use LibreNMS\Interfaces\Plugins\Hooks\SinglePageHook;
use DRP\DeviceImporter\Hooks\DeviceOverview;
use DRP\DeviceImporter\Hooks\Menu;
use DRP\DeviceImporter\Hooks\Page;
use DRP\DeviceImporter\Hooks\Settings;
use LibreNMS\Plugins;

class DeviceImportProvider extends ServiceProvider {

    public function register(): void {
    }

    public function boot(): void {
        $pluginName = 'device-importer';

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'device-importer');

        /*
         * Compatibility view path.
         *
         * LibreNMS local plugins commonly reference views like:
         * device-photo::resources.views.page
         *
         * Package views can also be referenced as:
         * device-photo::page
         */
        $this->loadViewsFrom(__DIR__ . '/..', 'device-importer');

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        $pluginManager = $this->app->make(PluginManagerInterface::class);

        $pluginManager->publishHook($pluginName, DeviceOverviewHookInterface::class, DeviceOverview::class);
        $pluginManager->publishHook($pluginName, MenuEntryHookInterface::class, Menu::class);
        $pluginManager->publishHook($pluginName, SinglePageHook::class, Page::class);
        $pluginManager->publishHook($pluginName, SettingsHookInterface::class, Settings::class);
    }
}
