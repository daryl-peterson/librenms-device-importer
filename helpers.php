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

use Throwable;
use Illuminate\Support\Facades\Log;

/**
 * Log an error message for a Throwable.
 *
 * @param Throwable $e The exception or error to log.
 * @return void
 *
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


function isAdmin(): bool {
    if (auth()->user()?->hasRole('admin')) {
        // User has the explicit 'admin' role assigned
        return true;
    }
    return false;
}
