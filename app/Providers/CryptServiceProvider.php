<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/4/1
 * Time: 11:07
 */

namespace App\Providers;


use App\Crypt3Des\src\Crypt3Des;
use Illuminate\Support\ServiceProvider;

class CryptServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('crypt3des',function(){
            return new Crypt3Des();
        });
    }
}