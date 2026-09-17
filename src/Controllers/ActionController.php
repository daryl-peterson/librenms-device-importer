<?php

/**
 * Action Controller
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
 * Standard PHP imports.
 */

use Exception;
use Throwable;

/**
 * Laravel imports.
 */

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Plugin imports.
 */

use DRP\DeviceImporter\CsvProcessor;
use DRP\DeviceImporter\Helper;
use DRP\DeviceImporter\Log;
use DRP\DeviceImporter\PluginData;
use DRP\DeviceImporter\PluginDb;
use DRP\DeviceImporter\PluginSettings;
use DRP\DeviceImporter\TraitHidePrivates;
use DRP\DeviceImporter\TraitValidateAdmin;
use DRP\DeviceImporter\Jobs\ImportDeviceJob;

/**
 * Action Controller
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */
class ActionController extends Controller {
    use TraitHidePrivates;
    use TraitValidateAdmin;

    private array $headersRequired = [];
    private array $map = [];
    private PluginSettings $settings;
    private array $csvHeaders;

    public function __construct() {
        $this->headersRequired = ['hostname', 'ip_address', 'os'];
        $this->settings = new PluginSettings();
        $this->csvHeaders = ['hostname', 'hardware', 'serial', 'os', 'snmpver', 'community', 'snmp_disable'];
    }

    /**
     * Handel request.
     *
     * @param Request $request
     * @return StreamedResponse|Redirector|RedirectResponse|null
     * @since 0.0.1
     */
    public function handle(Request $request): StreamedResponse|Redirector|RedirectResponse|null {
        $user = auth()->user();

        if (! Helper::isAdmin()) {
            abort(403, 'Forbidden');
        }

        $action = (string) $request->input('action', '');

        return match ($action) {
            'export' => $this->export($request),
            'upload' => $this->upload($request),
            'save' => $this->save($request),
            default => $this->redirect(
                null,
                'unknown_action'
            ),
        };
    }

    /**
     * Export devices as a CSV file.
     *
     * @param Request $request
     * @return StreamedResponse|null
     * @since 0.0.1
     * @throws Exception If there is an error during the export process.
     *
     */
    public function export(Request $request): ?StreamedResponse {
        $this->validateAdmin();

        try {
            $obj = new CsvProcessor();
            return $obj->export();
        } catch (Throwable $th) {
            Log::error("Error exporting CSV: " . $th->getMessage());
            return null;
        }
    }


    /**
     * Handle the upload action.
     *
     * @param Request $request
     * @return Redirector|RedirectResponse
     * @since 0.0.1
     */
    public function upload(Request $request): Redirector|RedirectResponse {
        $this->validateAdmin();

        try {


            $file = $request->file('csv');
            $url = route('device-importer.import');
            if (empty($file)) {
                $type = 'error';
                $message = 'No file uploaded';
                Log::error('No file uploaded');
                return $this->redirect(
                    $url,
                    $type,
                    $message
                );
            }

            // Check if the file is valid
            if (! $file->isValid()) {
                $type = 'error';
                $message = 'Invalid file upload';
                Log::error('Invalid file upload');
                return $this->redirect(
                    $url,
                    $type,
                    $message
                );
            }

            $return = $request->validate([
                'csv' => 'required|file|mimes:csv,txt',
            ]);

            $mimeType = $file->getMimeType($file);
            if ($mimeType !== 'text/csv') {
                $type = 'error';
                $message = 'Invalid file';
                Log::error('Invalid file MIME type: ' . $mimeType);
                return $this->redirect(
                    $url,
                    $type,
                    $message
                );
            }

            $data = file($file->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $path = $file->getRealPath();
            //unlink($path);

            $config = PluginDb::getDbConnectionConfig();
            $databaseConnection = app('db.factory')->make($config, PluginDb::PLUGIN_DB_CONNECTION);


            // 2. Resolve the explicit database connection instance from the manager
            //$databaseConnection = app('db')->connection('plugin_db');

            // 3. Manually construct the Database Queue Driver
            $queueConnection = new \Illuminate\Queue\DatabaseQueue(
                $databaseConnection,             // The DB connection instance
                'jobs',                          // The physical table target inside librenms_plugin_db
                'plugin_queue',                  // The default queue channel
                90                               // Retry time value
            );

            // 4. FIX: Manually assign the application container to satisfy framework requirements
            $queueConnection->setContainer(app());

            // 5. Explicitly push your Job class directly to the custom worker container instance
            $queueConnection->push(new ImportDeviceJob($data));

            //ImportDeviceJob::dispatch($data);


            return $this->redirect(
                $url,
                'success',
                'File uploaded successfully'
            );
        } catch (Throwable $th) {
            Log::error("Error during file upload: " . $th->getMessage());
            return $this->redirect(
                route('device-importer.import'),
                'error',
                'An error occurred during file upload'
            );
        }
    }

    /**
     * Save settings
     *
     * @param Request $request
     * @return Redirector|RedirectResponse
     * @since 0.0.1
     */
    public function save(Request $request): Redirector|RedirectResponse {

        $database = $request->input('database', '');
        $username = $request->input('username', '');
        $password = $request->input('password', '');

        $result = $this->settings->set('database', $database);
        $result = $this->settings->set('username', $username) && $result;
        $result = $this->settings->set('password', $password) && $result;

        $type = 'success';
        $message = 'Settings saved successfully';
        if (!$result) {
            $type = 'error';
            $message = 'Failed to save settings';
        }

        return $this->redirect(
            route('device-importer.settings'),
            $type,
            $message
        );
    }

    /**
     * Do redirect to the plugin page with an optional status.
     *
     * @param string|null $url The URL to redirect to.
     * @param string|null $type The type of message (e.g., 'error', 'success').
     * @param string|null $message The message to display after the redirect.
     * @return Redirector|RedirectResponse
     */
    private function redirect(
        ?string $url = null,
        ?string $type = null,
        ?string $message = null
    ): Redirector|RedirectResponse {

        $query = [];

        if (is_null($url)) {
            $url = url('plugin/' . PluginData::PLUGIN);
        }

        if ($type !== null) {
            return redirect($url)->with($type, $message);
        }
        return redirect($url);
    }
}
