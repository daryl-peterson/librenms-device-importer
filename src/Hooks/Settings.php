<?php

/**
 * Settings for the Device Importer plugin.
 *
 * @package     App\Plugins\DeviceImporter
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
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
 * @since       0.0.1
 */
class Settings extends SettingsHook {
    use \DRP\DeviceImporter\TraitHidePrivates;

    /**
     * The plugin identifier.
     */
    private string $plugin;

    public function __construct() {
        $this->plugin = DeviceImporter::PLUGIN;
    }

    public function getRouteName(): string {
        // Return the exact name of the route you defined in your web.php routes file
        return "plugin.$this->plugin.settings";
    }

    public function view() {
        // Fallback: Force a redirection to your custom route if the view is called directly
        return redirect()->route("plugin.$this->plugin.settings");
    }

    /**
     * Get the data for the settings view.
     *
     * @param array $settings The current settings stored in the database.
     * @return array The data to be passed to the settings view.
     */
    public function data(array $settings = []): array {
        return [
            'info' => DeviceImporter::getInfo(),
            'settings' => $settings,
        ];
    }
}
