<?php
namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class Enterprise extends Facade{
    protected static function getFacadeAccessor() { return 'Enterprise'; }
}