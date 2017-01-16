<?php
namespace App\Log\Facades;
use Illuminate\Support\Facades\Facade;

class Logger extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        //返回logger类
        return 'logger';
    }
}