<?php
namespace App\Providers;
use App\Service\ag\Enterprise;
use Illuminate\Support\ServiceProvider;

class EnterpriseServiceProvider extends ServiceProvider{
    public function register()
    {
        $this->app->singleton('Enterprise',function(){
            return new Enterprise();
        });
    }
}