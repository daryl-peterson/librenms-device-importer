<?php

/**
 * Plugin job model class
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */

namespace DRP\DeviceImporter\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Plugin job model class
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */
class Jobs extends Model {
	protected $table = 'jobs';

	public $timestamps = false;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 * @since 0.0.1
	 */
	protected $fillable = [
		'queue',
		'payload',
		'attempts',
		'reserved_at',
		'available_at',
		'created_at',
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 * @since 0.0.1
	 */
	protected $casts = [
		'id' => 'integer',
		'attempts' => 'integer',
		'reserved_at' => 'integer',
		'available_at' => 'integer',
		'created_at' => 'integer',
	];

	/**
	 * Get the payload as an array.
	 *
	 * @return array
	 * @since 0.0.1
	 */
	public function getPayloadDataAttribute(): array {
		return json_decode($this->payload, true) ?? [];
	}

	/**
	 * Determine whether the job is currently reserved.
	 *
	 * @return boolean
	 * @since 0.0.1
	 */
	public function getIsReservedAttribute(): bool {
		return $this->reserved_at !== null;
	}

	/**
	 * Determine whether the job is available to run.
	 *
	 * @return boolean
	 * @since 0.0.1
	 */
	public function getIsAvailableAttribute(): bool {
		return $this->available_at <= time()
			&& $this->reserved_at === null;
	}
}
