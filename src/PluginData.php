<?php

namespace DRP\DeviceImporter;

use App\Models\Plugin as PluginModel;

use DRP\DeviceImporter\Log;
use DRP\DeviceImporter\PluginSettings;
use DRP\DeviceImporter\PluginCache;

/**
 * LibreNMS Device Importer Plugin.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 *
 * @property-read string $name
 * @property-read string $title
 * @property-read string $author
 * @property-read string $ver
 * @property-read array $settings
 * @property-read string $image
 */
class PluginData {

    private array $data = [];

    public function __construct() {
        $settings = (array) self::getSettings();

        $this->data = [
            'name'  => PLUGIN_NAME,
            'title' => PLUGIN_TITLE,
            'author' => PLUGIN_AUTHOR,
            'ver'    => PLUGIN_VER,
            'settings' => $settings,
        ];
    }

    /**
     * Get plugin instance.
     *
     * @return self
     * @version 0.0.1
     */
    public static function getInstance(): self {
        return new self();
    }

    /**
     * Get plugin data array.
     *
     * @return array
     * @version 0.0.1
     */
    public static function getData(): array {
        $obj = self::getInstance();
        $dbError = PluginCache::get(CACHE_DB_ERROR);
        $result = [
            'info' => [
                'name'     => $obj->name,
                'title'    => $obj->title,
                'author'   => $obj->author,
                'ver'      => $obj->ver,
                'image'    => $obj->image,
            ],

            'settings' => $obj->settings ?? []
        ];
        return $result;
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
        $dbError = PluginCache::get(CACHE_DB_ERROR);

        $result = array(
            'name'     => PLUGIN_NAME,
            'title'    => PLUGIN_TITLE,
            'author'   => PLUGIN_AUTHOR,
            'ver'      => PLUGIN_VER,
            'image'    => 'https://avatars.githubusercontent.com/u/13834451?s=400&u=ff8417db6126da8d9ff82822ea0be5897ad744b3&v=4',
            'settings' => self::getSettings(),
            'dbStatus'  => [
                'ready' => !$dbError,
                'error' => $dbError
            ],
        );

        return $result;
    }

    /**
     * Get plugin object model.
     *
     * @return PluginModel|null
     * @version 0.0.1
     */
    public static function getPluginModel(): PluginModel|null {
        $result = PluginModel::where('plugin_name', PLUGIN_NAME)->first();

        // Check if the plugin exists in the database.
        if (is_null($result)) {
            Log::error('Plugin not found: ' . PLUGIN_NAME);
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

    public static function getPluginName(): string {
        return PLUGIN_NAME;
    }


    /**
     * Get plugin name.
     *
     * @return string
     * @version 0.0.1
     */
    public string $name {
        get => $this->data['name'];
    }

    /**
     * Get plugin title.
     *
     * @return string
     * @version 0.0.1
     */
    public string $title {
        get => $this->data['title'];
    }
    /**
     * Get plugin author.
     *
     * @return string
     * @version 0.0.1
     */
    public string $author {
        get => $this->data['author'];
    }
    /**
     * Get plugin version.
     *
     * @return string
     * @version 0.0.1
     */
    public string $ver {
        get => $this->data['ver'];
    }

    /**
     * Get plugin settings.
     *
     * @return array
     * @version 0.0.1
     */
    public array $settings {
        get => $this->data['settings'];
    }
}
