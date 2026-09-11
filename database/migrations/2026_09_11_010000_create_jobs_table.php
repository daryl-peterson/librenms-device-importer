<?php

use DRP\DeviceImporter\PluginDb;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private string $conn;
    private string $tbl;

    public function __construct() {
        $this->conn = PluginDb::getDbConnection();
        $this->tbl = 'jobs';
    }

    /**
     * Run the migrations.
     */
    public function up(): void {
        if (!Schema::connection($this->conn)->hasTable($this->tbl)) {
            Schema::connection($this->conn)->create($this->tbl, function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('queue')->index();
                $table->longText('payload');
                $table->unsignedTinyInteger('attempts');
                $table->unsignedInteger('reserved_at')->nullable();
                $table->unsignedInteger('available_at');
                $table->unsignedInteger('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::connection($this->conn)->dropIfExists($this->tbl);
    }
};
