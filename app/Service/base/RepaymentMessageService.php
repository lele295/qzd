<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/3/16
 * Time: 11:29
 */

namespace App\Service\base;



use App\Log\Facades\Logger;
use App\Model\Base\AsRepaymentMessage;
use App\Model\Base\SyncModel;
use App\Service\mobile\Service;
use App\Util\CodeLibrary;
use App\Util\Loan;
use Illuminate\Support\Facades\App;

class RepaymentMessageService extends Service
{
    public function get_repayment_message($user_id,$loan_id=0){
        $loan = Loan::get_order_entry($user_id,$loan_id);
        if($loan['status']){
            $loan_id_array = $loan['data'];
            $info = AsRepaymentMessage::where($loan_id_array)->first();
            return array('status'=>true,'data'=>array('message'=>'可以进行相关操作','entry'=>$info));
        }else{
            return array('status'=>false,'data'=>array('message'=>'没有相应的订单需要填写信息'));
        }
    }

    public function auth_and_update_repayment_message($array){
        //$bankService = new BankService();
        //if(App::environment('product')){
        //    $bank_result = $bankService->bank_auth($array);
        //}else{
            $bank_result['status'] = true;
        //}
        if(!$bank_result['status']){
            return array('status'=>false,'data'=>array('message'=>$bank_result['message']['data']));
        }else{
            $info = $this->update_repayment_message($array);
            if($info['status']){
                return array('status'=>true,'data'=>array('message'=>$info['data']['message']));
            }else{
                return array('status'=>false,'data'=>array('message'=>$info['data']['message']));
            }
        }
    }

    public function update_repayment_message($array){
        $loan = Loan::get_order_entry($array['user_id']);
        if($loan['status']){
            try {
                $loan_id = $loan['data'];
                $repaymentMessageModel = AsRepaymentMessage::firstOrCreate($loan_id);
                $repaymentMessageModel->RepaymentWay = '1';
                $repaymentMessageModel->ReplaceAccount = $array['ReplaceAccount'];
                $repaymentMessageModel->OpenBank = $array['OpenBank'];
                $repaymentMessageModel->OpenBankName = CodeLibrary::get_bank_name_by_code($array['OpenBank']);
                $repaymentMessageModel->OpenBranch = $array['OpenBranch'];
                $repaymentMessageModel->OpenBranchName = CodeLibrary::get_bank_branch_name_by_code($array['OpenBranch']);
                $repaymentMessageModel->City = $array['City'];
                $repaymentMessageModel->CityName = CodeLibrary::get_city_name_by_code($array['City']);
                $repaymentMessageModel->ReplaceName = $array['real_name'];
                $repaymentMessageModel->OperationTime = date('Y-m-d H:i:s',time());
                $repaymentMessageModel->save();
                return array('status'=>true,'data'=>array('message'=>'银行卡信息保存成功'));
            }catch(\Exception $e){
                throw $e;
            }
        }else{
            return array('status'=>false,'data'=>array('message'=>'没有相应的订单'));
        }
    }

    public function get_newest_repayment_message($user_id){
        $loan = Loan::get_newest_order_entry($user_id);
        if($loan['status']){
            $loan_id = $loan['data'];
            $repayment_message = AsRepaymentMessage::firstOrCreate($loan_id);
            return array('status'=>true,'data'=>array('entry'=>$repayment_message));
        }else{
            return array('status'=>false,'data'=>array('entry'=>''));
        }
    }
}