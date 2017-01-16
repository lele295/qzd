<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/9/23
 * Time: 15:16
 */

namespace App\Api\api;

//银行卡认证接口
use App\Log\src\Logger;
use Illuminate\Support\Facades\Log;

class BankAuthApi implements BaseApi
{
    private $bankapi;

    public function __construct(){
        $this->bankapi = new Bankapi();
    }

    //进行银行认证
    public function check_bank_user($array){
        $info = $this->bankapi->sent_message_to_web($array);
        if($info){
            return $info;
        }else{
            Logger::info('银行卡认证出现异常');
            return false;
        }
    }

    public function update_bank_check($out_id){
        $info = $this->bankapi->query_message_to_web($out_id);
        return $info;
    }

    //进行银行卡帐户认证
    public function check_bank_no_auth($array){

    }

}