<?php
namespace RentalManager\Photos\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Created by PhpStorm.
 * Date: 7/4/18
 * Time: 12:23 PM
 * Phtoos.php
 * @author Goran Krgovic <goran@dashlocal.com>
 */

class Photos extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'photos';
    }
}