<?php

/**
 * Web routes for the Device Importer plugin.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       1.0.0
 */


use Illuminate\Support\Facades\Route;

use DRP\DeviceImporter\Controllers\ImportController;
use DRP\DeviceImporter\Controllers\ActionController;
use function DRP\DeviceImporter\checkRedis;
use DRP\DeviceImporter\DeviceImporter;

$plugin = DeviceImporter::PLUGIN;


/**
 * Upload route
 */
if (checkRedis()) {
    Route::middleware(['web'])
        ->get("plugin/$plugin/upload", [ImportController::class, 'upload'])
        ->name("$plugin.upload");
}


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
