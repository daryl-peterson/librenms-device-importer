<?php

/**
 * LibreNMS Device Importer plugin helper functions.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */

namespace DRP\DeviceImporter;


/**
 * Laravel imports.
 */

use Illuminate\Support\Facades\Auth;


define(
    'DEVICE_IMPORTER_PATH',
    'vendor/daryl-peterson/librenms-device-importer/'
);

/**
 * Check if the current user is an admin.
 *
 * @return bool True if the current user is an admin, false otherwise.
 * @since 0.0.1
 */
function isAdmin(): bool {
    if (!Auth::check() || !Auth::user()->hasRole('admin')) {
        return false;
    }
    return true;
}
