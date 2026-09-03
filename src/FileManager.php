<?php

/**
 * File manager class for adding, renaming, and deleting files in the uploads directory.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       1.0.0
 */

namespace DRP\DeviceImporter;

use DateTime;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;


/**
 * File manager class for handling file operations in the uploads directory.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       1.0.0
 */
class FileManager {
    use TraitHidePrivates;

    /**
     * Add a file to the uploads directory.
     *
     * @param UploadedFile|array $file
     * @return string|array|null
     * @since 1.0.0
     */
    public static function addFile(UploadedFile|array $file): string|array|null {
        $fileName = null;
        try {
            if ($file instanceof UploadedFile) {
                $fileName = self::storeFile($file);
            } elseif (is_array($file)) {
                // Handle multiple file uploads
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            Log::error($th->getTraceAsString() . PHP_EOL);
            return null;
        }

        return $fileName;
    }

    /**
     * Delete a file from the uploads directory.
     *
     * @param string $fileName
     * @return boolean
     * @since 1.0.0
     */
    public static function deleteFile(string $fileName): bool {
        try {
            $fileName = basename($fileName);
            $path = storage_path('uploads/' . $fileName);
            if (file_exists($path)) {
                return unlink($path);
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            Log::error($th->getTraceAsString());
            return false;
        }

        return false;
    }

    public static function deleteAll() {

        try {
            $directory = "../storage/app/uploads/";
            $files = glob($directory . "*device-import-src.csv");
            Log::debug('Files to delete: ' . PHP_EOL . print_r($files, true));

            foreach ($files as $file) {
                unlink($file);
            }
            Log::debug('All files deleted successfully.');
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            Log::error($th->getTraceAsString());
        }
    }

    /**
     * Store a file in the uploads directory.
     *
     * @param UploadedFile $file
     * @return string|null
     *
     * @since 1.0.0
     */
    private static function storeFile(UploadedFile $file): ?string {
        $date = new DateTime();
        $safeName = $date->format('YmdHis') . "-device-import-src.csv";

        $path = $file->storeAs('uploads', $safeName);

        if (empty($path) | !$path) {
            return null;
        }

        return $safeName;
    }
}
