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

use App\Actions\Device\ValidateDeviceAndCreate;
use App\Models\Device;
use DRP\DeviceImporter\Log;
use DRP\DeviceImporter\Models\ImportStatusModel;
use DRP\DeviceImporter\TraitHidePrivates;
use Exception;
use Illuminate\Support\Facades\DB;
use LibreNMS\Exceptions\HostExistsException;
use LibreNMS\Exceptions\HostUnreachableException;
use LibreNMS\Exceptions\SnmpVersionUnsupportedException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;


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
        Log::debug('Starting import from array');

        $headers = array_shift($data);
        if (!$this->isValidHeader($headers)) {
            Log::error('CSV Header error:', [$headers]);
            return false;
        }

        foreach ($data as $line) {
            $cols = $this->lineToCols($line);
            $this->processCols($cols);
        }
        return true;
    }

    /**
     * Process a single CSV row and create a device.
     *
     * @param array $row The CSV row to process.
     * @return bool
     * @since 0.0.1
     */
    private function processCols(array $row): bool {

        try {
            $device = $this->mapArray($row);
            $objDevice = $this->initDevice($device);

            $result = (new ValidateDeviceAndCreate($objDevice))->execute();
            if (!$result) {
                $this->setImportStatus($device, true, 'Validation failed');
                return false;
            }
            $this->setImportStatus($device, false, 'Success');
            return $result;
        } catch (HostExistsException) {
            Log::error("Host already exists:", $device);
            $this->setImportStatus($device, true, 'Host already exists');
        } catch (HostUnreachableException) {
            Log::error("Host unreachable:",  $device);
            $this->setImportStatus($device, true, 'Host unreachable');
        } catch (SnmpVersionUnsupportedException) {
            Log::error("SNMP version unsupported:",    $device);
            $this->setImportStatus($device, true, 'SNMP version unsupported');
        } catch (Throwable $th) {
            Log::error("Error Processing CSV: " . $th->getMessage(),  $device);
            $this->setImportStatus($device, true, 'Error Processing CSV: ' . $th->getMessage());
        }

        return false;
    }

    /**
     * Check if the device array has the expected number of columns.
     *
     * @param array $device The device array to check.
     * @return bool True if the column count matches, false otherwise.
     * @since 0.0.1
     */
    private function checkColCounts(array $device): bool {
        $expectCnt = count($this->csvHeaders);
        return count($device) === $expectCnt;
    }

    /**
     * Initialize a device array from a CSV row.
     *
     * @param array $device The associative array representing the device.
     * @return Device The device model if it does not already exist, or null if invalid.
     * @throws Exception If the device array does not match the expected column count.
     * @throws HostExistsException If the device already exists.
     * @since 0.0.1
     */
    private function initDevice(array $device): Device {
        Log::debug('Initializing device from array:', $device);

        if (!$this->checkColCounts($device)) {
            Log::error('Device array does not match expected column count:', $device);
            throw new Exception('Device array does not match expected column count.');
        }


        $model = Device::where('hostname', $device['hostname'])->first();
        // If the device already exists, return null.
        if ($model) {
            throw new HostExistsException('Host already exists. ' . $device['hostname']);
        }
        return new Device($device);
    }

    /**
     * Check if the expected headers match.
     *
     * @param string $headers CSV header row as a string
     * @return bool
     * @since 0.0.1
     */
    private function isValidHeader(string $headers): bool {
        $headers = $this->lineToCols($headers);

        foreach ($headers as $key => $header) {
            $headers[$key] = $this->sanitizeString($header);
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
     * Map an indexed array to an associative array using CSV headers.
     *
     * @param array $array Indexed array of column values.
     * @return array Associative array with column headers as keys.
     * @since 0.0.1
     */
    private function mapArray(array $array): array {
        $device = [];
        foreach ($array as $key => $value) {

            $col = $this->csvHeaders[$key] ?? null;
            if ($col === null) {
                continue;
            }

            $device[$col] = trim($value);
        }

        return $device;
    }

    /**
     * Convert a CSV line to an array.
     *
     * @param string $line CSV line as a string
     * @return array Indexed array of column values.
     * @since 0.0.1
     */
    private function lineToCols(string $line): array {
        $line = $this->sanitizeString($line);
        $line = str_getcsv($line, ',', '"', "\n");
        return $line;
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
        return $string;
    }

    /**
     * Set the import status for a device.
     *
     * @param array $device The device array.
     * @param bool $failed Whether the import failed.
     * @param string $status The import status message.
     * @since 0.0.1
     */
    private function setImportStatus(array $device, bool $failed, string $status): void {

        try {

            ImportStatusModel::updateOrCreate(
                [
                    'hostname' => $device['hostname'] ?? 'unknown',
                    'os' => $device['os'] ?? null,
                    'snmpver' => $device['snmpver'] ?? null,
                    'community' => $device['community'] ?? null,
                    'snmp_disable' => $device['snmp_disable'] ?? false,
                    'failed' => $failed,
                    'status' => $status,
                    'imported_at' => now(),
                ]
            );
        } catch (Throwable $th) {
            Log::error("Error setting import status: " . $th->getMessage());
        }
    }
}
