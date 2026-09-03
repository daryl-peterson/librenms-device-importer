<?php

/**
 * Import settings for the Device Importer plugin.
 *
 * @package     App\Plugins\DeviceImporter\ImportSettings
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
 * Import settings for the Device Importer plugin.
 *
 * @package     App\Plugins\DeviceImporter\ImportSettings
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       1.0.0
 */
class ImportSettings {
    /**
     * Import settings for the Device Importer plugin.
     *
     * @since 1.0.0
     */
    public array $settings;

    /**
     * The plugin instance.
     *
     * @since 1.0.0
     */
    public Plugin|null $plugin = null;

    /**
     * Import settings constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
        $this->plugin = DeviceImporter::getPlugin();
        $settings = null;

        if (is_null($this->plugin)) {
            $this->settings = [];
            return;
        }

        $settings = $this->plugin->settings;

        if (!is_array($settings)) {
            $settings = [];
            $this->settings = $settings;
            $this->plugin->settings = $this->settings;
            $this->plugin->save();
        }

        $this->settings = $settings;
    }

    /**
     * Get all plugin settings.
     *
     * @return array
     *
     * @since 1.0.0
     */
    public function all(): array {
        return $this->settings;
    }

    /**
     * Get a specific plugin setting.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     *
     * @since 1.0.0
     */
    public function get(string $key, $default = null) {
        return $this->settings[$key] ?? $default;
    }

    /**
     * Set a plugin setting.
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     *
     * @since 1.0.0
     */
    public function set(string $key, $value): bool {
        try {
            $this->settings[$key] = $value;

            if (is_null($this->plugin)) {
                return false;
            }

            $this->plugin->settings = $this->settings;
            return $this->plugin->save();
        } catch (\Exception $e) {
            Log::error('Failed to save plugin settings: ' . $e->getMessage());
            return false;
        }
    }

    public function reset() {
        $this->settings = [];
        if (!is_null($this->plugin)) {
            $this->plugin->settings = $this->settings;
            $this->plugin->save();
        }
    }

    public function delete(string $key): bool {
        try {
            if (isset($this->settings[$key])) {
                unset($this->settings[$key]);
            }

            if (is_null($this->plugin)) {
                return false;
            }

            $this->plugin->settings = $this->settings;
            return $this->plugin->save();
        } catch (\Exception $e) {
            Log::error('Failed to delete plugin setting: ' . $e->getMessage());
            return false;
        }
    }
}
