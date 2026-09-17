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
        Schema::connection($this->conn)->dropIfExists($this->tbl);

        Schema::connection($this->conn)->create(
            $this->tbl,
            function (Blueprint $table) {
                $table->id();
                $table->string('hostname', 150);
                $table->string('os', 100)->nullable();
                $table->string('snmpver', 50)->nullable();
                $table->string('community', 100)->nullable();
                $table->integer('snmp_disable')->nullable();
                $table->tinyInteger('failed')->default(0);
                $table->string('status', 255)->nullable();
                $table->dateTime('imported_at')->nullable();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::connection($this->conn)->dropIfExists($this->tbl);
    }
};
