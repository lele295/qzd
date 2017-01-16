<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2016/5/9
 * Time: 9:30
 */
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthenticatePc{

    public function handle($request, Closure $next){
        if(!Auth::check()){
            if($request->ajax()){
                return response('Unauthorized.', 401);
            }else{
                return redirect()->guest('/pc/users/login');
            }
        }
        return $next($request);
    }
}