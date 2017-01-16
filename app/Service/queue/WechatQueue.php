<?php
namespace App\Http\Service\queue;


use App\Http\Controllers\mobile\WeChatController;
use App\Model\Base\UserModel;

class WechatQueue {

    public function get_wechat_message($access_token,$openid){
        $wechatservice = new WeChatController();
        $wxinfo = $wechatservice->get_wx_userinfo($access_token,$openid);
        if($wxinfo){
            $userModel = new UserModel();
            $array = array(
                'nickname'=>$wxinfo->nickname,
                'headimgurl'=>$wxinfo->headimgurl,
            );
            $userModel->update_user_info_by_openid($openid,$array);
        }
    }

}