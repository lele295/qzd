<?php
namespace App\Util;

use App\Model\Admin\AdminModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AdminAuth
{
    public static function attempt(array $array){
        $admin  = new AdminModel();
        $info = $admin->get_admin_to_login($array['email']);
        if(!empty($info)){
            if(Hash::check($array['password'],$info->password)){
                Session::push('admin', $info);
                return array('status'=>true,'msg'=>'登录成功');
            }else{
                return array('status'=>false,'msg'=>'密码不正确，请重试');
            }
        }else{
            return array('status'=>false,'msg'=>'该用户不存在，或已被禁用，请联系管理员');
        }

    }

    public static function check()
    {
        if(Session::has('admin')){
            return true;
        }else{
            return false;
        }
    }

    public static function id()
    {
        return Session::get('admin')[0]->id;
    }

    public static function user(){
        return Session::get('admin')[0];
    }

    public static function logOut(){
        Session::flush();
    }
}