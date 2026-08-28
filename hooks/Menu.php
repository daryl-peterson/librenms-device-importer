<?php

/**
 * Class menu for the DeviceImporter plugin
 *
 * @package     DeviceImporter
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       1.0.0
 */

namespace DRP\DeviceImporter\Hooks;

use App\Plugins\Hooks\MenuEntryHook;
use Illuminate\Support\Facades\Log;

try {
    require_once __DIR__ . '/src/includes.php';
} catch (\Exception $e) {
    Log::error('Failed to include includes.php: ' . $e->getMessage());
}

/**
 * Class menu for the DeviceImporter plugin
 *
 * @package     DeviceImporter
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       1.0.0
 */
class Menu extends MenuEntryHook {

    // override the data function to add additional data to be accessed in the view
    // inside the blade, all variables will be named based on the key in the returned array
    public function data(array $settings = []): array {
        // inject settings and count how many we have so we can display it in the menu

        return [
            'count' => 32,
        ];
    }
}
