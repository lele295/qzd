<?php
namespace App\Providers;
use App\Service\ag\Enterprise;
use App\Util\AppRequest;
use Illuminate\Support\ServiceProvider;

/**
 * Class AppRequestServiceProvider
 * @package App\Providers
 */
class AppRequestServiceProvider extends ServiceProvider{
    public function register()
    {
        $this->app->singleton('AppRequest',function(){
            return new AppRequest();
        });

        $this->app->singleton('Enterprise',function(){
            return new Enterprise();
        });
    }

    public function boot()
    {
    }
}