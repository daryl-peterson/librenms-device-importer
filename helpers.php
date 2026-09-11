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
 * PHP imports.
 */

use Throwable;

/**
 * Laravel imports.
 */

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


/**
 * Log an error message for a Throwable.
 *
 * @param Throwable $e The exception or error to log.
 * @return void
 * @since 0.0.1
 */
function doErrorMsg(Throwable $e) {
    $limitedTrace = array_slice($e->getTrace(), 0, 5);

    $result = sprintf(
        "Error: %s\nMessage: %s\nTrace: %s",
        get_class($e),
        $e->getMessage(),
        print_r($limitedTrace, true)
    );
    Log::error($result);
}

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
