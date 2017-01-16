<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/11/9
 * Time: 16:40
 */

namespace App\Http\Middleware;

use Closure;

class UserLoggerRecord
{

    public function handle($request, Closure $next){
        \App\Log\Facades\UserLoggerRecord::record();
        return $next($request);
    }
}