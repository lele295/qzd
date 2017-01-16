<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/5/5
 * Time: 10:10
 */

namespace App\Service\api;


use App\Log\Facades\Logger;
use App\Model\Base\AuthModel;
use App\Model\Base\LoanMessageModel;
use App\Model\Base\LoanModel;
use App\Model\Base\UserModel;
use App\Service\base\RepaymentMessageService;
use App\Service\mobile\Service;
use Illuminate\Support\Facades\Auth;

class CenterApiService extends Service
{
    public function get_refund_info($user_id){
        $loanmodel = new LoanModel();
        $loan_info = $loanmodel->get_loan_newest($user_id);
        if(!isset($loan_info->status) || $loan_info->status != "050"){
            return array('status'=>false,'data'=>array('message'=>'没有还款计划','entry'=>''));
        }
        $loanmessage = new LoanMessageModel();
        $data['refundinfo'] = $loanmessage->sel_id_loan_schedules($loan_info->id);
        return array('status'=>true,'data'=>array('message'=>'获取到还款计划','entry'=>$data));
    }

    public function get_person_self_info($user_id){
        $userModel = new UserModel();
        $authModel = new AuthModel();
        $repaymentMessageService = new RepaymentMessageService();
        $user = $userModel->get_user_message_by_id($user_id);
        $auth = $authModel->get_auth_info_by_user_id($user_id);
        $repayment_message = $repaymentMessageService->get_newest_repayment_message($user_id);
        $repayment = $repayment_message['data']['entry'];
        $array['mobile'] = isset($user->mobile)?$user->mobile:'';
        $array['created_at'] = isset($user->created_at)?$user->created_at:'';
        $array['real_name'] = isset($auth->real_name)?$auth->real_name:'';
        $array['id_card'] = isset($auth->id_card)?$auth->id_card:'';
        $array['bank'] = isset($repayment->OpenBankName)?$repayment->OpenBankName:'';
        $array['bank_no'] = isset($repayment->ReplaceAccount)?$repayment->ReplaceAccount:'';
        $array['bank_city'] = isset($repayment->CityName)?$repayment->CityName:'';
        return array('status'=>true,'data'=>array('message'=>'个人信息','entry'=>$array));
    }


}