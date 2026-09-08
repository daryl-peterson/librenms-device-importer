<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use DRP\DeviceImporter\DeviceImporter;

return new class extends Migration {


    public function __construct() {
        $this->connection = 'plugin_db';
    }

    /**
     * Run the migrations.
     */
    public function up(): void {
        // Explicitly isolate this tracking table to your separate database connection
        Schema::connection($this->connection)->create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::connection($this->connection)->dropIfExists('failed_jobs');
    }
};
