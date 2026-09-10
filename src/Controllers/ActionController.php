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

use Exception;
use Throwable;

/**
 * Laravel imports.
 */

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Plugin imports.
 */

use DRP\DeviceImporter\CsvProcessor;
use DRP\DeviceImporter\DbCheck;
use DRP\DeviceImporter\DeviceImporter;
use DRP\DeviceImporter\FileManager;
use DRP\DeviceImporter\Jobs\ImportDeviceJob;
use DRP\DeviceImporter\PluginSettings;
use DRP\DeviceImporter\TraitHidePrivates;
use DRP\DeviceImporter\TraitValidateAdmin;

use function DRP\DeviceImporter\isAdmin;

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

        if (! isAdmin()) {
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
        } catch (Throwable $e) {
            doErrorMsg($e);
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
            DbCheck::isReady();

            $file = $request->file('csv');

            Log::debug('CSV file: ', [$file]);
            $url = route('device-importer.upload');
            if (empty($file)) {
                $type = 'error';
                $message = 'No file uploaded';
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
                return $this->redirect(
                    $url,
                    $type,
                    $message
                );
            }

            $return = $request->validate([
                'csv' => 'required|file|mimes:csv,txt',
            ]);
            Log::debug('Validation result: ', [$return]);

            $mimeType = $file->getMimeType($file);
            Log::debug('CSV MIME type: ' . $mimeType);
            if ($mimeType !== 'text/csv') {
                $type = 'error';
                $message = 'Invalid file';
                return $this->redirect(
                    $url,
                    $type,
                    $message
                );
            }

            FileManager::deleteAll();
            $fileName = FileManager::addFile($file);
            Log::debug('File added: ' . $fileName);

            ImportDeviceJob::dispatch($fileName);

            return $this->redirect(
                $url,
                'success',
                'File uploaded successfully'
            );
        } catch (Throwable $e) {
            doErrorMsg($e);
            return $this->redirect(
                route('device-importer.upload'),
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

        $communities = $request->input('communities', '');
        $result = $this->settings->set('communities', $communities);

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
            $url = url('plugin/' . DeviceImporter::PLUGIN);
        }

        if ($type !== null) {
            return redirect($url)->with($type, $message);
        }
        return redirect($url);
    }
}
