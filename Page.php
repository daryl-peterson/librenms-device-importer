<?php

/**
 * Page for the Device Importer plugin.
 *
 * @package     App\Plugins\DeviceImporter
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       1.0.0
 */

namespace App\Plugins\DeviceImporter;

use App\Plugins\Hooks\PageHook;
use Illuminate\Support\Facades\Log;

try {
    require_once __DIR__ . '/src/includes.php';
} catch (\Exception $e) {
    Log::error('Failed to include includes.php: ' . $e->getMessage());
}

/**
 * Page for the Device Importer plugin.
 *
 * @package     App\Plugins\DeviceImporter
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       1.0.0
 */
class Page extends PageHook {


    public function data(): array {
        return [
            'info' => Importer::getInfo(),
        ];
    }
}
