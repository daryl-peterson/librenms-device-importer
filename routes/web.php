<?php

use Illuminate\Support\Facades\Route;

use DRP\DeviceImporter\Controllers\DeviceImportController;
use DRP\DeviceImporter\Controllers\ActionController;



Route::middleware(['web', 'auth'])->group(function (): void {
    Route::get('plugin/device-importer-package', [DeviceImportController::class, 'index']);
});



Route::middleware(['web'])
    ->post('plugin/device-importer-package/action', [ActionController::class, 'handle'])
    ->name('device-importer.action');
