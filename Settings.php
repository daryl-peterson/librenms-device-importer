<?php

/**
 * Settings class for the DeviceImporter plugin
 *
 * @package     App\Plugins\DeviceImporter
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/librenms-device-importer
 * @since       1.0.0
 */

namespace App\Plugins\DeviceImporter;

use App\Plugins\Hooks\SettingsHook;

require_once __DIR__ . '/src/includes.php';

/**
 * Settings class for the DeviceImporter plugin
 *
 * @package     App\Plugins\DeviceImporter
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/librenms-device-importer
 * @since       1.0.0
 */
class Settings extends SettingsHook {

    public function __construct() {
    }

    /**
     * Returns the data to be used in the settings view.
     *
     * @param array $settings The current settings stored in the database.
     * @return array The data to be passed to the view.
     * @since 1.0.0
     */
    public function data(array $settings = []): array {
        // run any calculations here
        $total = array_sum([1, 2, 3, 4]);
        $obj = new Importer();

        return [
            'settings' => $settings, // this is an array of all the settings stored in the database
            'something' => 'this is a variable and can be accessed with {{ $something }}',
            'total' => $total,
        ];
    }
}
