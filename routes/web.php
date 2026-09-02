<?php

use Illuminate\Support\Facades\Route;

use DRP\DeviceImporter\Controllers\ImportController;
use DRP\DeviceImporter\Controllers\ActionController;



/*

Route::middleware(['web'])
    ->get('plugin/device-importer/', [DeviceImportController::class, 'index'])
    ->name('device-importer.index');
*/

Route::middleware(['web'])
	->get('plugin/device-importer/upload', [ImportController::class, 'upload'])
	->name('device-importer.upload');

Route::middleware(['web'])
	->post('plugin/device-importer/action', [ActionController::class, 'handle'])
	->name('device-importer.action');
