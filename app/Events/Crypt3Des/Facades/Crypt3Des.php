<?php
namespace App\Crypt3Des\Facades;

use Illuminate\Support\Facades\Facade;

class Crypt3Des extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'crypt3des';
    }
}