<?php

/**
 * LibreNMS Device Importer Plugin Process Plugin Queue Command.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */

namespace DRP\DeviceImporter\Console;

/**
 * Laravel and Artisan imports.
 */

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

/**
 * Plugin imports
 */

use DRP\DeviceImporter\DbCheck;

/**
 * LibreNMS Device Importer Plugin Process Plugin Queue Command.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */
class ProcessPluginQueue extends Command {

    /**
     * The name and signature of the console command.
     */
    protected $signature = 'plugin:process-plugin-queue {--tries=3}';

    /**
     * The console command description.
     */
    protected $description = 'Injects configs dynamically and fires the Laravel queue worker';

    public function handle() {
        $dbName = 'plugin_db';
        $queueName = 'default';
        $tries = $this->option('tries');

        // 1. Inject the Database Connection Array into memory
        config([
            "database.connections.{$dbName}" => [
                'driver'    => 'mysql',
                'host'      => env('PLUGIN_DB_HOST', '127.0.0.1'),
                'database'  => env('PLUGIN_DB_DATABASE', DbCheck::PLUGIN_DB_DATABASE),
                'username'  => env('PLUGIN_DB_USERNAME', DbCheck::PLUGIN_DB_USERNAME),
                'password'  => env('PLUGIN_DB_PASSWORD', ''),
                'charset'   => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix'    => '',
            ]
        ]);

        // 2. Inject the Queue Connection mapping that points to the DB above
        config([
            'queue.connections.plugin_queue' => [
                'driver'     => 'database',
                'table'      => 'jobs',
                'queue'      => $queueName,
                'connection' => $dbName, // References the connection injected above
                'retry_after' => 90,
            ]
        ]);

        $this->info("Successfully injected configs. Booting worker on db [{$dbName}]...");

        // 3. Programmatically hand control over to the native queue worker
        Artisan::call('queue:work', [
            'connection'        => 'plugin_queue',
            '--stop-when-empty' => true,
            '--tries'           => $tries,
        ], $this->output);

        return 0;
    }
}
