<?php

use Illuminate\Support\Facades\Route;
use DRP\DeviceImporter\Controllers\ActionController;
use DRP\DeviceImporter\Controllers\ImportController;


Route::middleware(['web'])
    ->get('plugin/device-importer-package/image', [ImportController::class, 'show'])
    ->name('device-importer.image');


Route::middleware(['web'])
    ->post('plugin/device-importer-package/action', [ActionController::class, 'handle'])
    ->name('device-importer.action');
