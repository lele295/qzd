<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/10/16
 * Time: 13:57
 */

namespace App\Service\admin;


use App\Model\Admin\PermissionModel;
use App\Model\Admin\PermissionRoleModel;
use App\Model\Admin\RoleModel;
use App\Model\Admin\UserAuthModel;
use App\Util\AdminAuth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermissionService extends Service
{
    private $roleModel;
    private $userAuthModel;
    private $admin_id;
    private $name ;
    public function __construct(){
        $this->roleModel = new RoleModel;
        $this->userAuthModel = new UserAuthModel();
    }

    public function add_role_info($info){
        $array['name'] = $info['role'];
        $array['display_name'] = $info['descript'];
        $array['created_at'] = date('Y-m-d H:i:s',time());
        $array['updated_at'] = date('Y-m-d H:i:s',time());
        $array['status'] = '1';
        $data = $this->roleModel->insert_new_role($array);
        return $data;
    }

    public function get_role_list($limit = 15){
        $info = $this->roleModel->get_role_list($limit);
        return $info;
    }

    public function update_role_message($role_id,$array){
        $info = $this->roleModel->update_role_list($role_id,$array);
        return $info;
    }

    public function update_role_message_by_role_id($role_id,$array){
        $info = $this->roleModel->update_role_list_by_role_id_list($role_id,$array);
        return $info;
    }

    public function get_role_by_admin_id($admin_id){
        $userAuthModel = new UserAuthModel();
        $info = $userAuthModel->get_role_by_admin_id($admin_id);
        return $info;
    }

    public function get_loan_by_admin_id_array($admin_id)
    {
        $info = $this->get_role_by_admin_id($admin_id);
        $array = array();
        foreach($info as $val){
            array_push($array,$val->role_id);
        }
        return $array;
    }

    public function add_role_to_admin_id($role,$admin_id){
        $this->start_conn();
        $delete = $this->userAuthModel->delete_role_by_admin_id($admin_id);
        $info = array();
        foreach($role as $val){
            $array['admin_id'] = $admin_id;
            $array['role_id'] = $val;
            $array['created_at'] = date('Y-m-d H:i:s',time());
            $array['updated_at'] = date('Y-m-d H:i:s',time());
            array_push($info,$array);
        }
        $info = $this->userAuthModel->insert_role($info);
        $info = $this->end_conn(array($delete,$info));
        return $info;
    }

    //获取所有权限
    public function get_permission_list()
    {
        $permissionModel = new PermissionModel();
        $info = $permissionModel->get_permission_list();
        $array = array();
        $model = array();
        $menu = array();
        $action = array();
        $data = array();
        $action_array = array();
        if($info){
            foreach($info as $val){
               if(empty($val->param_id)){
                   $model = array_add($model,$val->id,$val);
                   $data[$val->id] = array();
               }
               if(isset($model[$val->param_id])){
                   $menu = array_add($menu,$val->id,$val);
                   array_push($data[$val->param_id],$val);
                   $action_array[$val->id] = array();
               }
               if(isset($menu[$val->param_id])){
                   $action = array_add($action,$val->id,$val);
                   array_push($action_array[$val->param_id],$val);
               }
            }
            $array['model'] = $model;
            $array['menu'] = $data;
            $array['action'] = $action_array;
            return $array;
        }
    }

    //为用户组添加权限
    public function add_permission_to_role($permission,$role_id){
        $permissionRoleModel = new PermissionRoleModel();
        $array['permission'] = serialize($permission);
        $array['role_id'] = $role_id;
        $array['created_at'] = date('Y-m-d H:i:s',time());
        $array['updated_at'] = date('Y-m-d H:i:s',time());
        $info = $permissionRoleModel->insert_permission($array);
        return $info;
    }


    //从XML配置文件中更新后台权限列表
    public function update_permission_from_xml(){
        $info = view('admin.power.index');
        $xml = simplexml_load_string($info);
        $permissionModel = new PermissionModel();
        $permissionModel->delete_permission();
        foreach($xml->power->model as $model){
            $permission_id = $this->add_model_permission($model);
            $this->add_menu_permission($model,$permission_id);
        }
    }

    public function add_menu_permission($model,$param_id){
        foreach($model->menu as $menu){
            $info = $this->add_menu_to_permission($menu,$param_id);
            $this->add_action_permission($menu,$info);
        }
    }

    public function add_action_permission($menu,$param_id){
        foreach($menu->childer as $childer){
            $this->add_action_to_permission($childer,$param_id);
        }
    }

    public function add_action_to_permission($childer,$param_id){
        $permissionModel = new PermissionModel();
        $array['id'] = $childer->id;
        $array['name'] = $childer->name;
        $array['display_name'] = $childer->descript;
        $array['node'] = '3';
        $array['created_at'] = date('Y-m-d H:i:s',time());
        $array['updated_at'] = date('Y-m-d H:i:s',time());
        $array['route'] = $childer->url;
        $array['status'] = '1';
        $array['param_id'] = $param_id;
        $info = $permissionModel->insert_permission($array);
        return $info;
    }

    public function add_menu_to_permission($menu,$param_id){
        $permissionModel = new PermissionModel();
        $array['id'] = $menu->param->id;
        $array['name'] = $menu->param->name;
        $array['display_name'] = $menu->param->descript;
        $array['node'] = '2';
        $array['created_at'] = date('Y-m-d H:i:s',time());
        $array['updated_at'] = date('Y-m-d H:i:s',time());
        $array['route'] = $menu->param->url;
        $array['status'] = '1';
        $array['param_id'] = $param_id;
        $info = $permissionModel->insert_permission($array);
        return $info;
    }

    public function add_model_permission($model){
        $permissionModel = new PermissionModel();
        $array['id'] = $model->id;
        $array['name'] = $model->key;
        $array['display_name'] = $model->descript;
        $array['node'] = '1';
        $array['created_at'] = date('Y-m-d H:i:s',time());
        $array['updated_at'] = date('Y-m-d H:i:s',time());
        $array['route'] = $model->key;
        $array['status'] = '1';
        $info = $permissionModel->insert_permission($array);
        Log::info($info);
        return $info;
    }

    //获取管理员所拥有的权限
     public function get_admin_permission($admin_id){
        $userAuthModel = new UserAuthModel();
        $permissionRoleModel = new PermissionRoleModel();
        $permissionModel = new PermissionModel();
        $role_id = $userAuthModel->get_admin_role($admin_id);
        $permission_array = $permissionRoleModel->get_permission_list_by_role_id($role_id);
        $permission = $permissionModel->get_permission_by_id($permission_array,$admin_id);
        return $permission;
    }

    //获取管理组所拥有的权限
    public function get_role_permission($role_id)
    {
        $permissionRoleModel = new PermissionRoleModel();
        $permission_array = $permissionRoleModel->get_permission_list_by_role_id(array($role_id));
        return $permission_array;
    }

     public function get_admin_is_auth($name = ''){
         /*
        $permission = Cache::remember(AdminAuth::id().'-adminself-permission','60',function(){
            $permission = $this->get_admin_permission(AdminAuth::id());
            return $permission;
        });
         */
         $permission = $this->get_admin_permission(AdminAuth::id());
         return $permission;

        if(empty($name)){
            return true;
        }
        if(in_array($name,$permission)){
            return $permission[$name];
        }else{
            return false;
        }
    }

    public function get_all_permission_list($paginate){
        $permissionModel = new PermissionModel();
        $info = $permissionModel->get_permission_list_paginate($paginate);
        return $info;
    }

    public function get_all_permission(){
        $permissionModel = new PermissionModel();
        $info = $permissionModel->get_permission_list();
        return $info;
    }

    public function add_permission($array){
        $permissionModel = new PermissionModel();
        $info = $permissionModel->insert_permission($array);
        return $info;
    }

    public function update_permission($id,$array){
        $permissionModel = new PermissionModel();
        $array = array_add($array,'created_at',date('Y-m-d H:i:s'),time());
        $array = array_add($array,'updated_at',date('Y-m-d H:i:s'),time());
        $info = $permissionModel->update_permission_by_id($id,$array);
        return $info;
    }

    public function get_permission_by_id($id){
        $permissionModel = new PermissionModel();
        $info = $permissionModel->get_permission_by_id_self($id);
        return $info;
    }

    //获取一个管理组下面的用户
    public function get_role_admin($role_id){
        $userAuthModel = new UserAuthModel();
        $info = $userAuthModel->get_admin_by_role_id($role_id);
        return $info;
    }

    public function delete_role_admin($admin_id,$role_id){
        $userAuthModel = new UserAuthModel();
        $info = $userAuthModel->delete_admin_role($admin_id,$role_id);
        return $info;
    }

    static public function ge_user_rle_by_admin_id($admin_id){
        $userAuthModel = new UserAuthModel();
        $info = $userAuthModel->get_role_detail_by_admin_id($admin_id);
        return $info;
    }





}