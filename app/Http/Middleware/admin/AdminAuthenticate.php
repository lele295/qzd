<?php
namespace App\Http\Middleware\admin;
use App\Service\admin\AdminService;
use App\Util\AdminAuth;
use Closure;

/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/9/23
 * Time: 9:46
 */
class AdminAuthenticate
{
    public function handle($request, Closure $next){
        if(!AdminAuth::check()){
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('bqjieqianadmin/admin/bkend');
            }
        }else{
            $admin_s = new AdminService();
            $admin_s->check_power_menu();
        }
        return $next($request);
    }
}