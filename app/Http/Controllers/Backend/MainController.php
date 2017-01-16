<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\BaseController;
use App\Model\Backend\BackendUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

/**
 * Description of MainController
 *
 * @author lenovo
 */
class MainController extends BaseController {

    public function getIndex() {
        //$this->assign['username'] = $this->cur_user['username'];
        $data = $this->privilege();
        //dd($data);
        //return view('backend.main', $this->assign)->with('data',$data);//;
        return view('backend.main')->with('data',$data);//;
    }

    public function getDashboard() {

        $this->assign['login_count'] = $this->cur_user['login_count'];
        $this->assign['last_login_ip'] = $this->cur_user['last_login_ip'];
        $this->assign['last_login_time'] = date('Y-m-d H:i:s', $this->cur_user['last_login_time']);
        return view('backend.dashboard', $this->assign);
    }
    
    /**
     * 修改管理员密码
     */
    public function postChangePassword(Request $request) {
        $user = BackendUser::where('id', '=', $this->cur_user['id'])->first();
        if (!(Hash::check($request->oldP, $user['password']))) {
            return response()->json(['msg' => '原密码不正确', 'state' => 'error']);
        } elseif ($request->newP !== $request->conP) {
            return response()->json(['msg' => '确认密码与新密码不一致', 'state' => 'error']);
        } elseif ($request->oldP === $request->newP) {
            return response()->json(['msg' => '新密码不能与原密码相同', 'state' => 'error']);
        } else {
            $user->password = bcrypt($request->newP);
            if ($user->save()) {
                return response()->json(['msg' => '密码修改成功', 'state' => 'success']);
            } else {
                return response()->json(['msg' => '修改失败', 'state' => 'error']);
            }
        }
    }

    //查询登陆用户的所有权限
    protected function privilege()
    {
        $cur_user = json_decode(session('back_user'));//返回->登录用户信息
        if (isset($cur_user->id)){
            $cur_userid = $cur_user->id;
        } else {
            $cur_sync_userid = $cur_user->USERID;
        }
        //根据用户id获取角色id
        //根据存入session里面的用户名来区别登录用户是安硕用户还是本地用户，安硕用户用sync_user_info里面的USERID登录的
        if (isset($cur_userid) && ($cur_user->username != 'root')){
            //获取本地管理员的权限
            $privileges = DB::table('backend_role_user')
                ->leftjoin('backend_permission_role','backend_role_user.role_id','=', 'backend_permission_role.role_id')
                ->leftjoin('backend_permissions','backend_permission_role.permission_id','=','backend_permissions.id')
                ->select('backend_permissions.*')
                ->where('backend_role_user.user_id','=',$cur_userid)
                ->where('backend_permissions.pid','=',0)
                ->get();
        } elseif (isset($cur_sync_userid)) {
            //获取安硕用户的权限
            $privileges = DB::table('sync_user_info')
                ->leftjoin('sync_code_library','sync_user_info.JOB_TITLE','=','sync_code_library.ITEMNO')
                ->leftjoin('backend_roles','sync_code_library.ITEMNAME','=','backend_roles.position')
                ->leftjoin('backend_permission_role','backend_roles.id','=','backend_permission_role.role_id')
                ->leftjoin('backend_permissions','backend_permission_role.permission_id','=','backend_permissions.id')
                ->select('backend_permissions.*')
                ->where('sync_user_info.USERID','=',$cur_sync_userid)
                ->where('backend_permissions.pid','=',0)
                ->get();
        } else {
            //超级管理员权限
            //$privileges = DB::table('backend_role_user')
            $privileges = DB::table('backend_permissions')
                //->leftjoin('backend_permission_role','backend_role_user.role_id','=', 'backend_permission_role.role_id')
                //->leftjoin('backend_permissions','backend_permission_role.permission_id','=','backend_permissions.id')
                ->select('backend_permissions.*')
                ->where('backend_permissions.pid','=',0)
                ->get();
            //dd($privileges);
        }

        //dd($privileges);
        //遍历所有的顶级权限，获取顶级权限的id，然后查出其子集权限
        if (isset($cur_userid) && $cur_user->username != 'root'){
            foreach ($privileges as $val)
            {
                $privileges_son = DB::table('backend_role_user')
                    ->leftjoin('backend_permission_role','backend_role_user.role_id','=', 'backend_permission_role.role_id')
                    ->leftjoin('backend_permissions','backend_permission_role.permission_id','=','backend_permissions.id')
                    ->select('backend_permissions.name','backend_permissions.id','backend_permissions.base_uri')
                    ->where('backend_role_user.user_id','=',$cur_userid)
                    ->where('backend_permissions.pid','=',$val->id)
                    ->get();
                $val->son= $privileges_son;
            }
        } elseif(isset($cur_sync_userid)) {
            foreach ($privileges as $val)
            {
                $privileges_son = DB::table('sync_user_info')
                    ->leftjoin('sync_code_library','sync_user_info.JOB_TITLE','=','sync_code_library.ITEMNO')
                    ->leftjoin('backend_roles','sync_code_library.ITEMNAME','=','backend_roles.position')
                    ->leftjoin('backend_permission_role','backend_roles.id','=','backend_permission_role.role_id')
                    ->leftjoin('backend_permissions','backend_permission_role.permission_id','=','backend_permissions.id')
                    ->select('backend_permissions.name','backend_permissions.id','backend_permissions.base_uri')
                    ->where('sync_user_info.USERID','=',$cur_sync_userid)
                    ->where('backend_permissions.pid','=',$val->id)
                    ->get();
                $val->son= $privileges_son;
            }
        } else {
            foreach ($privileges as $val)
            {
                //$privileges_son = DB::table('backend_role_user')
                $privileges_son = DB::table('backend_permissions')
                    //->leftjoin('backend_permission_role','backend_role_user.role_id','=', 'backend_permission_role.role_id')
                    //->leftjoin('backend_permissions','backend_permission_role.permission_id','=','backend_permissions.id')
                    ->select('backend_permissions.name','backend_permissions.id','backend_permissions.base_uri')
                    //->where('backend_permissions.pid','=',0)
                    ->where('backend_permissions.pid','=',$val->id)
                    ->get();
                //dd($privileges_son);
                $val->son= $privileges_son;
            }
        }
        //dd($privileges);
        return $privileges;
    }
}
