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

use DRP\DeviceImporter\Log;
use DRP\DeviceImporter\PluginDb;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

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
    protected $signature = 'device-importer:process-plugin-queue';

    /**
     * The console command description.
     */
    protected $description = 'Injects configs dynamically and fires the Laravel queue worker';

    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int The exit code of the command.
     */
    public function handle() {

        //$dbName = PluginDb::getDbConnection();
        $queueName = 'default';
        $tries = 3;
        $exitCode = 0;

        try {

            //PluginDb::setDefaults();

            /*

            $settings = PluginData::getSettings();
            $conn = PluginDb::PLUGIN_DB_CONNECTION;


            $config = [
                'driver'    => 'mysql',
                'host'      => $settings['host'] ?? PluginDb::PLUGIN_DB_HOST,
                'database'  => $settings['database'] ?? PluginDb::PLUGIN_DB_DATABASE,
                'username'  => $settings['username'] ?? PluginDb::PLUGIN_DB_USERNAME,
                'password'  => $settings['password'] ?? PluginDb::PLUGIN_DB_PASSWORD,
                'charset'   => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix'    => '',
            ];


            // 1. Inject the Database Connection Array into memory
            Config::set("database.connections.{$conn}", $config);
            $info = Config::get("database.connections.{$conn}");

            // Mask the password for logging purposes
            if (isset($info['password'])) {
                $info['password'] = '********';
            }


            Log::info(
                "Database connection info being injected: ",
                ["database.connections.{$conn}" => $info]
            );

            $config = [
                'connection' => $conn,
                'driver'     => 'database',
                'table'      => 'jobs',
                'queue'      => 'default',
                'retry_after' => 90,
            ];

            // 2. Inject the Queue Connection mapping that points to the DB above

            Config::set('queue.connections.plugin_queue', $config);
            $info = Config::get('queue.connections.plugin_queue');
            Log::info(
                "Queue connection info injected: ",
                ['queue.connections.plugin_queue' => $info]
            );
            */


            // 3. Programmatically hand control over to the native queue worker
            $exitCode = Artisan::call("queue:work", [
                'connection'          => 'plugin_queue',
                '--stop-when-empty' => true,
                '--tries'           => $tries
            ]);

            if ($exitCode === 2) {
                Log::warning("Plugin queue completed, but some jobs failed. Output: " . Artisan::output());
                $exitCode = 0;
            }

            if ($exitCode !== 0) {
                Log::error("Queue worker exited with an error code [{$exitCode}]. Output: " . Artisan::output());
                $exitCode = 1;
            }
        } catch (\Throwable $th) {
            $this->error("Error processing plugin queue: " . $th->getMessage());
            return 1;
        }
        return $exitCode; // Return the determined exit code
    }
}
