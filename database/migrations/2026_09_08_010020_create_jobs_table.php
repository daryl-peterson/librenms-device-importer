<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use DRP\DeviceImporter\DeviceImporter;


return new class extends Migration {


    public function __construct() {
        $this->connection = 'plugin_db';
    }

    public function up() {
        // Explicitly target the external_db connection to create the table there
        Schema::connection($this->connection)->create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });
    }

    public function down() {
        Schema::connection($this->connection)->dropIfExists('jobs');
    }
};
