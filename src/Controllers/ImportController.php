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


use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

use DRP\DeviceImporter\TraitHidePrivates;
use DRP\DeviceImporter\DeviceImporter;
use DRP\DeviceImporter\TraitValidateAdmin;

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
    use TraitValidateAdmin;

    private array $info;
    private string $plugin;

    /**
     * Constructor.
     *
     * @since 0.0.1
     */
    public function __construct() {
        $this->info = DeviceImporter::getInfo();
        $this->plugin = DeviceImporter::PLUGIN;
    }


    public function index(): View {

        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called');
        return view("$this->plugin::page");
    }

    /**
     * Upload page.
     *
     * @return View
     * @since 0.0.1
     */
    public function upload(): View {
        $this->validateAdmin();

        return view("$this->plugin::upload", ['info' => $this->info]);
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
