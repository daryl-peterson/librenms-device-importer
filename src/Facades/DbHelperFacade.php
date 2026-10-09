<?php

namespace DRP\DeviceImporter\Facades;
use Illuminate\Support\Facades\Facade;


class DbHelperFacade extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'dbhelper';
    }
}
