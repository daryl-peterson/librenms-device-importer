<?php

/**
 * Import Job for devices from a CSV file
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */

namespace DRP\DeviceImporter\Jobs;

/**
 * Standard PHP imports.
 */

use Throwable;

/**
 * Laravel and application imports.
 */

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


/**
 * Plugin imports.
 */

use DRP\DeviceImporter\CsvProcessor;
use DRP\DeviceImporter\Log;
use DRP\DeviceImporter\PluginDb;
use DRP\DeviceImporter\FileManager;

/**
 * Import Job for devices from a CSV file
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */
class ImportDeviceJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, SerializesModels;

    protected array $data;

    /**
     * The name of the CSV file to import.
     */
    protected string $fileName;

    /**
     * Force this job onto your separate DB queue connection.
     */
    public $connection = 'plugin_queue';

    /**
     * Force failures to write to the exact same isolated database connection!
     */
    public $failedConnection = PluginDb::PLUGIN_DB_CONNECTION;

    /**
     * Object constructor.
     *
     * @param string $fileName The name of the CSV file to import.
     * @param array $data The data from the CSV file.
     */
    public function __construct(string $fileName, array $data) {
        $this->failedConnection = PluginDb::getDbConnection();
        $this->fileName = $fileName;
        $this->data = $data;

        PluginDb::setDefaults();
    }

    /**
     * Handle the job.
     *
     * @return void
     */
    public function handle() {

        try {
            $obj = new CsvProcessor();
            Log::debug('Starting import for file: ' . $this->fileName);
            if (! $obj->import($this->fileName)) {
                $this->fail("Import failed for file: $this->fileName");
                $this->cleanup();
                return;
            }
        } catch (Throwable $e) {
            Log::error("Import failed for file: $this->fileName " . PHP_EOL . $e->getTraceAsString());
            $this->fail("Import failed for file: $this->fileName " . PHP_EOL . $e->getTraceAsString());
        }
        $this->cleanup();
    }

    /**
     * Cleanup after the job is processed.
     *
     * Deletes the CSV file used for import.
     *
     * @return void
     */
    private function cleanup() {
        try {
            FileManager::deleteFile($this->fileName);
        } catch (Throwable $th) {
            doErrorMsg($th);
        }
    }
}
