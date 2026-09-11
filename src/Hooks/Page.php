<?php

/**
 * LibreNMS Device Importer Page.
 *
 * @package     App\Plugins\DeviceImporter
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */

namespace DRP\DeviceImporter\Hooks;

use App\Plugins\Hooks\PageHook;
use DRP\DeviceImporter\PluginData;


/**
 * LibreNMS Device Importer Page.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */
class Page extends PageHook {

    /**
     * Get page data.
     *
     * @return array
     * @version 0.0.1
     */
    public function data(): array {

        return [
            'info' => PluginData::getInfo(),
            'plugin' => PluginData::getPluginName(),
        ];
    }
}
