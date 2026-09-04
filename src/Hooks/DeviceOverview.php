<?php

/**
 * LibreNMS Device Importer Device Overview Hook
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */

namespace DRP\DeviceImporter\Hooks;

use App\Models\Device;
use App\Plugins\Hooks\DeviceOverviewHook;

/**
 * LibreNMS Device Importer Device Overview Hook
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */
class DeviceOverview extends DeviceOverviewHook {


    public function data(\App\Models\Device $device): array {
        // here we pass a title string, url to notes, and the device to the blade view for display

        return [
            'title' => 'Example Plugin: Device Notes',
            'device' => $device,
            'url' => url('device/' . $device->device_id . '/notes'),
        ];
    }
}
