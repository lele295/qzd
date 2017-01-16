<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/10/26
 * Time: 10:50
 */

namespace App\Service\admin;



use App\Model\Admin\LoanAdminModel;
use App\Model\Base\UniqueCodeModel;
use App\Service\mobile\CenterService;
use Illuminate\Support\Facades\Log;

class SendService extends Service
{
    public function get_loan_send_msg(){
        $loanAdminModel = new LoanAdminModel();
        $centerService = new CenterService();
        $time = time();
        $time = strtotime(date('Y-m-d H',$time).':00:00');
        Log::info(date('Y-m-d H',$time).':00:00');
        $date_end = $time;
        $date =strtotime("-2 hours",$time);
        Log::info(array($date,$date_end));
        $data = $loanAdminModel->get_loan_by_create_time($date,$date_end);
        Log::info('发送数量为：');
        Log::info(count($data));
        foreach($data as $val){
           $centerService->send_weixin_message($val->id);
       }
    }

    public function send_msn_to_admin($message,$phone){
        $unique = new UniqueCodeModel();
        $unique->select_send_supply($message, $phone);
    }
}