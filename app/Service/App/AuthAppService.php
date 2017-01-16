<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/4/6
 * Time: 10:26
 */

namespace App\Service\App;


use App\Api\api\AnApi;
use App\Facades\AppRequest;
use App\Log\Facades\Logger;
use App\Service\api\AuthApiService;
use App\Service\base\AuthService;
use App\Service\base\UserService;
use App\Service\mobile\Service;
use App\Util\AppRule;

class AuthAppService extends Service
{
    /**
     * 身份认证
     * @param $info
     * @return array
     * @throws \Exception
     * status = 100成功    200 失败
     */
    public function auth($info){
        $rule = AppRule::auth_filter($info);
        if($rule['status']){
            $user_message = array('user_id'=>$info['user_id']);
            $anApi = new AnApi();
            $auth_api = $anApi->get_customer_message($info['real_name'],$info['id_card']);
            if($auth_api){
                $authApiService = new AuthApiService();
                $auth_check = $authApiService->check_auth_user_message_user_id($info['user_id']);
                if(!$auth_check['status']){
                    $info = $authApiService->auth_user_message($info['real_name'],$info['id_card'],$user_message);
                    if($info['status']){
                        return array('status'=>100,'data'=>array('message'=>'实名认证'));
                    }else{
                        return array('status'=>200,'data'=>array('message'=>$info['message']['data']));
                    }
                }else{
                    $res_info = $authApiService->update_user_message($info['real_name'],$info['id_card']);
                    if($res_info['status']){
                        return array('status'=>100,'data'=>array('message'=>'实名认证'));
                    }else{
                        return array('status'=>200,'data'=>array('message'=>$info['message']['data']));
                    }
                }
            }else{
                return array('status'=>200,'data'=>array('message'=>'暂不符合资格'));
            }
        }else{
            return array('status'=>200,'data'=>array('message'=>$rule['data']['message']));
        }
    }

    public function get_auth_message($user_id){
        $authService = new AuthService();
        $auth = $authService->get_auth_by_user_id($user_id);
        if($auth){
            return array('status'=>100,'data'=>array('message'=>'获取实名信息成功','auth'=>(array)$auth));
        }else{
            return array('status'=>200,'data'=>array('message'=>'没有该用户的实名信息','auth'=>''));
        }
    }


    public function get_user_message($user_id){

    }
}