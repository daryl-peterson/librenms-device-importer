<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('plugin_device_import', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('hostname')->index();
            $table->binary('ip_address', length: 16)->nullable();
            $table->string('snmp_version')->default('v2c');
            $table->string('snmp_community')->nullable();
            $table->string('import_status')->default('pending');
            $table->text('error_message')->nullable();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->unsignedInteger('created_at');
            $table->unsignedInteger('updated_at');
        });
    }

    public function down() {
        Schema::dropIfExists('plugin_device_import');
    }
};
