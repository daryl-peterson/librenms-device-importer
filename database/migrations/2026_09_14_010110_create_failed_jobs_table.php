<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use DRP\DeviceImporter\PluginDb;

return new class extends Migration {
    private string $conn;
    private string $tbl;

    public function __construct() {
        $this->conn = PluginDb::getDbConnection();
        $this->tbl = 'failed_jobs';
    }
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::connection($this->conn)->dropIfExists($this->tbl);
        Schema::connection($this->conn)->create($this->tbl, function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique(); // Unique string ID for tracking and retrying
            $table->text('connection');       // Name of the queue connection (e.g., redis, database)
            $table->text('queue');            // Name of the specific queue (e.g., default, high)
            $table->longText('payload');      // JSON-encoded string holding your serialized Job object
            $table->longText('exception');    // The full error message and PHP stack trace
            $table->timestamp('failed_at')
                ->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::connection($this->conn)->dropIfExists($this->tbl);
    }
};
