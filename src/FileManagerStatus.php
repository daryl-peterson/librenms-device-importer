<?php

/**
 * File manager status class
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       1.0.0
 */

namespace DRP\DeviceImporter;

/**
 * File manager status class
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       1.0.0
 */
class FileManagerStatus {
    use TraitHidePrivates;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * File name
     *
     * @var string
     */
    public string $fileName;

    /**
     * File status
     *
     * @var string
     */
    public string $status;

    /**
     * ImportSettings object
     *
     * @var ImportSettings
     */
    private ImportSettings $settings;

    /**
     * Initialize class object
     *
     * @param string $fileName
     */
    public function __construct(string $fileName) {

        $this->fileName = $fileName;
        $this->settings = new ImportSettings();

        $files = $this->settings->get('upload_files', array());

        if (isset($files[$this->fileName])) {
            $this->status = $files[$this->fileName]->status ?? self::STATUS_PENDING;
        } else {
            $this->status = self::STATUS_PENDING;
            $files[$this->fileName] = $this;
            $this->settings->set('upload_files', $files);
        }
    }

    /**
     * Set the status of the file
     *
     * @param string $status
     * @return
     * @since 1.0.0
     */
    public function setStatus(string $status): void {
        $this->status = $status;
        $files = $this->settings->get('upload_files', array());

        if (!isset($files[$this->fileName])) {
            $files[$this->fileName] = $this;
        } else {
            $files[$this->fileName]->status = $this->status;
        }

        $this->settings->set('upload_files', $files);
    }
}
