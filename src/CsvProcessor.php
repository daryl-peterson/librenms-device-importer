<?php

/**
 * Class description
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */

namespace DRP\DeviceImporter;

use DRP\DeviceImporter\TraitHidePrivates;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;


/**
 * Class description
 *
 * @package     device-importer`
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */
class CsvProcessor {
    use TraitHidePrivates;
    private array $csvHeaders;

    public function __construct() {
        $this->csvHeaders = ['hostname', 'hardware', 'serial', 'os', 'snmpver', 'community', 'snmp_disable'];
    }


    /**
     * Export devices to a CSV file.
     *
     * @return StreamedResponse|null
     * @since 0.0.1
     */
    public function export(): ?StreamedResponse {
        try {
            // Add the 'd.' prefix to each item in the array
            $prefixedHeaders = array_map(function ($header) {
                return 'd.' . $header;
            }, $this->csvHeaders);

            $fields = implode(',', $prefixedHeaders);
            $sql = "SELECT $fields FROM devices d";

            $results = DB::select($sql);

            $response = new StreamedResponse(function () use ($results) {
                $handle = fopen('php://output', 'w');

                // Add CSV Headers
                fputcsv($handle, $this->csvHeaders);

                // Add Data Rows
                foreach ($results as $row) {
                    fputcsv($handle, [$row->hostname, $row->hardware, $row->serial, $row->os, $row->snmpver, $row->community, $row->snmp_disable]);
                }

                fclose($handle);
            });

            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="librenms-export.csv"');

            return $response;
        } catch (Exception $e) {
            Log::error('Export error: ' . $e->getMessage() . PHP_EOL);
            Log::error($e->getTraceAsString());
            return null;
        }
    }


    public function import(string $fileName): bool {



        //$path = storage_path('app/uploads/' . $fileName);
        $path = storage_path('uploads2/' . $fileName);

        $handle = fopen($path, 'r');
        if ($handle === false) {
            Log::error("Unable to open file: $path");
            throw new Exception("Unable to open file: $path");
        }

        // Optional: If your CSV has a header row, read it first to skip or capture it
        $headers = fgetcsv($handle);

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            // $row is now a simple numerical array of columns
            // Example: access first column via $row[0]
            Log::debug('CSV row: ', [$row]);
        }

        // Close the file pointer
        fclose($handle);


        return true;
    }
}
