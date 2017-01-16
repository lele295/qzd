<?php

namespace App\Service\datamigrate;

use App\Api\api\AnApi;
use App\Model\Base\UserModel;
use App\Model\Datamigrate\AuthModel;
use App\Service\mobile\Service;
use Illuminate\Support\Facades\Log;

//数据迁移
class AuthmigrateService extends Service
{
    public function get_auth_message(){
        $authModel = new AuthModel();
        $info = $authModel->get_auth_user_list();
        foreach($info as $val){
            $this->get_auth_api($val);
        }
        Log::info('实名认证迁移成功');
    }

    public function get_auth_update_message(){
        $authModel = new AuthModel();
        $info = $authModel->get_auth_list();
        foreach($info as $val){
            $this->get_auth_api_update($val);
        }
    }

    public function get_auth_api_update($auth){
        $anapi = new AnApi();
        $authBaseModel = new \App\Model\Base\AuthModel();
        $info = $anapi->get_custom_status($auth->real_name,$auth->id_card);
        if($info){
            $info = array_add($info,'created_at',date('Y-m-d H:i:s',time()));
            $info = array_add($info,'updated_at',date('Y-m-d H:i:s',time()));
            $flag = $authBaseModel->insert_or_update_auth_info($info,$auth->user_id);
            return $flag;
        }
    }

    public function get_auth_api($auth){
        $anapi = new AnApi();
        $authModel = new AuthModel();
        $authBaseModel = new \App\Model\Base\AuthModel();
        $user = new UserModel();
        $info = $anapi->get_custom_status($auth->real_name,$auth->id_card);
        if($info){
            $this->start_connect();
        //    $data = $user->update_user_info_by_id($auth->id, array('realname'=>$auth->real_name));
            $info = array_add($info,'created_at',date('Y-m-d H:i:s',time()));
            $info = array_add($info,'updated_at',date('Y-m-d H:i:s',time()));
            $info = array_add($info,'step_status',$this->get_user_loan($auth->id));
            $flag = $authBaseModel->insert_or_update_auth_info($info,$auth->id);
            $connect = $this->end_connect(array($flag));
            if($connect){
                Log::info($auth->real_name.':'.'用户认证成功');
                return array('status'=>true,'msg'=>'用户认证成功');
            }else{
                Log::info($auth->real_name.':'.'用户认证失败');
                return array('status'=>false,'msg'=>'用户认证失败');
            }
        }else{
            $loan = $authModel->get_user_loan_message($auth->real_name,$auth->id_card);
            if(empty($loan)){
                return;
            }
            $loan = $loan[0];
            $this->start_connect();
        //    $data = $user->update_user_info_by_id($auth->id, array('realname'=>$auth->real_name));
            $info = array();
            $info = array_add($info,'created_at',date('Y-m-d H:i:s',time()));
            $info = array_add($info,'updated_at',date('Y-m-d H:i:s',time()));
            $info = array_add($info,'CreditLimit',$loan->maxcash);
            $info = array_add($info,'SubProductType','1');
            $info = array_add($info,'user_id',$auth->id);
            $info = array_add($info,'TopMonthPayment',$loan->max_refund);
            $info = array_add($info,'real_name',$loan->name);
            $info = array_add($info,'id_card',$loan->idcard);
            $info = array_add($info,'CustomerID',$loan->contract_id);
            $info = array_add($info,'step_status',$this->get_user_loan($auth->id));
            $flag = $authBaseModel->insert_or_update_auth_info($info,$auth->id);
            $connect = $this->end_connect(array($flag));
            if($connect){
                Log::info($auth->real_name.':'.'用户认证成功');
                return array('status'=>true,'msg'=>'用户认证成功');
            }else{
                Log::info($auth->real_name.':'.'用户认证失败');
                return array('status'=>false,'msg'=>'用户认证失败');
            }
        }
    }

    public function get_user_loan($user_id){
        $authModel = new AuthModel();
        $info = $authModel->get_loan_status_by_user_id($user_id);
        if($info){
            $info = $info[0];
            if($info->status == '-1'){
                return '101';
            }elseif($info->status == '0'){
                return '101';
            }elseif($info->status == '1'){
                return '101';
            }elseif($info->status == '2'){
                return '101';
            }elseif($info->status == '3'){
                return '201';
            }else{
                return '101';
            }
        }else{
            return '101';
        }
    }
}