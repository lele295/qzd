<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/5/25
 * Time: 16:41
 */

namespace App\Service\base;


use App\Log\Facades\Logger;
use App\Model\Base\LoanAfterModel;
use App\Service\mobile\Service;
use App\Service\mobile\WeChatService;
use App\Util\LocationUtil;
use App\Util\UserAgent;

class LoanAfterService extends Service
{
    public function update_loan_after_by_loan_id($loan_id,$array){
        $loanAfterModel = new LoanAfterModel();
        $loan_after = $loanAfterModel->update_loan_after_by_loan_id($loan_id,$array);
        return $loan_after;
    }

    public function get_user_we_chat_message($openid){
        $weChatService = new WeChatService();
        $we_info = $weChatService->get_user_information($openid);
        if($we_info){
            if(!isset($we_info->nickname)){
                return false;
            }
            $array['nickname'] = $we_info->nickname;
            $array['sex'] = $we_info->sex;
            $array['city'] = $we_info->city;
            $array['country'] = $we_info->country;
            $array['province'] = $we_info->province;
            $array['language'] = $we_info->language;
            $array['subscribe_time'] = date('Y-m-d H:i:s',$we_info->subscribe_time);
            return $array;
         }else{
            return false;
        }
    }

    public function init_loan_after($user_id,$loan_id){
        $loanBeforeService = new LoanBeforeService();
        $loan_before = $loanBeforeService->get_loan_before_by_loan_id($loan_id);
        if($loan_before){
            $userService = new UserService();
            $user = $userService->get_user_message_by_user_id($user_id);
            $array = array();
            if($user && $user->openid){
                $array = $this->get_user_we_chat_message($user->openid);
            }
                $ip = LocationUtil::get_network_ip();
                $network_opr = LocationUtil::get_network_opr($ip);
                $lbs = LocationUtil::get_ip_address($ip);
                $os = LocationUtil::get_os();
                $browser = LocationUtil::get_browser();
                $ua = new UserAgent();
                $mobile_model = $ua->deviceMode();

                $array = array_add($array,'ip',$ip);
                $array = array_add($array,'network_opr',$network_opr);
                $array = array_add($array,'lbs',$lbs);
                $array = array_add($array,'os',$os);
                $array = array_add($array,'explorer',$browser);
                $array = array_add($array,'model',$mobile_model);
                $loan_after = $this->update_loan_after_by_loan_id($loan_id,$array);
                return $loan_after;
        }else{
            return false;
        }
    }
}