<?php

namespace DRP\DeviceImporter\Contracts;

interface DbHelperInterface {

    /**
     * Get the database connection for the plugin.
     *
     * @return string
     */
    public function getConnection(): string;


    /**
     * Get the name of the database.
     *
     * @return string
     */
    public function getDbName(): string;

    /**
     * Get the last error message, if any.
     *
     * @return string|null
     */
    public function getError(): ?string;

    /**
     * Boot the database connection or perform any necessary initialization.
     *
     * @return void
     */
    public function boot(): void;
}
