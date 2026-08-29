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
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

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
class DeviceImportController extends Controller {

    const CONTROLLER_PATH = 'plugin/device-importer';

    public function index(): View {


        $filepath = "../resources/views/layouts/menu.blade.php";
        $contents = (array) file_get_contents($filepath);
        Log::alert('CONTESTS', $contents);

        return view('device-importer::page');
    }
}
