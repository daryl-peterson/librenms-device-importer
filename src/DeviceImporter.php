<?php

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

namespace DRP\DeviceImporter;

use App\Models\Plugin;
use Illuminate\Support\Facades\Log;

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
     * @return array{name: '',title: '',author: '',ver: '',settings: string,page: string,plugin: Plugin,redis: bool}
     *
     * @version 1.0.0
     */
    public static function getInfo() {

        $redisAvailable = checkRedis();
        return array(
            'name'     => self::PLUGIN,
            'title'    => self::TITLE,
            'author'   => self::AUTHOR,
            'ver'      => self::VER,
            'settings' => self::getSettings(),
            'rt-settings' => route('plugin.settings', self::PLUGIN),
            'rt-page'     => route('plugin.page', self::PLUGIN),
            'plugin'   => self::getPlugin(),
            'redis' => $redisAvailable,
        );
    }

    /**
     * Get plugin object model.
     *
     * @return Plugin
     * @version 1.0.0
     */
    public static function getPlugin(): Plugin|null {
        $result = Plugin::where('plugin_name', self::PLUGIN)->first();

        // Check if the plugin exists in the database.
        if (is_null($result)) {
            Log::error('Plugin not found: ' . self::PLUGIN);
            return null;
        }
        return $result;
    }

    public static function getSettings(): array {
        $obj = new ImportSettings();
        $settings = $obj->all();
        return $settings ?? [];
    }
}
