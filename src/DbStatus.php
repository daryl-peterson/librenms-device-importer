<?php

/**
 * Plugin database status class.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */

namespace DRP\DeviceImporter;

use DRP\DeviceImporter\DbCheck;
use DRP\DeviceImporter\PluginCache;

/**
 * Plugin database status class.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */
class DbStatus {

    /**
     * Database ready status.
     *
     * @var bool
     */
    public bool $ready;

    /**
     * Database error message.
     *
     * @var string|null
     */
    public ?string $error;

    /**
     * Constructor.
     *
     * @since 0.0.1
     */
    public function __construct() {
        # Code Here
        $this->ready = DbCheck::isReady();
        $this->error = PluginCache::get(DbCheck::CACHE_DB_ERROR);
    }
}
