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
