<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/9/28
 * Time: 10:45
 */

namespace App\Service\base;


use App\Api\api\BankAuthApi;
use App\Api\api\BankNoApi;
use App\Api\api\SysApi;
use App\Log\Facades\Logger;
use App\Model\Base\AsRepaymentMessage;
use App\Model\Base\AsUserAuthModel;
use App\Model\Base\AuthModel;
use App\Model\Base\SyncModel;
use App\Model\Base\UserBankCardModel;
use App\Model\Base\UserBankNoModel;
use App\Service\mobile\Service;
use App\Util\SwitchFlag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class ApiService extends Service
{
    public function __construct(){

    }

    //银行卡认证
    public function bank_auth_check($user_id){
        $bankAuth = new BankAuthApi();
        $bank = new UserBankCardModel();
        $authModel = new AuthModel();
        $bank_message = $bank->get_bank_card_by_user_id($user_id);
    //    $bank_message->bank_sub = Config::get('myconfig.bank_bq_bank_sub_name');
        $auth = $authModel->get_auth_info_by_user_id($user_id);
        $array = array();
        $array = array_add($array,'customerid',$auth->CustomerID);
        $array = array_add($array,'payamt',$bank_message->money);
        $array = array_add($array,'outid','jqm'.date('YmdHis',time()));
        $array = array_add($array,'payacctno',$bank_message->number);
        $array = array_add($array,'payacctname',$bank_message->real_name);
        $array = array_add($array,'payacctbankno',$bank_message->bank_sub);
        $array = array_add($array,'payacctbankname',SyncModel::bankBranchName($bank_message->bank_sub));
        $array = array_add($array,'acpacctno',Config::get('myconfig.bank_bq_bank_no'));
        $array = array_add($array,'acpacctname',Config::get('myconfig.bank_bq_bank_name'));
        $array = array_add($array,'acpbankno',Config::get('myconfig.bank_bq_bank_sub_no'));
        $array = array_add($array,'acpbankname',Config::get('myconfig.bank_bq_bank_sub_name'));
        $authdata = $bankAuth->check_bank_user($array);
        $bank->update_bank_card_by_user_id(array('out_id'=>$array['outid'],'result_status'=>$authdata),$user_id);
        if($authdata){
            if($authdata == '0000'){
                return array('status'=>'true','msg'=>'银行卡认证成功');
            }elseif($authdata == '1111'){
                return array('status'=>'fail','msg'=>'银行卡认证失败，请核实卡号或更换银行卡');
            }elseif($authdata == '1001'){
                return array('status'=>'true','msg'=>'银行卡正在认证');
            }elseif($authdata == '9001'){
                return array('status'=>'true','msg'=>'银行卡认证成功');
            }else{
                return array('status'=>'false','msg'=>'系统繁忙，请重试');
            }
        }else{
            return array('status'=>'false','msg'=>'系统繁忙，请重试');
        }
    }

    //帐户认证
    public function bank_no_auth($loan_id,$array){
            Logger::info('进行帐户认证');
            $authModel = new AuthModel();
            $bankNoApi = new BankNoApi();
            $auth = $authModel->get_auth_info_by_user_id($array['user_id']);
            $array = array_add($array,'real_name',$auth->real_name);
            $array = array_add($array,'id_card',$auth->id_card);
            $bank_need_check = $this->get_user_old_bank_message($array);
            if($bank_need_check){
                Logger::info('本系统自动认证');
                Logger::info($bank_need_check);
                return $bank_need_check;
            }
            $out_id = 'jqm' . date('YmdHis', time());
            $data['outid'] = $out_id;
            $data['realname'] = $auth->real_name;
            $data['certno'] = $auth->id_card;
            $data['bankcardtype'] = 'DEBIT_CARD';
            $data['bankcode'] = Config::get('bank.' . $array['itemno']);
            $data['servicetype'] = 'INSTALLMENT';
            $data['bankcardno'] = $array['bankcardno'];
            $data['mobileno'] = $array['mobileno'];
            $data['infotype'] = '1';
            $data['customerid'] = $auth->CustomerID;
            $result = $bankNoApi->send_message_to_web($data);
            Logger::info('返回结果为：');
            Logger::info($result);
            $info = $this->return_bank_no_result($result);
            Logger::info($info);
            if ($info['status'] == 'true') {
                Logger::info('睡觉6秒');
                sleep(6);
                Logger::info('开始运行');
                $auth_reslut = $this->get_query_bank_no_auth($out_id,$array);
                return $auth_reslut;
            } else {
                Logger::error('系统繁忙');
                return array('status' => 'false', 'msg' => '系统繁忙，请重试');
            }
    }
    public function get_query_bank_no_auth($out_id,$array){
        for($i=0;$i<3;$i++){
            if($i==2){
                return array('status'=>'false','msg'=>'系统繁忙，请重试');
            }
            $flag = $this->query_bank_no_auth($out_id);
            if($flag){
                $this->insert_new_bank_no($array,$flag['status']);
               return $flag;
            }else{
                Logger::info('睡觉2秒');
                sleep(2);
                Logger::info('醒');
                continue;
            }
        }
    }
    public function query_bank_no_auth($out_id){
        $bankNoApi = new BankNoApi();
        $reslut = $bankNoApi->query_message_to_web($out_id);
        $info = $this->return_bank_no_result($reslut,false);
        if($info['status'] == 'middle'){
            return false;
        }else{
            return $info;
        }
    }

    public function return_bank_no_result($result,$type = true){
        if($type == true){
            if($result == '0000'){
                return array('status'=>'true','msg'=>'接收成功');
            }elseif($result == '1111'){
                return array('status'=>'false','msg'=>'接收失败');
            }elseif($result == '9001'){
                return array('status'=>'error','msg'=>'校验错误');
            }elseif($result == '9009'){
                return array('status'=>'error','msg'=>'密码或用户名错误');
            }elseif($result == '9999'){
                return array('status'=>'error','msg'=>'程序异常');
            }
        }else{
            if($result == '0000'){
                return array('status'=>'true','msg'=>'认证成功');
            }elseif($result == '1111'){
                return array('status'=>'false','msg'=>'银行账号不正确，请更换');
            }elseif($result == '1001'){
                return array('status'=>'middle','msg'=>'请等待');
            }elseif($result == '9001'){
                return array('status'=>'error','msg'=>'错误信息');
            }elseif($result == '9009'){
                return array('status'=>'error','msg'=>'密码或用户名错误');
            }elseif($result == '9999'){
                return array('status'=>'error','msg'=>'程序异常');
            }elseif($result == '9002'){
                return array('status'=>'error','msg'=>'数据不存在');
            }
        }

    }


    public function update_bank_auth_status($user_id){
        $bank = new UserBankCardModel();
        $bank_message = $bank->get_bank_card_by_user_id($user_id);
        if($bank_message->result_status == '1001'){
            $bankAuthApi = new BankAuthApi();
            $bank_status = $bankAuthApi->update_bank_check($bank_message->out_id);
            $bank->update_bank_card_by_user_id(array('result_status'=>$bank_status),$user_id);
            Logger::info('用户银行卡认证后台自动更新脚本中，'.$bank_message->real_name.'-更新状态为:'.$bank_status);
            return true;
        }
    }

    public function get_bank_list_by_result_status($array){
        $bank = new UserBankCardModel();
        $info = $bank->get_bank_card_by_result_status($array);
        return $info;
    }


    /**
     * 通过安硕API接口进行实名认证
     * @param $real_name
     * @param $id_card
     * @return mixed
     */

    public function get_cust_auth_message($real_name,$id_card){
        $sysApi = new SysApi();
        $info = $sysApi->usercheck($real_name,$id_card);
        if($info){
            $info = $info->data[0];
            if($info->requestStatus === '1'){
                $data['real_name'] = $info->CustomerName;
                $data['id_card'] = $info->CertID;
                $data['WorkAdd'] = $info->City;
                $data['city'] = $info->WorkAdd;
                $data['CreditLimit'] = $info->CreditLimit;
                $data['TopMonthPayment'] = $info->TopMonthPayment;
                $data['Periods'] = isset($info->Period)?$info->Period:'';
                $data['SubProductType'] = $info->CustomerType;
                $data['ProductFeatures'] = $info->ProductFeatures;
                $data['EventName'] = $info->EventName;
                $data['EventDate'] = $info->EventDate;
                $data['EventID'] = $info->SerialNo?$info->SerialNo:'NULL';
                $data['CustomerID'] = $info->CustomerID;
                $data['MobileTelephone'] = $info->MobileTelephone;
                $data['FamilyAdd'] = $info->FamilyAdd;
                $data['customerPhase'] = $info->CustomerPhase;
                if($info->CustomerType == 1){
                    SwitchFlag::$_auth_city = $info->WorkAdd;
                }
                return $data;
            }else{
                Logger::info("id:" . Auth::id() . ",姓名:$real_name,id_card:{$id_card}",'existerror');
                return false;
            }
        }else{
            Logger::info("id:" . Auth::id() . ",姓名:$real_name,id_card:{$id_card}",'existerror');
            return false;
        }
    }

/*
    public function get_cust_auth_message($real_name,$id_card){
        $asUserAuthModel = new AsUserAuthModel();
        $info = $asUserAuthModel->get_auth_cust_by_id_card_and_real_name($real_name,$id_card);
        if($info){
                $data['real_name'] = $info->CUSTOMERNAME;
                $data['id_card'] = $info->CERTID;
                $data['WorkAdd'] = $info->CITY;
                $data['city'] = $info->WORKADD;
                $data['CreditLimit'] = $info->CREDITLIMIT;
                $data['TopMonthPayment'] = $info->TOPMONTHPAYMENT;
                $data['Periods'] = isset($info->period)?$info->period:'';
                $data['SubProductType'] = $info->SubProductType;
                $data['ProductFeatures'] = $info->PRODUCTFEATURES;
                $data['EventName'] = $info->EVENTNAME;
                $data['EventDate'] = $info->EVENTDATE;
                $data['EventID'] = $info->SERIALNO;
                $data['CustomerID'] = $info->CUSTOMERID;
                $data['MobileTelephone'] = $info->MOBILETELEPHONE;
                $data['FamilyAdd'] = $info->FAMILYADD;
                $data['customerPhase'] = $info->CUSTOMERPHASE;
                return $data;
        }else{
            return false;
        }
    }
*/
    public function get_user_old_bank_message($array)
    {
        $userBankNoModel = new UserBankNoModel();
        $info = $userBankNoModel->get_user_bank_no($array['itemno'],$array['bankcardno'],
            $array['id_card'],$array['mobileno'],$array['user_id']);
        if($info){
            if($info->status == '1'){
                return array('status'=>'true','msg'=>'已经在本平台认证过');
            }else{
                return array('status'=>'false','msg'=>'银行卡帐号不正确，请更换银行卡');
            }
        }else{
            return false;
        }
    }

    public function insert_new_bank_no($array,$flag){
        $condition = $this->return_array($array);
        $userBankNoModel = new UserBankNoModel();
        if($flag == 'true'){
            $condition = array_add($condition,'status','1');
            $userBankNoModel->insert_user_bank_no($condition);
            Logger::info(array('status'=>'true','msg'=>'已经在本平台认证过'));
            return ;
        }else if($flag == 'false'){
            $condition = array_add($condition,'status','2');
            $userBankNoModel->insert_user_bank_no($condition);
            Logger::info(array('status'=>'false','msg'=>'银行卡帐号不正确，请更换银行卡'));
            return;
        }else{
            return ;
        }
    }

    private function return_array($array){
        $condition = array(
            'user_id'=>$array['user_id'],
            'real_name' =>$array['real_name'],
            'mobile' =>$array['mobileno'],
            'id_card'=>$array['id_card'],
            'open_bank'=>$array['itemno'],
            'open_bank_name'=>$array['bank_card_name'],
            'bank_card_no'=>$array['bankcardno'],
            'created_at'=>date('Y-m-d H:i:s',time()),
            'updated_at'=>date('Y-m-d H:i:s',time()),
        );
        return $condition;
    }
}