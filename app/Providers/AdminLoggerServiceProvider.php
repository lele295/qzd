<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/11/4
 * Time: 14:48
 */

namespace App\Providers;


use App\Commands\SendEmail;
use App\Log\src\AdminLog;
use App\Log\src\AdminLoggerRecord;
use Illuminate\Support\ServiceProvider;

class AdminLoggerServiceProvider extends ServiceProvider
{

    public function boot(){

    }

    public function register(){
        $this->app->bind('adminLogger',function(){
            return new AdminLog();
        });

        $this->app->bind('adminLoggerRecord',function(){
            return new AdminLoggerRecord();
        });

    }
}