<?php

/**
 * Page class for the DeviceImporter plugin
 *
 * @package     App\Plugins\DeviceImporter
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/librenms-device-importer
 * @since       1.0.0
 */

namespace App\Plugins\DeviceImporter;

use App\Plugins\Hooks\PageHook;

require_once __DIR__ . '/src/includes.php';

/**
 * Page class for the DeviceImporter plugin
 *
 * @package     App\Plugins\DeviceImporter
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/librenms-device-importer
 * @since       1.0.0
 */
class Page extends PageHook {


    // override the data function to add additional data to be accessed in the view
    // default just passes the stored data through
    // inside the blade, all variables will be named based on the key in the returned array
    public function data(): array {


        return [
            'something' => 'this is a variable and can be accessed with {{ $something }}',
        ];
    }
}
