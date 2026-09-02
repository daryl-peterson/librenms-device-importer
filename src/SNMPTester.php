<?php

/**
 * Class description
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       1.0.0
 */

namespace DRP\DeviceImporter;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use LibrenmsConfig;
use Exception;

/**
 * Class description
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       1.0.0
 */
class SNMPTester {

    public array $communityStrings = [];

    public function __construct() {
        # Code Here
    }


    public function test(string $hostname, string $community, string $version) {
        try {
        } catch (\Exception $e) {
            Log::error("SNMP test failed for host $hostname: " . $e->getMessage());
            return false;
        }

        try {
            $cmd = ["snmpget", "-v", $version, "-c", $community, $hostname, ".1.3.6.1.2.1.1.1.0"];
            $proc = new \Symfony\Component\Process\Process($cmd);
            $proc->setTimeout(floatval(300));
            $proc->run();
            $output = $proc->getOutput();
            $sysDescr = trim($output);

            Log::debug('SNMP sysDescr: ', [$sysDescr]);

            if (!$sysDescr) {
                return false;
            } else {
                return true;
            }
        } catch (Exception $e) {
            Log::error("SNMP connection failed for host $hostname: " . $e->getMessage());
            return false;
        }
    }
}
