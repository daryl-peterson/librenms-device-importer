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
use LibreNMS\Exceptions\HostExistsException;
use LibreNMS\Exceptions\HostUnreachableException;
use LibreNMS\Exceptions\SnmpVersionUnsupportedException;
use Symfony\Component\HttpFoundation\StreamedResponse;


/**
 * Plugin imports.
 */


use DRP\DeviceImporter\TraitHidePrivates;
use DRP\DeviceImporter\Log;

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
        Log::debug('Initializing CsvProcessor object.');
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
     * Import devices from an array of CSV data.
     *
     * @param array $data The CSV data to import.
     * @return bool
     * @since 0.0.1
     */
    public function import(array $data): bool {
        Log::error('Starting import from array');

        $headers = array_shift($data);
        if (!$this->isValidHeader($headers)) {
            Log::error('CSV Header error:', $headers);
            return false;
        }


        foreach ($data as $line) {
            $cols = $this->lineToCols($line);
            $result = $this->processCols($cols);
        }
        return true;
    }

    /**
     * Process a single CSV row and create a device.
     *
     * @param array $row The CSV row to process.
     * @return bool
     */
    private function processCols(array $row): bool {

        Log::debug('Processing CSV row: ' . PHP_EOL . print_r($row, true));

        try {

            $objDevice = $this->initDevice($row);
            if (!$objDevice) {
                return false;
            }

            $result = (new ValidateDeviceAndCreate($objDevice))->execute();
            return $result;
        } catch (HostExistsException) {
            Log::error("Host already exists: " . PHP_EOL . print_r($row, true));
        } catch (HostUnreachableException) {
            Log::error("Host unreachable: " . PHP_EOL . print_r($row, true));
        } catch (SnmpVersionUnsupportedException) {
            Log::error("SNMP version unsupported: " . PHP_EOL . print_r($row, true));
        } catch (Throwable $th) {
            Log::error("Error Processing CSV: " . $th->getMessage() . PHP_EOL . print_r($row, true));
        }
        return false;
    }

    /**
     * Initialize a device array from a CSV row.
     *
     * @param array $row The CSV row to process.`
     * @return Device|null The device model if it does not already exist, or null if invalid.
     * @since 0.0.1
     */
    private function initDevice(array $row): ?Device {
        try {
            $device = [];
            foreach ($row as $key => $value) {

                $col = $this->csvHeaders[$key] ?? null;
                if ($col === null) {
                    continue;
                }

                $device[$col] = trim($value);
            }

            // Ensure the device array has the expected number of columns.
            $expectCnt = count($this->csvHeaders);
            if (count($device) !== $expectCnt) {
                Log::error('Device array does not match expected column count:', $device);
                return null;
            }

            $model = Device::where('hostname', $device['hostname'])->first();
            // If the device already exists, return null.
            if ($model) {
                return null;
            }
            return new Device($device);
        } catch (Throwable $th) {
            Log::error("Error initializing device: " . $th->getMessage());
            return null;
        }
        return null;
    }

    /**
     * Check if the expected headers match.
     * @param string $headers CSV header row as a string
     * @return bool
     * @since 0.0.1
     */
    private function isValidHeader(string $headers): bool {
        $headers = $this->lineToCols($headers);

        foreach ($headers as $key => $header) {
            $header = $this->sanitizeString($header);
            $headers[$key] = $header;
        }

        foreach ($this->csvHeaders as $key => $header) {
            $col = $headers[$key] ?? null;
            if ($col !== $header) {

                return false;
            }
        }
        return true;
    }

    /**
     * Convert a CSV line to an array.
     *
     * @param string $line CSV line as a string
     * @return array
     * @since 0.0.1
     */
    private function lineToCols(string $line): array {
        $line = $this->sanitizeString($line);
        return explode(',', $line);
    }

    /**
     * Sanitize a string by removing unwanted characters.
     *
     * @param string $string The string to sanitize
     * @return string
     * @since 0.0.1
     */
    private function sanitizeString(string $string): string {
        $string = trim($string);
        $string = preg_replace('/[^\x00-\x7F]/', '', $string);
        $search = array("'", '"', "\r\n", "\n", "\r");
        $string = str_replace($search, '', $string);
        return $string;
    }
}
