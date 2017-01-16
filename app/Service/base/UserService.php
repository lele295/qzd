<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/4/6
 * Time: 10:12
 */

namespace App\Service\base;


use App\Model\Base\UserModel;
use App\Service\mobile\Service;

class UserService extends Service
{
    public function update_user_message_by_mobile($mobile,$data){
        $userModel = new UserModel();
        $info = $userModel->update_user_info_by_mobile($mobile,$data);
        return $info;
    }

    public function get_user_message_by_user_id($user_id){
        $userModel = new UserModel();
        $info = $userModel->get_user_message_by_id($user_id);
        return $info;
    }

    public function get_user_message_by_open_id($open_id){
        $userModel = new UserModel();
        $info = $userModel->sel_openid_user($open_id);
        return $info;
    }

    public function get_user_message_by_mobile($mobile){
        $userModel = new UserModel();
        $info = $userModel->get_user_message_by_mobile($mobile);
        return $info;
    }

    public function insert_user_message($array){
        $userModel = new UserModel();
        $user_id = $userModel->insert_user_message($array);
        return $user_id;
    }

    public function user_message_by_user_id($user_id,$update){
        $userModel = new UserModel();
        $info = $userModel->update_user_info_by_id($user_id,$update);
        return $info;
    }
}