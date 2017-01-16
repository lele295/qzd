<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/11/6
 * Time: 15:01
 */

namespace App\Service\admin;


use App\Commands\SendCancelWechat;
use App\Log\Facades\Logger;
use App\Model\Admin\LoanAdminModel;
use App\Model\Base\AuthModel;
use Illuminate\Support\Facades\Queue;

class CommandService extends Service
{
    public function update_over_date_loan(){
        $loanAdminModel = new LoanAdminModel();
        $authModel = new AuthModel();
        $event_time = strtotime('-1 days',time());
        $info = $loanAdminModel->get_over_loan_by_loan_status(array('011'),$event_time);
        Logger::info('系统自动取消客户订单的数量为：'.count($info));

        foreach($info as $val){
            $this->start_conn();
            $loanflag = $loanAdminModel->update_loan_by_id(array('status'=>'100','reason'=>'24小时内未提交，系统自动取消，取消时间为：'.date('Y-m-d H:i:s',time())),$val->id);
            $authflag = $authModel->update_auth_info_by_user_id(array('step_status'=>AuthModel::STEP_STATUS_LOAN_RE),$val->user_id);
            $flag = $this->end_conn(array($loanflag,$authflag));
            if(!$flag){
                Logger::error($val->realname.'-'.$val->user_id.'-订单id为：'.$val->id.'-于24小时内未提交，系统自动取消失败');
                continue;
            }
   //         Queue::push(new SendCancelWechat($val->id));
            Logger::info($val->realname.'-'.$val->user_id.'-订单id为：'.$val->id.'-于24小时内未提交，系统自动取消');
        }


    }
}