<?php

namespace App\Plugins\DeviceImporter;

use Illuminate\Support\Facades\Log;

function loadLibs() {
    foreach (glob(__DIR__ . '/*.php') as $file) {
        try {
            if (basename($file) === 'includes.php') {
                continue;
            }
            require_once $file;
        } catch (\Throwable $th) {
            Log::error("Error including file: $file", ['exception' => $th]);
        }
    }
}

loadLibs();
