<?php

/**
 * Class description
 *
 * @package     DRP\DeviceImporter\GitHubInfo
 * @author      Daryl Peterson <@gmail.com>
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       1.0.0
 */

namespace DRP\DeviceImporter;

use Illuminate\Support\Facades\Log;


/**
 * Class description
 *
 * @package     DRP\DeviceImporter\GitHubInfo
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       1.0.0
 *
 * @todo Implement this.
 */
class GitHubInfo {
    public function __construct() {
        # Code Here
    }

    public static function getLatestRelease(string $repo = "daryl-peterson/librenms-device-importer"): ?string {
        try {
            $options = [
                "http" => [
                    "method" => "GET",
                    "header" => "User-Agent: PHP-Version-Fetcher\r\n"
                ]
            ];

            $context = stream_context_create($options);
            $response = file_get_contents("https://api.github.com/repos/{$repo}/releases/latest", false, $context);

            if ($response !== false) {
                $data = json_decode($response, true);
                return $data['tag_name'] ?? null;
            }
        } catch (\Throwable $th) {
            Log::error('Failed to fetch latest release info from GitHub: ' . $th->getMessage());
        }


        return null;
    }
}
