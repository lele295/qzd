<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/10/8
 * Time: 16:49
 */

namespace App\Service\datamigrate;


use App\Model\Datamigrate\BankModel;
use App\Service\mobile\Service;
use Illuminate\Support\Facades\Log;

class BankService extends Service
{
    public function get_bank_message(){
        $bankModel = new BankModel();
        $info = $bankModel->get_bank_message();
        foreach($info as $val){
            $array['id'] = $val->id;
            $array['user_id'] = $val->user_id;
            $array['real_name'] = $val->real_name;
            $array['identification'] = $val->identification;
            $array['bank_name'] =  $val->bank_name;
            $array['number'] = $val->number;
            $array['money'] = $val->money;
            $array['check_status'] = '101';
            $array['reason'] = $val->reason;
            $array['error_number'] = $val->error_number;
            $array['time'] = $val->time;
            $array['bank'] = '';
            $array['bank_sub'] = '';
            $array['bank_add'] = '';
            $data = $bankModel->insert_bank_message($array);
            if($data){
                Log::info('用户银行卡认证信息迁移成功：'.$val->user_id);
            }else{
                Log::info('用户银行卡认证信息迁移失败：'.$val->user_id);
            }
        }
        Log::info('用户银行卡信息迁移完成');
    }

}