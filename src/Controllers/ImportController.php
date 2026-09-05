<?php

/**
 * Device Import Controller
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */

namespace DRP\DeviceImporter\Controllers;

use Illuminate\Support\Facades\Log;
use DRP\DeviceImporter\TraitHidePrivates;
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
 * @since       0.0.1
 */
class ImportController extends Controller {
    use TraitHidePrivates;

    private array $info;
    private string $plugin;
    private string $controllerPath;

    public function __construct() {
        $this->info = DeviceImporter::getInfo();
        $this->plugin = DeviceImporter::PLUGIN;
        $this->controllerPath = self::CONTROLLER_PATH;
    }


    public function index(): View {

        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called');
        return view("$this->plugin::page");
    }

    public function upload(): View {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called');

        return view("$this->plugin::upload", ['info' => $this->info]);
    }

    public function export(): View {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called');
        return view("$this->plugin::export", ['info' => $this->info]);
    }

    public function settings(): View {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called');

        return view("$this->plugin::settings", ['info' => $this->info]);
    }
}
