<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/9/28
 * Time: 14:17
 */

namespace App\Http\Middleware;


use App\User;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Support\Facades\Session;

class AuthenticateMobile
{
    public function handle($request, Closure $next){
        if(!Auth::check()){
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('/m/index/home');
            }
        }
  //      User::where('id',Auth::id())->update(array('session_id'=>Session::getId()));
        return $next($request);
    }
}