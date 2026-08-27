<?php
/**
 * DeviceImporter class for handling device import functionality.
 *
 * @package     DeviceImporter
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license    https://opensource.org MIT License
 * @link       https://github.com/daryl-peterson/librenms-device-importer/
 * @since       1.0.0
 */
namespace App\Plugins\DeviceImporter\Libs;

use App\Models\Plugin;
use Illuminate\Support\Facades\Log;


/**
 * DeviceImporter class for handling device import functionality.
 *
 * @package     DeviceImporter
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license    https://opensource.org MIT License
 * @link       https://github.com/daryl-peterson/librenms-device-importer/
 * @since       1.0.0
 */
class DeviceImporter {

	const PLUGIN   = 'DeviceImporter';
	const TITLE    = 'Device Importer';
	const AUTHOR   = 'Daryl Peterson';
	const PATH_KEY = 'DEVIMP_';
	const VER      = '0.0.1';

	private bool $debuging = false;

	/**
	 * Constructor for the DeviceImporter class.
	 *
	 * Initializes the plugin and sets up necessary settings.
	 *
	 * @version 1.0.0
	 */
	public function __construct() {

		$plugin         = self::getPlugin();
		$this->debuging = false;

		// Check if the plugin has already been initialized.
		if ( isset( $plugin->settings ) & key_exists( 'init', $plugin->settings ) ) {
			$this->debuging = $plugin->settings['debug'] ?? false;
			$this->debug( 'DeviceImporter plugin already initialized.' );

			return;
		}

		$plugin->settings = array(
			'init'  => true,
			'debug' => false,
		);
		$plugin->save();
	}

	/**
	 * Get plugin information.
	 *
	 * @return array
	 *
	 * @version 1.0.0
	 */
	public static function getInfo(): array {
		$plugin = self::getPlugin();

		$msg = print_r( $plugin, true );
		Log::debug( 'DeviceImporter plugin info: ' . $msg );
		return array(
			'name'     => self::PLUGIN,
			'title'    => self::TITLE,
			'author'   => self::AUTHOR,
			'ver'      => self::VER,
			'settings' => route( 'plugin.settings', self::PLUGIN ),
			'plugin'   => $plugin,
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
		return Plugin::where( 'plugin_name', self::PLUGIN )->first();
	}

	/**
	 * Debug helper function for logging messages.
	 *
	 * @param mixed $message
	 *
	 * @return void
	 */
	private function debug( mixed $message ) {
		if ( ! $this->debuging ) {
			return;
		}
		if ( ! is_string( $message ) ) {
			Log::debug( 'DeviceImporter: ' . print_r( $message, true ) );
		} else {
			Log::debug( 'DeviceImporter: ' . $message );
		}
	}
}
