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

use Exception;
use DRP\DeviceImporter\CsvProcessor;
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
    public $connection = 'plugin_database_queue';

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
    }

    public function handle() {

        try {
            $obj = new CsvProcessor();
            Log::debug('Starting import for file: ' . $this->fileName);
            if (! $obj->import($this->fileName)) {
                $this->fail("Import failed for file: $this->fileName");
                return;
            }

        } catch (Exception $e) {
            Log::error('Import error: ' . $e->getMessage() . PHP_EOL);
            Log::error($e->getTraceAsString());
            $this->fail("Import failed for file: $this->fileName ". PHP_EOL . $e->getTraceAsString());
        }

    }
}
