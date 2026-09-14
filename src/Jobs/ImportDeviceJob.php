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
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


/**
 * Plugin imports.
 */

use DRP\DeviceImporter\CsvProcessor;
use DRP\DeviceImporter\PluginDb;
use DRP\DeviceImporter\Log;

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
class ImportDeviceJob implements ShouldQueue, ShouldBeUnique {
    use Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * The data from the CSV file to import.
     */
    protected array $data;

    /**
     * Force this job onto your separate DB queue connection.
     */
    public $connection = 'plugin_queue';

    /**
     * Force failures to write to the exact same isolated database connection!
     */
    public $failedConnection = PluginDb::PLUGIN_DB_CONNECTION;

    /**
     * The number of seconds the job should be unique for.
     */
    public $uniqueFor = 3600;

    /**
     * The unique identifier for the job.
     */
    public $uniqueId = null;

    /**
     * Object constructor.
     *
     * @param array $data The data from the CSV file.
     * @since 0.0.1
     */
    public function __construct(array $data) {
        $this->failedConnection = PluginDb::getDbConnection();
        $this->data = $data;
        $this->uniqueId = md5(json_encode($data));

        PluginDb::setDefaults();
    }

    /**
     * Handle the job.
     *
     * @return void
     */
    public function handle(): void {


        Log::debug('Import array: ', [$this->data]);

        $obj = new CsvProcessor();
        $obj->import($this->data);
    }

    /**
     * Get the unique identifier for the job.
     *
     * @return string|null
     */
    public function uniqueId(): ?string {
        return $this->uniqueId;
    }
}
