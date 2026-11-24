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

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use PDOException;
use Illuminate\View\View;

/**
 * Plugin imports.
 */

use DRP\DeviceImporter\TraitHidePrivates;
use DRP\DeviceImporter\PluginData;
use DRP\DeviceImporter\PluginDb;
use DRP\DeviceImporter\TraitValidateAdmin;
use const DRP\DeviceImporter\PLUGIN_NAME;

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
        $this->plugin = PLUGIN_NAME;
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

        return view("$this->plugin::import", ['info' => $this->info, 'plugin' => $this->plugin]);
    }

    /**
     * Export page.
     *
     * @return View
     * @since 0.0.1
     */
    public function export(): View {
        $this->validateAdmin();

        return view("$this->plugin::export", ['info' => $this->info, 'plugin' => $this->plugin]);
    }

    public function databaseTest(Request $request): View {
        $this->validateAdmin();

        $config = config('database.connections.' . PluginDb::getDbConnection());
        $result = null;

        if ($request->isMethod('post')) {
            try {
                $hasMigrationsTable = PluginDb::testDatabase($config);
                $result = $hasMigrationsTable
                    ? ['success', 'Connected to the database. The migrations table is visible.']
                    : ['warning', 'Connected to the database, but the migrations table is missing or inaccessible. Check the database grants and migration status.'];
            } catch (PDOException $exception) {
                $code = (int) ($exception->errorInfo[1] ?? $exception->getCode());
                $message = match ($code) {
                    1045, 1698 => 'The database rejected the login. Check PLUGIN_DB_USERNAME, PLUGIN_DB_PASSWORD, and the database account allowed from this host.',
                    1044 => 'The account lacks access to the configured database. Check its database grants.',
                    1049 => 'The configured database does not exist. Check PLUGIN_DB_DATABASE.',
                    2002, 2003, 2005 => 'Could not reach the database server. Check PLUGIN_DB_HOST and PLUGIN_DB_PORT.',
                    default => 'The database check failed. Verify the database configuration and permissions.',
                };
                $result = ['danger', $message . ($code > 0 ? " (Database error $code)" : '')];
            }
        }

        return view("$this->plugin::database-test", [
            'info' => $this->info,
            'plugin' => $this->plugin,
            'database' => Arr::only($config, ['host', 'port', 'database', 'username']),
            'result' => $result,
        ]);
    }

    /**
     * Settings page.
     *
     * @return View
     * @since 0.0.1
     */
    public function settings(): View {
        $this->validateAdmin();

        return view("$this->plugin::settings", ['info' => $this->info, 'plugin' => $this->plugin]);
    }
}
