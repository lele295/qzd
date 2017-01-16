<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/9/29
 * Time: 14:58
 */

namespace App\Service\base;


use App\Model\Base\UniqueCodeModel;
use App\Model\Base\UserBankCardModel;
use App\Service\mobile\Service;

class MessageService extends Service
{
    //发送信息服务
    public function send_message_to_user($bank_id,$msg){
        $userBankModel = new UserBankCardModel();
        $unique = new UniqueCodeModel();
        $user = $userBankModel->get_user_message_by_bank_id($bank_id);
        $unique->select_send_supply($msg,$user->mobile);
    }
}