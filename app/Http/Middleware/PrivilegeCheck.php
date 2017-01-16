<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PrivilegeCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    //权限判断全局中间件,对每个路由都进行判断
    public function handle($request, Closure $next)
    {
        //登录页都有权限，不用验证;
        if($request->path() == 'backend/login'){
            //return true;
        } else {
            //根据登录者的信息，查询出所有的信息，然后获取当前点击的uri,判断uri是否在数组当中，如果在则允许访问
            //如果不在在则不允许访问，弹出一个遮罩层，显示你无权访问
            $cur_uri = $request->path();
            //查询出所有权限
            $cur_user = json_decode(session('back_user'));
            if (isset($cur_user->id)){
                $cur_userid = $cur_user->id;//本地管理员
                //获取本地管理员的权限
                $privileges = DB::table('backend_role_user')
                    ->leftjoin('backend_permission_role','backend_role_user.role_id','=', 'backend_permission_role.role_id')
                    ->leftjoin('backend_permissions','backend_permission_role.permission_id','=','backend_permissions.id')
                    ->select('backend_permissions.base_uri')
                    ->where('backend_role_user.user_id','=',$cur_userid)
                    ->get();
            } else {
                $cur_sync_userid = $cur_user->USERID;//
                //获取安硕用户的权限
                $privileges = DB::table('sync_user_info')
                    ->leftjoin('sync_code_library','sync_user_info.JOB_TITLE','=','sync_code_library.ITEMNO')
                    ->leftjoin('backend_roles','sync_code_library.ITEMNAME','=','backend_roles.position')
                    ->leftjoin('backend_permission_role','backend_roles.id','=','backend_permission_role.role_id')
                    ->leftjoin('backend_permissions','backend_permission_role.permission_id','=','backend_permissions.id')
                    ->select('backend_permissions.base_uri')
                    ->where('sync_user_info.USERID','=',$cur_sync_userid)
                    ->get();
            }
            $data = [];
            foreach ($privileges as $key => $value){
                $data[$key] = $value->base_uri;
            }
            //dd($data);
            if (!in_array($cur_uri,$data)){
                //dd(123);
                return response('没有权限');
            }
        }
        return $next($request);
    }
}
