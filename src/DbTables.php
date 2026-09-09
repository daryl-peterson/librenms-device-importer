<?php

/**
 * Tables required by the plugin.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */

namespace DRP\DeviceImporter;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use DRP\DeviceImporter\DbCheck;

/**
 * Tables required by the plugin.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */
class DbTables {
    public function __construct() {
        # Code Here
    }

    public static function createTables() {
        $connection = DbCheck::getDbConnection();
        if (!Schema::connection($connection)->hasTable('jobs')) {
            Schema::connection($connection)->create('jobs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('queue')->index();
                $table->longText('payload');
                $table->unsignedTinyInteger('attempts');
                $table->unsignedInteger('reserved_at')->nullable();
                $table->unsignedInteger('available_at');
                $table->unsignedInteger('created_at');
            });
        }

        if (!Schema::connection($connection)->hasTable('failed_jobs')) {
            Schema::connection($connection)->create('failed_jobs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->longText('connection');
                $table->longText('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->unsignedInteger('failed_at');
            });
        }
    }
}
