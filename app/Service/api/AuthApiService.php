<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/1/20
 * Time: 11:08
 */

namespace App\Service\api;


use App\Service\base\AuthService;
use App\Service\mobile\Service;
use App\Util\Rule;

class AuthApiService extends Service{

    public function get_user_is_auth($user_id){
        $authService = new AuthService();
        $auth = $authService->get_auth_by_user_id($user_id);
        if($auth){
            return array('status'=>true,'message'=>array('用户已实名','type'=>$auth->SubProductType));
        }else{
            return array('status'=>false,'message'=>array('用户未实名','type'=>''));
        }
    }

    /**
     * 用户实名认证
     * @param $real_name
     * @param $cert_id
     * @return \App\Model\Base\UserModel|array
     */
    public function auth_user_message($real_name,$cert_id,$user){
        $authService = new AuthService();
        $result = Rule::auth_filter(array('real_name'=>$real_name,'id_card'=>strtoupper($cert_id)));
        if($result['status']){
            $info = $authService->add_auth_user_message($real_name,$cert_id,$user);
            if($info['status']){
                return array('status'=>true,'message'=>array('data'=>$info['message']['data'],'auth'=>$info['message']['auth']));
            }else{
                return array('status'=>false,'message'=>array('data'=>$info['message']['data']));
            }
        }else{
            return array('status'=>false,'message'=>array('data'=>$result['data']['message']));
        }
    }

    public function check_user_auth($cert_id,$real_name){
        $authService = new AuthService();
        $info = $authService->get_auth_by_id_card($cert_id,$real_name);
        if($info){
           return array('status'=>true,'message'=>array('data'=>$info));
        }else{
            return array('status'=>false,'message'=>array('data'=>'该用户不存在'));
        }
    }

    public function check_auth_user_message_user_id($user_id){
        $authService = new AuthService();
        $info = $authService->get_auth_by_user_id($user_id);
        if($info){
            return array('status'=>true,'message'=>array('data'=>$info));
        }else{
            return array('status'=>false,'message'=>array('data'=>'该用户不存在'));
        }
    }

    /**
     * 更新用户的实名认证信息
     * @param $real_name
     * @param $cert_id
     * @return array
     */
    public function update_user_message($real_name,$cert_id){
        $authService = new AuthService();
        $info = $authService->update_auth_user_message($real_name,$cert_id);
        return $info;
    }
}