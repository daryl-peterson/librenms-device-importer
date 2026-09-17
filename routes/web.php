<?php

/**
 * LibreNMS Device Importer Web Routes.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       1.0.0
 */


use Illuminate\Support\Facades\Route;

/**
 * Plugin imports.
 */

use DRP\DeviceImporter\Controllers\ImportController;
use DRP\DeviceImporter\Controllers\ActionController;
use DRP\DeviceImporter\PluginDb;
use DRP\DeviceImporter\PluginData;

$plugin = PluginData::PLUGIN;


/**
 * Upload route
 */
//if (PluginDb::isReady()) {
Route::middleware(['web'])
    ->get("plugin/$plugin/import", [ImportController::class, 'import'])
    ->name("$plugin.import");
//}


Route::middleware(['web'])
    ->get("plugin/$plugin/export", [ImportController::class, 'export'])
    ->name("$plugin.export");


/**
 * Settings route
 */
Route::middleware(['web'])
    ->get("plugin/settings/$plugin", [ImportController::class, 'settings'])
    ->name("$plugin.settings");

/**
 * Action route
 */
Route::middleware(['web'])
    ->post("plugin/$plugin/action", [ActionController::class, 'handle'])
    ->name("$plugin.action");
