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
        if (!Schema::connection($this->conn)->hasTable($this->tbl)) {
            Schema::connection($this->conn)->create($this->tbl, function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->longText('connection');
                $table->longText('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->unsignedInteger('failed_at');
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
