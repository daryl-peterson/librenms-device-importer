<?php

/**
 * LibreNMS Device Importer Import Status Model.
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
use DRP\DeviceImporter\PluginDb;

/**
 * LibreNMS Device Importer Import Status Model.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 *
 * @property string $hostname
 * @property string $os
 * @property string $snmpver
 * @property string $community
 * @property bool $snmp_disable
 * @property bool $failed
 * @property string $status
 * @property \DateTime $imported_at
 */
class ImportStatusModel extends Model {

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = '';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'import_status';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'hostname',
        'os',
        'snmpver',
        'community',
        'snmp_disable',
        'failed',
        'status',
        'imported_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'snmp_disable' => 'boolean',
        'failed' => 'boolean',
    ];

    /**
     * Import Status Model constructor.
     *
     * @param array $attributes
     * @since 0.0.1
     */
    public function __construct(array $attributes = []) {
        $this->connection = PluginDb::getDbConnection();
        return parent::__construct($attributes);
    }
}
