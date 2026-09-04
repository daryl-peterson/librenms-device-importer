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



	public function __construct(string $fileName) {
		$this->fileName = $fileName;
		config(['queue.default' => 'redis']);
	}

	public function handle() {
		// Process the data here
		// ...
		Log::debug('Processing file: ', [$this->fileName]);
	}
}
