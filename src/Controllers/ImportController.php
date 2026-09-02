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

        return view('device-importer::page');
    }

    public function upload(): View {
        $data = DeviceImporter::getInfo();
        return view('device-importer::upload');
    }
}
