<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Request;
use App\Models\BackendLog;
use Illuminate\Support\Facades\DB;

/**
 * Description of BaseController
 *
 * @author lenovo
 */
class BaseController extends Controller {

    protected $cur_user = '';
    protected $default_page_size = 8;


    public function __construct() {
        $this->checkLogin();
        $this->privilegeCheck();
    }

    //权限控制
    protected function privilegeCheck()
    {
        //dd(123);
        //获取当前请求的uri
        //$cur_uri = $request->getRequestUri();
        $cur_uri = $_SERVER['REQUEST_URI'];
        //var_dump($cur_uri);die;
        //dd($cur_uri);
        $cur_uri = substr($cur_uri,1);//去掉uri最前面的/斜杠
        //因为查询功能时，会带上分页条件，因此uri后会跟上查询字符串，因此需要去掉？号及后面所有的部分
        if (strpos($cur_uri,'?')){
            $cur_uri = substr($cur_uri,0,strpos($cur_uri,'?'));
        }
        //dd($cur_uri);
        //查询出所有权限
        $cur_user = json_decode(session('back_user'));
        if (isset($cur_user->id) &&($cur_user->username != 'root')){
            $cur_userid = $cur_user->id;//本地管理员
            //获取本地管理员的权限
            $privileges = DB::table('backend_role_user')
                ->leftjoin('backend_permission_role','backend_role_user.role_id','=', 'backend_permission_role.role_id')
                ->leftjoin('backend_permissions','backend_permission_role.permission_id','=','backend_permissions.id')
                ->select('backend_permissions.base_uri','backend_permissions.id')
                ->where('backend_role_user.user_id','=',$cur_userid)
                ->get();
        } elseif(isset($cur_user->USERID)){
            $cur_sync_userid = $cur_user->USERID;//安硕管理员
            //获取安硕用户的权限
            $privileges = DB::table('sync_user_info')
                ->leftjoin('sync_code_library','sync_user_info.JOB_TITLE','=','sync_code_library.ITEMNO')
                ->leftjoin('backend_roles','sync_code_library.ITEMNAME','=','backend_roles.position')
                ->leftjoin('backend_permission_role','backend_roles.id','=','backend_permission_role.role_id')
                ->leftjoin('backend_permissions','backend_permission_role.permission_id','=','backend_permissions.id')
                ->select('backend_permissions.base_uri','backend_permissions.id')
                ->where('sync_user_info.USERID','=',$cur_sync_userid)
                ->get();
        } else {
            //本地超级管理员
            $privileges = DB::table('backend_permissions')->select('base_uri','id')->get();
            //dd($privileges);
        }

        $data = [];
        foreach ($privileges as $key => $value){
            $data[$key] = $value->base_uri;
        }
        //dd($data);
        //权限判断
        if ($cur_uri == 'backend/main' || $cur_uri=='backend/main/change-password'){
            return true;
        } elseif(in_array($cur_uri,$data)) {
            return true;
        } else {
            dd('没有权限');
        }
        //dd($data);
        //权限判断
        /*if ($cur_uri == 'backend/main'){
            return true;
        } else {
            if (in_array($cur_uri,$data) && ($cur_uri != 'backend/main')){
                //dd(111);
                return true;
            } else {
                //dd(22);
                //目前只做到四级，需要判断等级是3和4的任意，或者另外一种方式，对二级以下的权限全部放开
                //必须再判断一次，否则页面里面的请求将也会没拒绝，没有权限;
                //方法是：获取当前的uri，然后到数据库里面查获其父级uri，如果当前父级uri在数组$data中，就通过，否则不通过
                if ($this->lookThree($cur_uri,$data)){
                    return true;
                } else {

                    if ($this->lookFour($cur_uri,$data)){
                        return true;
                    } else {
                        dd('对不起，您没有权限操作');
                    }
                }

            }
        }*/

        //只做页面级别的控制，需要递归遍历所有
    }

    //如果是三级权限的父级权限
    protected  function lookThree($res,$data){
        //找到base_uri为$res的权限，根据此条权限的pid找到其父级权限，然后判断其父亲节权限是否在$data中
        //dd($res);
        $result = DB::table('backend_permissions')->where('base_uri','=',$res)->select('pid')->first();//
        //查找$result这条权限对应的父级权限
        //dd($result);
        //此判断可以给左侧导航页面里面的权限进行控制， 姑且叫三级权限，如果没有添加这项权限$result的结果为null,则进入判断
        if (is_null($result)){
            dd('您没权限操作');dd(1);
        }
        $result_parent = DB::table('backend_permissions')->where('id','=',$result->pid)->select('base_uri')->first();
        //dd($result_parent);
        //判断$result_parent是否在$res当中
        if (in_array($result_parent->base_uri,$data)){
            return true;
        } else {
            return false;
        }
    }

    //如果是四级权限的父级权限的父级权限
    protected function lookFour($res,$data){
        //获取本级权限的pid,然后根据pid找到三级权限
        $result4 = DB::table('backend_permissions')->where('base_uri','=',$res)->select('pid')->first();
        //根据四级权限找出三级权限
        if (is_null($result4)){
            //dd('您没权限操作');
        }
        //dd($result4);
        $result3 = DB::table('backend_permissions')->where('id','=',$result4->pid)->select('pid')->first();
        //根据三级权限找出二级权限
        if (is_null($result3)){
            //dd('您没权限操作');
        }
        //dd($result3);
        $result2 = DB::table('backend_permissions')->where('id','=',$result3->pid)->select('base_uri')->first();
        //dd($result2);
        if (in_array($result2->base_uri,$data)){
            return true;
        } else {
            //dd(1111111);
            return false;
        }
    }

    //防止翻墙操作，只有登录了用户才能进入后台
    protected function checkLogin() {
        $backUser = json_decode(session('back_user'), true);
        if (empty($backUser)) {
            header('Location:/backend/login');
            exit;
        }
        $this->cur_user = $backUser;
    }

    /**
     * 插入后台操作日志
     * @param $model
     * @param $remark
     */
    protected function addLog($model, $remark){
        $data['user_id'] = $this->cur_user['id'];
        $data['user_name'] = $this->cur_user['username'];
        $data['ip'] = $this->cur_user['last_login_ip'];
        $data['model'] = $model;
        $data['remark'] = $remark;
        $data['add_time'] = time();

        BackendLog::log($data);
    }

}
