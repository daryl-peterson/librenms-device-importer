<?php

namespace DRP\DeviceImporter;

use App\Models\Plugin;

/**
 * Importer for the Device Importer plugin.
 *
 * @package     App\Plugins\DeviceImporter
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       1.0.0
 */
class DeviceImporter {

    const PLUGIN          = 'device-importer';
    const TITLE           = 'Device Importer';
    const AUTHOR          = 'Daryl Peterson';
    const VER             = '0.0.1';

    public function __construct() {
        # Code Here
    }


    /**
     * Get plugin information.
     *
     * @return array{name: '', title: '', author: '', ver: '', settings: mixed, plugin: mixed}
     *
     * @version 1.0.0
     */
    public static function getInfo() {

        return array(
            'name'     => self::PLUGIN,
            'title'    => self::TITLE,
            'author'   => self::AUTHOR,
            'ver'      => self::VER,
            'settings' => route('plugin.settings', self::PLUGIN),
            'plugin'   => self::getPlugin(),
        );
    }

    /**
     * Get plugin object model.
     *
     * @return mixed
     *
     * @version 1.0.0
     */
    private static function getPlugin() {
        return Plugin::where('plugin_name', self::PLUGIN)->first();
    }
}
