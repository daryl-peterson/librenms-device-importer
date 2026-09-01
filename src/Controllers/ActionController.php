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

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use DRP\DeviceImporter\DeviceImporter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use DateTime;

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
    public function __construct() {
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

        Log::debug('FILE', [$file]);

        // Process the CSV file here
        // ...


        Log::debug('CSV: ', $job);

        $date = new DateTime();
        $safeName = $date->format('YmdHis') . "-device-import.csv";

        $this->addJob($file->get());

        $path = $file->storeAs('uploads', $safeName);



        return $this->redirect('success');
    }

    private function addJob(string $payload) {
        try {
            $job = [];
            $job['queue'] = 'device_import';
            $job['payload'] = $payload;

            $job['attempts'] = 0;
            $job['reserved_at'] = null;
            $job['available_at'] = time();
            $job['created_at'] = time();
            config(['queue.connections.database.table' => 'plugin_jobs']);
            dispatch(new \DRP\DeviceImporter\Jobs\ImportDataJob($job));

            Log::debug('JOB: ', $job);
        } catch (\Throwable $th) {
            Log::error('Failed to add job', ['exception' => $th]);
        }
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
