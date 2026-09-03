<?php

/**
 * Action Controller
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       1.0.0
 */

namespace DRP\DeviceImporter\Controllers;

use App\Models\Device;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use DRP\DeviceImporter\DeviceImporter;
use DRP\DeviceImporter\FileManager;
use DRP\DeviceImporter\ImportSettings;
use DRP\DeviceImporter\Jobs\ImportDeviceJob;
use DRP\DeviceImporter\SNMPTester;
use Illuminate\Support\Facades\Artisan;

/**
 * Action Controller
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       1.0.0
 */
class ActionController extends Controller {


    private array $headersRequired = [];
    private array $map = [];
    private ImportSettings $settings;

    public function __construct() {
        $this->headersRequired = ['hostname', 'ip_address', 'os'];
        $this->settings = new ImportSettings();
    }

    public function handle(Request $request) {
        $user = auth()->user();

        if (! $user || ! $user->can('global-read')) {
            abort(403, 'Forbidden');
        }

        $action = (string) $request->input('action', '');
        Log::debug('Action requested: ' . $action . ' by user: ' . Auth::id());


        return match ($action) {
            'upload' => $this->upload($request,),
            default => $this->redirect('unknown_action'),
        };
    }

    public function upload(Request $request) {
        Log::debug('Upload action initiated by user: ' . Auth::id());


        if (!Auth::check() || Auth::user()->hasRole('admin')) {
            //return $this->redirect('permission_denied');
        }

        $file = $request->file('csv');

        Log::debug('CSV file: ', [$file]);
        if (empty($file)) {
            return $this->redirect('no_file');
        }

        // Check if the file is valid
        if (! $file->isValid()) {
            return $this->redirect('invalid_file');
        }

        $request->validate([
            'csv' => 'required|file|mimes:csv,txt',
        ]);

        $mimeType = $file->getMimeType($file);
        Log::debug('CSV MIME type: ' . $mimeType);
        if ($mimeType !== 'text/csv') {
            return $this->redirect('invalid_file');
        }

        FileManager::deleteAll();
        $fileName = FileManager::addFile($file);


        ImportDeviceJob::dispatch($fileName);
        Artisan::call('queue:work', [
            '--stop-when-empty' => true,
            '--tries' => 3,
        ]);
        return $this->redirect('success');
    }



    private function processUpload(array $data) {

        $header = explode(',', array_shift($data));
        $this->parseCsvHeader($header);

        Log::debug('CSV Header Map: ' . print_r($map, true));
        Log::debug('CSV Header: ' . print_r($header, true));
        Log::debug('CSV Data: ' . print_r($data, true));


        foreach ($data as $key => $value) {
            $mappedData = $this->parseCsvLine($value);
            /*
            $line = explode(',', $value);

            foreach ($this->map as $column => $index) {
                $mappedData[$index] = trim($line[$index]);
            }
            */
            Log::debug('Mapped Data: ' . print_r($mappedData, true));
        }

        /*
        [hostname] => wainwright-sw10g-01.oklatel.net
        [ip_address] => 10.30.32.5
        [os] => Cisco IOS
        */



        $obj = new SNMPTester();
        $result = $obj->test('10.13.10.4', 'moly560311', '2c');
        Log::debug('SNMP Test Result: ' . print_r($result, true));
        //SNMPTester::test($mappedData['hostname'], 'public', 2);
    }

    private function parseCsvLine(string $line) {

        $line = explode(',', $line);
        $mappedData = [];
        foreach ($this->map as $column => $index) {

            $clean = preg_replace('/^["\'](.*)["\']$/', '$1', $line[$index]);
            $mappedData[$column] = trim($clean);
        }






        return $mappedData;
    }

    private function parseCsvHeader(array $header) {

        foreach ($header as $key => $column) {
            Log::debug('CSV Column: ' . $column);
            $column = trim($column);
            $column = strtolower($column);
            $column = str_replace(' ', '_', $column);
            $column = preg_replace('/^["\'](.*)["\']$/', '$1', $column);
            $header[$key] = $column;
        }

        Log::debug('Header: ' . print_r($header, true));

        $map = [];

        foreach ($header as $key => $column) {
            Log::debug('Mapping column: ' . $column);
            if (in_array($column, $this->headersRequired)) {
                $map[$column] = $key;
            }
        }

        $this->map = $map;
    }


    private function redirect(?string $status = null) {

        $query = [];

        if ($status !== null) {
            $query['status'] = $status;
        }

        $path = url('plugin/' . DeviceImporter::PLUGIN);
        return redirect($path . ($query ? '?' . http_build_query($query) : ''));
    }
}
