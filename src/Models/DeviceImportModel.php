<?php

namespace DRP\DeviceImporter\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class DeviceImportModel extends Model {
    protected $table = 'plugin_device_import';
    protected $fillable = [
        'hostname',
        'ip_address',
        'snmp_version',
        'snmp_community',
        'import_status',
        'error_message',
        'created_at',
        'updated_at',
    ];


     protected function ip_address(): Attribute
    {
        return Attribute::make(
            // Executed when reading $product->price (After Get)
            get: fn (mixed $value) => inet_ntop($value),


            set: fn (string $value) => inet_pton($value),
        );
    }
    //INET6_ATON('192.168.1.10');

    /*
                $table->bigIncrements('id');
            $table->string('hostname')->index();
            $table->string('ip_address')->nullable();
            $table->string('snmp_version')->default('v2c');
            $table->string('snmp_community')->nullable();
            $table->string('import_status')->default('pending');
            $table->text('error_message')->nullable();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->unsignedInteger('created_at');
            $table->unsignedInteger('updated_at');
    */
}
