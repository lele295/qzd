<?php
namespace App\Facades;
use Illuminate\Support\Facades\Facade;

/**
 * Class AppRequestFacede
 * @package App\Facades
 */
class AppRequest extends Facade{
    protected static function getFacadeAccessor() { return 'AppRequest'; }
}