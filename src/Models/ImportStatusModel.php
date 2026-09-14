<?php

namespace DRP\DeviceImporter\Models;

use Illuminate\Database\Eloquent\Model;
use DRP\DeviceImporter\PluginDb;


class ImportStatusModel extends Model {
    protected $connection = '';
    protected $table = 'import_status';
    public $timestamps = false;

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


    public function __construct(array $attributes = []) {
        $this->connection = PluginDb::getConnectionName();
        return parent::__construct($attributes);
    }
}
