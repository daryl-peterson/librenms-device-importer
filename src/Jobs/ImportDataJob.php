<?php

/**
 * Class description
 *
 * @package     NameSpace\ImportDataJob
 * @author      Daryl Peterson <@gmail.com>
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       1.0.0
 */

namespace DRP\DeviceImporter\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * Class description
 *
 * @package     NameSpace\ImportDataJob
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       1.0.0
 */
class ImportDataJob implements ShouldQueue {
    protected array $data;


    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'plugin_jobs';

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function handle() {
        // Process the data here
        // ...
        Log::debug('Processing data: ', [$this->data]);
    }
}
