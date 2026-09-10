<?php

/**
 * File manager class for adding, renaming, and deleting files in the uploads directory.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */

namespace DRP\DeviceImporter;

use DateTime;
use Throwable;
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
 * @since       0.0.1
 */
class FileManager {
    use TraitHidePrivates;

    /**
     * Add a file to the uploads directory.
     *
     * @param UploadedFile|array $file
     * @return string|array|null
     * @since 0.0.1
     */
    public static function addFile(UploadedFile|array $file): string|array|null {
        $fileName = null;
        try {
            if ($file instanceof UploadedFile) {
                $fileName = self::storeFile($file);
            } elseif (is_array($file)) {
                // Handle multiple file uploads
            }
        } catch (Throwable $th) {
            doErrorMsg($th);
            return null;
        }

        Log::debug('File added: ' . $fileName);
        return $fileName;
    }

    /**
     * Delete a file from the uploads directory.
     *
     * @param string $fileName The name of the file to delete.
     * @return boolean True if the file was successfully deleted, false otherwise.
     * @since 0.0.1
     */
    public static function deleteFile(string $fileName): bool {
        try {
            $fileName = basename($fileName);

            $path = self::getStorageDir() . $fileName;
            if (file_exists($path)) {
                return unlink($path);
            }
        } catch (Throwable $th) {
            doErrorMsg($th);
            return false;
        }

        return false;
    }

    /**
     * Delete all files matching the pattern "*device-import-src.csv" in the uploads directory.
     *
     * @since 0.0.1
     */
    public static function deleteAll() {

        try {
            $directory = self::getStorageDir();
            $files = glob($directory . "*device-import-src.csv");
            Log::debug('Files to delete: ' . PHP_EOL . print_r($files, true));

            foreach ($files as $file) {
                unlink($file);
            }
            Log::debug('All files deleted successfully.');
        } catch (Throwable $th) {
            doErrorMsg($th);
        }
    }

    /**
     * Get the storage directory for uploaded files.
     *
     * @return string The storage path
     * @since 0.0.1
     */
    public static function getStorageDir(): string {
        return storage_path('app/uploads/');
    }

    /**
     * Store a file in the uploads directory.
     *
     * @param UploadedFile $file The file to store in the uploads directory.
     * @return string|null The name of the stored file, or null if the storage failed.
     *
     * @throws \Exception If the file could not be stored.
     *
     * @since 0.0.1
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
