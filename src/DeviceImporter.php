<?php

/**
 * Device Importer Plugin.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */

namespace DRP\DeviceImporter;

use App\Models\Plugin;
use Illuminate\Support\Facades\Log;

/**
 * Device Importer Plugin.
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
    const DB_NAME       = 'librenms_plugin_db';
    const DB_CONN = 'plugin_db';

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
     *  settings: array,
     *  routes: array{
     *    settings: string,
     *    page: string,
     *    export: string,
     *    upload: string
     *  },
     *  redis: bool
     * }
     *
     * @version 0.0.1
     */
    public static function getInfo() {
        $plugin = self::PLUGIN;
        $redisAvailable = checkRedis();
        $result = array(
            'name'     => self::PLUGIN,
            'title'    => self::TITLE,
            'author'   => self::AUTHOR,
            'ver'      => self::VER,
            'settings' => self::getSettings(),
            'routes'   => [
                'settings' => route('plugin.settings', $plugin),
                'page'     => route('plugin.page', $plugin),
                'export'   => route("$plugin.export", $plugin),
                'upload'   => route("$plugin.upload", $plugin),
            ],
            //'plugin'   => self::getPlugin(),
            'redis' => $redisAvailable,
        );
        Log::debug('Plugin info: ' . PHP_EOL . print_r($result, true));
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
