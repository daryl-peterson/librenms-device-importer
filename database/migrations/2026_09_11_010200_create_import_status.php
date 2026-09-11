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
        $this->tbl = 'import_status';
    }

    /**
     * Run the migrations.
     */
    public function up(): void {
        if (!Schema::connection($this->conn)->hasTable($this->tbl)) {
            Schema::connection($this->conn)->create(
                $this->tbl,
                function (Blueprint $table) {
                    $table->id();
                    $table->timestamps();
                }
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::connection($this->conn)->dropIfExists($this->tbl);
    }
};
