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


        return match ($action) {
            'upload' => $this->upload($request,),
            default => $this->redirect('unknown_action'),
        };
    }

    public function upload(Request $request) {

        if (!Auth::check() || Auth::user()->hasRole('admin')) {
            return $this->redirect('permission_denied');
        }

        $file = $request->file('csv');
        if (empty($file)) {
            return $this->redirect('no_file');
        }

        // Check if the file is valid
        if (! $file->isValid()) {
            return $this->redirect('invalid_file');
        }

        $mimeType = $this->getMimeType($file);
        if ($mimeType !== 'text/csv') {
            return $this->redirect('invalid_file');
        }

        // Process the CSV file here
        // ...

        return $this->redirect('success');
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
