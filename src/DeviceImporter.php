<?php

/**
 * LibreNMS Device Importer Plugin.
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
 * Laravel and application imports.
 */

use App\Models\Plugin;
use Illuminate\Support\Facades\Log;

/**
 * Plugin imports.
 */

use DRP\DeviceImporter\DbCheck;
use DRP\DeviceImporter\PluginSettings;


/**
 * LibreNMS Device Importer Plugin.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */
class DeviceImporter {

    const PLUGIN        = 'device-importer';
    const TITLE         = 'Device Importer';
    const AUTHOR        = 'Daryl Peterson';
    const VER           = '0.0.1';

    /**
     * Constructor.
     *
     * @since 0.0.1
     */
    public function __construct() {
        # Code Here
    }


    /**
     * Get plugin information.
     *
     * @return array{
     *  name: '',
     *  title: '',
     *  author: '',
     *  ver: '',
     *  image: '',
     *  settings: array,
     *  dbStatus: array{
     *    ready: bool,
     *    error: string|null
     *  },
     * }
     *
     * @version 0.0.1
     */
    public static function getInfo() {
        $plugin = self::PLUGIN;

        $result = array(
            'name'     => self::PLUGIN,
            'title'    => self::TITLE,
            'author'   => self::AUTHOR,
            'ver'      => self::VER,
            'image'    => 'https://avatars.githubusercontent.com/u/13834451?s=400&u=ff8417db6126da8d9ff82822ea0be5897ad744b3&v=4',
            'settings' => self::getSettings(),
            'dbStatus'  => [
                'ready' => DbCheck::isReady(),
                'error' => DbCheck::getError()
            ],
        );

        return $result;
    }

    /**
     * Get plugin object model.
     *
     * @return Plugin|null
     * @version 0.0.1
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

    /**
     * Get plugin settings.
     *
     * @return array
     * @version 0.0.1
     */
    public static function getSettings(): array {
        $obj = new PluginSettings();
        $settings = $obj->all();
        return $settings ?? [];
    }
}
