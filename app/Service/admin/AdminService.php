<?php

namespace App\Service\admin;

use App\Log\Facades\Logger;
use App\Model\Admin\AdminMessageModel;
use App\Model\Admin\AdminModel;
use App\Util\AdminAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class AdminService extends Service
{
    public function __construct(){

    }

    public function login($email,$password){
        $login  = AdminAuth::attempt(array('email'=>$email,'password'=>$password));
        if($login['status']){
            $permissionService = new PermissionService();
            $permission = $permissionService->get_admin_permission(AdminAuth::id());
            Session::put('permission',$permission);
        }
        return $login;
    }

    public function logOut(){
        return AdminAuth::logOut();
    }

    public function get_admin_list(){
        $adminModel = new AdminModel();
        $info = $adminModel->get_admin_detail_message_list();
        return $info;
    }

    public function add_new_admin($info){
        $adminModel = new AdminModel();
        $adminMessageModel = new AdminMessageModel();
        $admin['username'] = $info['real_name'];
        $admin['password'] = Hash::make('welcome!bqjr88');
        $admin['is_valid'] = '100';
        $admin['created_at'] = date('Y-m-d H:i:s',time());
        $admin['updated_at'] = date('Y-m-d H:i:s',time());
        $admin['real_name'] = $info['real_name'];
        $admin['email'] = $info['email'];
        $admin_id = $adminModel->insert_or_update_admin($admin);
        $admin_message['admin_id'] = $admin_id;
        $admin_message['mobile'] = $info['mobile'];
        $admin_message['work_no'] = $info['work_no'];
        $admin_message['id_card'] = $info['id_card'];
        $admin_message['created_at'] = date('Y-m-d H:i:s',time());
        $admin_message['updated_at'] = date('Y-m-d H:i:s',time());
        $flag = $adminMessageModel->insert_admin_message($admin_message);
        return $flag;
    }


    public function get_auth_data($model,$menu){
        $array = array();
        foreach($model as $val){
            array_push($array,$this->get_all_menu($val,$menu));
        }
        return $array;
    }

    public function get_all_menu($model,$menu){
        $array = array();
        $data = array();
        foreach($menu as $val){
            if(starts_with($val,$model)){
                array_push($array,$val);
            }
        }
        $data = array_add($data,$model,$array);
        return $data;
    }

    public function set_admin_password($admin_id,$action_id,$password){
        $adminModel = new AdminModel();
        $info = $adminModel->update_admin_info(array('password'=>Hash::make($password)),$admin_id);
        return $info;
    }

    public function get_admin_message_by_admin_id($admin_id){
        $adminModel = new AdminModel();
        $info = $adminModel->get_admin_name_by_admin_id($admin_id);
        return $info;
    }

    public function update_admin_status($status,$admin_id){
        $adminModel = new AdminModel();
        $info = $adminModel->update_admin_info(array('is_valid'=>$status),$admin_id);
        return $info;
    }

    /*
     * 输出下载文件
     */
    public function down_file($filename)
    {
        $fileinfo = pathinfo($filename);
        header('Content-type: application/x-'.$fileinfo['extension']);
        header('Content-Disposition: attachment; filename='.$fileinfo['basename']);
        header('Content-Length: '.filesize($filename));
        return readfile($filename);
    }

    /*
     * 获取管理员菜单和权限判断
     */
    public function check_power_menu(){
        $permission = Session::get("permission");

        $url_suffix = $_SERVER['REQUEST_URI'];

        $parse_url = preg_split("/\/|\?/", $url_suffix);

        $this_url = "/".$parse_url[1]."/".$parse_url[2]."/".$parse_url[3];
        if(!isset($parse_url[3]) || $this_url=="/bqjieqianadmin/admin/index" || $this_url=="/bqjieqianadmin/admin/logout"){
            return true;
        }

        if(in_array($this_url, $permission["url"])){
            return true;
        }
        //die("您没有权限访问");
    }
}