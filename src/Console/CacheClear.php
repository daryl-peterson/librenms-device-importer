<?php

/**
 * LibreNMS Device Importer Plugin Cache Clear Command.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */

namespace DRP\DeviceImporter\Console;

/**
 * Laravel and Artisan imports.
 */

use DRP\DeviceImporter\Log;
use DRP\DeviceImporter\PluginCache;
use Illuminate\Console\Command;


/**
 * LibreNMS Device Importer Plugin Cache Clear Command.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */
class CacheClear extends Command {

    /**
     * The name and signature of the console command.
     */
    protected $signature = 'device-importer:cache-clear';

    /**
     * The console command description.
     */
    protected $description = 'Clears the device importer cache';

    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int The exit code of the command.
     */
    public function handle() {

        $tries = 3;
        $exitCode = 0;

        try {
            $keys = PluginCache::keys();
            Log::info("Clearing cache keys: " . implode(', ', $keys));
            foreach ($keys as $key) {
                PluginCache::forget($key);
            }
        } catch (\Throwable $th) {
            $this->error("Error clearing cache: " . $th->getMessage());
            return 1;
        }
        return $exitCode; // Return the determined exit code
    }
}
