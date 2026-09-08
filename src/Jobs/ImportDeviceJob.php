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

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use DRP\DeviceImporter\CsvProcessor;


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

    protected string $fileName;

    /**
     * Force this job onto your separate DB queue connection.
     */
    public $connection = 'plugin_database_queue';

    /**
     * Force failures to write to the exact same isolated database connection!
     */
    public $failedConnection = 'plugin_db';


    public function __construct(string $fileName) {
        $this->fileName = $fileName;

        /*
        config(['queue.default' => 'redis']);
        config(
            [
                'failed' => [
                    'driver' => 'redis',
                    'table' => 'failed_jobs'
                ]
            ]
        );
        */
    }

    public function handle() {
        $obj = new CsvProcessor();
        if (! $obj->import($this->fileName)) {
            $this->fail("Import failed for file: $this->fileName");
            return;
        }



        // ...
        Log::debug('Processing file: ', [$this->fileName]);
    }
}
