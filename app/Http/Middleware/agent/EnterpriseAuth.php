<?php
namespace App\Http\Middleware\agent;

use App\Service\ag\Enterprise;
use Closure;
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/9/23
 * Time: 9:46
 */
class EnterpriseAuth
{
    public function handle($request, Closure $next){
        if(!Enterprise::check()){
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('agent/enterpise/login');
            }
        }else{
            return $next($request);
        }
    }
}