<?php

/**
 * CSV Processor for the LibreNMS Device Importer plugin.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */

namespace DRP\DeviceImporter;

/**
 * Standard PHP imports.
 */

use Exception;
use Throwable;

/**
 * Laravel and application imports.
 */

use App\Actions\Device\ValidateDeviceAndCreate;
use App\Models\Device;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Plugin imports.
 */

use DRP\DeviceImporter\Log;
use DRP\DeviceImporter\TraitHidePrivates;


/**
 * LibreNMS Device Importer CSV Processor class.
 *
 * @package     device-importer`
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 *
 * @todo Add logic to get more info on the import and create screen for status.
 */
class CsvProcessor {
    use TraitHidePrivates;

    /**
     * CSV headers for import/export.
     *
     * @var array
     */
    private array $csvHeaders;

    /**
     * Object constructor.
     *
     * Initializes object properties, specifically the CSV headers for import/export.
     */
    public function __construct() {
        $this->csvHeaders = ['hostname', 'hardware', 'serial', 'os', 'snmpver', 'community', 'snmp_disable'];
    }

    /**
     * Export devices to a CSV file.
     *
     * @return StreamedResponse|null If the export is successful, returns a StreamedResponse; otherwise, returns null.
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
        } catch (Throwable $th) {
            Log::error("Error exporting CSV: " . $th->getMessage());
            return null;
        }
    }


    /**
     * Import devices from a CSV file.
     *
     * @param string $fileName The name of the CSV file to import.
     * @return bool
     * @since 0.0.1
     * @throws Exception If the file cannot be opened.
     */
    public function import(string $fileName): bool {

        $path = FileManager::getStorageDir() . $fileName;

        $handle = fopen($path, 'r');
        if ($handle === false) {
            Log::error("Error opening CSV file: " . $path);
            throw new Exception("Unable to open file: $path");
        }

        // Optional: If your CSV has a header row, read it first to skip or capture it
        $headers = fgetcsv($handle, null, ',', '"', "\n");

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $result = $this->processCsvRow($row);
        }

        // Close the file pointer
        fclose($handle);


        return true;
    }

    /**
     * Process a single CSV row and create a device.
     *
     * @param array $row The CSV row to process.
     * @return bool
     */
    private function processCsvRow(array $row): bool {

        Log::debug('Processing CSV row: ' . PHP_EOL . print_r($row, true));

        try {
            $expectCnt = count($this->csvHeaders);
            if (count($row) !== $expectCnt) {
                Log::error('CSV row does not match expected column count: ' . PHP_EOL . print_r($row, true));
                return false;
            }

            $device = [];
            foreach ($row as $key => $value) {

                $col = $this->csvHeaders[$key] ?? null;
                if ($col === null) {
                    continue;
                }

                $device[$col] = trim($value);
            }
            if (count($device) !== $expectCnt) {
                Log::error('Processed device does not match expected column count: ' . PHP_EOL . print_r($device, true));
                return false;
            }

            $objDevice = new Device($device);
            $result = (new ValidateDeviceAndCreate($objDevice))->execute();
            return $result;
        } catch (Throwable $th) {
            Log::error("Error processing CSV row: " . $th->getMessage());
            return false;
        }
    }


    public function importArray(array $data): bool {
        foreach ($data as $row) {
            $result = $this->processCsvRow($row);
            if (!$result) {
                return false;
            }
        }
        return true;
    }
}
