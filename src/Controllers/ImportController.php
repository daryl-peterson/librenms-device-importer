<?php

/**
 * Device Import Controller
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       1.0.0
 */

namespace DRP\DeviceImporter\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use DRP\DeviceImporter\DeviceImporter;

/**
 * Device Import Controller
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       1.0.0
 */
class ImportController extends Controller {

    const CONTROLLER_PATH = 'plugin/device-importer';

    public function index(): View {

        /*
        $filepath = "../resources/views/layouts/menu.blade.php";
        $contents = (array) file_get_contents($filepath);
        Log::alert('CONTESTS', $contents);
        */
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called');
        return view('device-importer::page')->with('error', 'Your custom error message goes here.');
    }

    public function upload(): View {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called');
        $data = DeviceImporter::getInfo();
        return view('device-importer::upload', ['info' => $data]);
    }

    public function settings(): View {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called');
        $data = DeviceImporter::getInfo();
        return view('device-importer::settings', ['info' => $data]);
    }
}
