<?php

/**
 * Trait to hide private properties from debug output.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       1.0.0
 */

namespace DRP\DeviceImporter;

use ReflectionProperty;

trait TraitHidePrivates {
    public function __debugInfo() {
        $properties = get_object_vars($this);
        foreach ($properties as $key => $value) {
            if ((new ReflectionProperty($this, $key))->isPrivate()) {
                unset($properties[$key]);
            }
        }
        return $properties;
    }
}
