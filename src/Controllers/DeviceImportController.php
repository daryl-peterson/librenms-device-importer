<?php

namespace DRP\DeviceImporter\Controllers;


use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;


class DeviceImportController extends Controller {
    public function index(): View {


        $filepath = "../resources/views/layouts/menu.blade.php";
        $contents = (array) file_get_contents($filepath);
        Log::alert('CONTESTS', $contents);


        return view('device-importer::page');
    }
}
