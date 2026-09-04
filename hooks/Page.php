<?php

/**
 * Page for the Device Importer plugin.
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
use DRP\DeviceImporter\DeviceImporter;
use Illuminate\Support\Facades\Log;


/**
 * Page for the Device Importer plugin.
 *
 * @package     App\Plugins\DeviceImporter
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */
class Page extends PageHook {

	public function data($settings = []): array {
		Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called');
		session()->put('error', 'Your custom error message goes here.');
		session(['error' => 'Your custom error message goes here.']);


		return [
			'info' => DeviceImporter::getInfo(),
		];
	}
}
