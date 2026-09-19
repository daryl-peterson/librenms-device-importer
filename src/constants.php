<?php

/**
 * LibreNMS Device Importer Constants
 *
 * @package     device-importer
 * @author      Daryl Peterson <daryl.peterson@gmail.com>
 * @license     https://opensource.org MIT License
 * @link        https://github.com
 * @since       0.0.1
 */

namespace DRP\DeviceImporter;

const PLUGIN_NAME           = 'device-importer';
const PLUGIN_TITLE          = 'Device Importer';
const PLUGIN_AUTHOR         = 'Daryl Peterson';
const PLUGIN_VER            = 'v0.1.0-alpha.08';

/**
 * Database connection constants.
 */
const PLUGIN_DB_HOST        = '127.0.0.1';
const PLUGIN_DB_PORT        = '3306';
const PLUGIN_DB_CONNECTION  = 'plugin_db';
const PLUGIN_DB_DATABASE    = 'librenms_plugin_db';
const PLUGIN_DB_USERNAME    = 'plugin_user';
const PLUGIN_DB_PASSWORD    = 'plugin_password';

/**
 * Database cache keys for connection checks.
 */
const CACHE_DB_PASS = 'db_pass';

/**
 * Database cache key for migration checks.
 */
const CACHE_DB_MIGRATION_CHECK = 'db_migration_check';

/**
 * @var string The cache key for database errors.
 */
const CACHE_DB_ERROR = 'db_error';
