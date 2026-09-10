<?php

/**
 * LibreNMS Device Importer Trait to validate admin users.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */

namespace DRP\DeviceImporter;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait TraitValidateAdmin {
    /**
     * Check if the current user is an admin and abort if not.
     *
     * @return void
     *
     * @since 0.0.1
     * @throws HttpException
     * @throws NotFoundHttpException
     * @throws HttpResponseException
     *
     */
    public function validateAdmin(): void {
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
    }
}
