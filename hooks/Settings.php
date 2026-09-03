<?php

/**
 * Settings for the Device Importer plugin.
 *
 * @package     App\Plugins\DeviceImporter
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       1.0.0
 */

namespace DRP\DeviceImporter\Hooks;

use App\Plugins\Hooks\SettingsHook;
use Illuminate\Support\Facades\Log;
use DRP\DeviceImporter\DeviceImporter;



/**
 * Settings for the Device Importer plugin.
 *
 * @package     App\Plugins\DeviceImporter
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       1.0.0
 */
class Settings extends SettingsHook {

    public function hook() {
    }

    /**
     * Get the data for the settings view.
     *
     * @param array $settings The current settings stored in the database.
     * @return array The data to be passed to the settings view.
     */
    public function data2(): array {
        $info = DeviceImporter::getInfo();

        return [
            'info' => $info,
            'settings' => $info['settings'],
        ];
    }
}
