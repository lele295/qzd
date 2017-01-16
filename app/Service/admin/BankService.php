<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/11/24
 * Time: 17:11
 */

namespace App\Service\admin;


use App\Api\api\BankNoApi;
use App\Log\Facades\Logger;
use App\Model\Admin\AdminBankNoCheckModel;
use App\Model\Admin\BankCardResultModel;
use App\Model\Admin\LoanAdminModel;
use App\Model\Base\AsRepaymentMessage;
use App\Model\Base\AuthModel;
use App\Model\Base\SyncModel;
use App\Model\Base\UserBankNoModel;
use App\Model\Base\UserModel;
use App\Service\base\BankNewService;
use App\Util\AdminAuth;
use App\Util\AdminRule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class BankService extends Service
{
    public function get_bank_card_result_list(){
        $bankCardResultModel = new BankCardResultModel();
        $info = $bankCardResultModel->get_bank_card_result_list();
        return $info;
    }

    public function add_bank_card_result($data){
        $bankCardResultModel = new BankCardResultModel();
        $info = $bankCardResultModel->add_bank_card_result_list($data);
        return $info;
    }

    public function delete_bank_card_result($id){
        $bankCardResultModel = new BankCardResultModel();
        $info = $bankCardResultModel->delete_bank_card_result($id);
        return $info;
    }

    public function edit_bank_card_result($id){
        $bankCardResultModel = new BankCardResultModel();
        $info = $bankCardResultModel->get_bank_card_result_by_id($id);
        return $info;
    }

    public function update_bank_card_result($array,$id){
        $bankCarResulModel = new BankCardResultModel();
        $info = $bankCarResulModel->update_bank_card_result_by_id($array,$id);
        return $info;
    }


    //根据订单id进行银行卡认证
    public function send_bank_check_by_loan_id($loan_id){
        try {
            $asRepaymentMessage = new AsRepaymentMessage();
            $loan_repayment_message = $asRepaymentMessage->get_data_by_loan_id($loan_id);  //获取订单还款信息
            $loanAdminModel = new LoanAdminModel();
            $loan_message = $loanAdminModel->get_loan_by_id($loan_id);
            $authModel = new AuthModel();
            $auth_message = $authModel->get_auth_info_by_user_id($loan_message->user_id);
            $userModel = new UserModel();
            $user_message = $userModel->get_user_message_by_id($loan_message->user_id);
            $array = array();
            $array = array_add($array, 'real_name', $auth_message->real_name);
            $array = array_add($array, 'id_card', $auth_message->id_card);
            $array = array_add($array, 'CustomerID', $auth_message->CustomerID);
            $array = array_add($array, 'itemno', $loan_repayment_message->OpenBank);
            $array = array_add($array, 'bankcardno', $loan_repayment_message->ReplaceAccount);
            $array = array_add($array, 'mobileno', $user_message->mobile);
            $array = array_add($array, 'bank_card_name', SyncModel::bankBranchName($loan_repayment_message->OpenBank));
            $array = array_add($array, 'user_id', AdminAuth::id());
            $array = array_add($array, 'loan_id', $loan_id);
            return $array;
        }catch(\Exception $e){
            return false;
        }
    }


    public function send_bank_no_check($array){

    }


    public function get_bank_no_list(){
        $adminBankNoCheckModel = new AdminBankNoCheckModel();
        $data = $adminBankNoCheckModel->get_bank_no_list();
        return $data;
    }

    public function get_bank_by_loan_id($loan_id){
        $bankNewService = new BankNewService();
        $array = $this->send_bank_check_by_loan_id($loan_id);
        if($array){
            $info = $bankNewService->send_bank_no_api_auth($array);
            if($info['status']){
                $expiresAt = Carbon::now()->addMinutes(30);
                Cache::put($loan_id.'bank_no_auth',$info['data']['out_id'],$expiresAt);
                return array('status'=>true,'data'=>array('message'=>$info['data']['message']));
            }else{
                return array('status'=>false,'data'=>array('message'=>$info['data']['message']));
            }
        }else{
            return array('status'=>false,'data'=>array('message'=>'没有该订单'));
        }
    }

    public function get_bank_no_auth_result($loan_id){
        if(!Cache::has(serialize($loan_id.'bank_no_auth'))){
            $info = $this->get_bank_no_auth_by_detail($loan_id,serialize($loan_id.'bank_no_auth'));
            if(!$info['status']){
                return array('status'=>false,'data'=>array('message'=>$info['data']['message']));
            }
        }
        $bankNewService = new BankNewService();
        $result = $bankNewService->get_bank_no_auth(Cache::get($loan_id.'bank_no_auth'));
        return $result;
    }

    public function get_bank_no_auth_by_detail($array,$key){
        $bankNewService = new BankNewService();
        $info = $bankNewService->send_bank_no_api_auth($array);
        if($info['status']){
            $expiresAt = Carbon::now()->addMinutes(30);
            Cache::put($key,$info['data']['out_id'],$expiresAt);
            return array('status'=>true,'data'=>array('message'=>$info['data']['message']));
        }else{
            return array('status'=>false,'data'=>array('message'=>$info['data']['message']));
        }
    }

   public function get_bank_no_auth_result_by_detail($array){
       $info = $this->get_bank_detail_array($array);
      if($info){
          if(!Cache::has(serialize($info))){
              $result = $this->get_bank_no_auth_by_detail($info,serialize($info));
              if(!$result['status']){
                  return array('status'=>false,'data'=>array('message'=>$result['data']['message']));
              }
          }
          $bankNewService = new BankNewService();
          $result = $bankNewService->get_bank_no_auth(Cache::get(serialize($info)));
          return $result;
      }else{
          return array('status'=>false,'data'=>array('message'=>'有输入框为空'));
      }
   }

   public function get_bank_detail_array($array){
       $rule = AdminRule::admin_bank_rule($array);
       if($rule['status']){
           $array = $rule['data']['message'];
           $bank = array();
           $bank = array_add($bank, 'real_name', $array['real_name']);
           $bank = array_add($bank, 'id_card', $array['id_card']);
           $bank = array_add($bank, 'CustomerID', $array['customer_id']);
           $bank = array_add($bank, 'itemno',$array['open_bank']);
           $bank = array_add($bank, 'bankcardno', $array['number']);
           $bank = array_add($bank, 'mobileno', $array['mobile']);
           $bank = array_add($bank, 'bank_card_name', SyncModel::bankBranchName($array['open_bank']));
           $bank = array_add($bank, 'user_id', AdminAuth::id());
           return $bank;
       }else{
           return false;
       }
   }
}