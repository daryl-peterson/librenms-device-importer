<?php

/**
 * LibreNMS Device Importer Import Controller
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */

namespace DRP\DeviceImporter\Controllers;

/**
 * Laravel imports.
 */

use Illuminate\Routing\Controller;
use Illuminate\View\View;

/**
 * Plugin imports.
 */

use DRP\DeviceImporter\TraitHidePrivates;
use DRP\DeviceImporter\PluginData;
use DRP\DeviceImporter\TraitValidateAdmin;

/**
 * LibreNMS Device Importer Import Controller
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */
class ImportController extends Controller {
	use TraitHidePrivates;
	use TraitValidateAdmin;

	private array $info;
	private string $plugin;

	/**
	 * Constructor.
	 *
	 * @since 0.0.1
	 */
	public function __construct() {
		$this->info = PluginData::getInfo();
		$this->plugin = PluginData::PLUGIN;
	}


	public function index(): View {

		return view("$this->plugin::page");
	}

	/**
	 * Import page.
	 *
	 * @return View
	 * @since 0.0.1
	 */
	public function import(): View {
		$this->validateAdmin();

		return view("$this->plugin::import", ['info' => $this->info]);
	}

	/**
	 * Export page.
	 *
	 * @return View
	 * @since 0.0.1
	 */
	public function export(): View {
		$this->validateAdmin();

		return view("$this->plugin::export", ['info' => $this->info]);
	}

	/**
	 * Settings page.
	 *
	 * @return View
	 * @since 0.0.1
	 */
	public function settings(): View {
		$this->validateAdmin();

		return view("$this->plugin::settings", ['info' => $this->info]);
	}
}
