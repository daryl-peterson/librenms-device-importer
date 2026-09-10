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

use DRP\DeviceImporter\CsvProcessor;
use DRP\DeviceImporter\DbCheck;
use DRP\DeviceImporter\FileManager;
use Exception;
use Throwable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


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
    public $failedConnection = 'plugin_db';

    /**
     * Object constructor.
     *
     * @param string $fileName The name of the CSV file to import.
     */
    public function __construct(string $fileName) {
        $this->fileName = $fileName;

        DbCheck::setDefaults();
    }

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
            doErrorMsg($e);
            $this->fail("Import failed for file: $this->fileName " . PHP_EOL . $e->getTraceAsString());
        }
        $this->cleanup();
    }

    private function cleanup() {
        try {
            FileManager::deleteFile($this->fileName);
        } catch (Throwable $th) {
            doErrorMsg($th);
        }
    }
}
