<?php


/**
 * Class description
 *
 * @package     DeviceImporter
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @since       1.0.0
 */

namespace App\Plugins\DeviceImporter;

use App\Plugins\Hooks\MenuEntryHook;


require_once __DIR__ . '/src/includes.php';

// this will create a menu entry in the plugin menu
// it should generally just be a
class Menu extends MenuEntryHook {

    // override the data function to add additional data to be accessed in the view
    // inside the blade, all variables will be named based on the key in the returned array
    public function data(array $settings = []): array {
        // inject settings and count how many we have so we can display it in the menu

        return [
            'count' => 32,
        ];
    }
}
