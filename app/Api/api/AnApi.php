<?php

namespace App\Api\api;

use App\Log\Facades\Logger;
use App\Model\Admin\CityLimitModel;
use App\Model\Base\AsUserAuthModel;
use App\Model\Base\AuthModel;
use App\Service\base\ApiService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use PhpSpec\Exception\Exception;

class AnApi implements BaseApi{

    private $asapi = '';

    public function __construct(){
        $this->asapi = new SysApi();
    }


    /**
     * 获取存量客户的信息
     * 更新于2016-01-20
     * @param $name
     * @param $id_card
     */
    public function get_customer_message($name,$id_card){
        try{
            $apiService = new ApiService();
            $info = $apiService->get_cust_auth_message($name,$id_card);
            $arr = array();
            if(!empty($info)){
                $arr['real_name'] = $info['real_name'];
                $arr['id_card'] = $info['id_card'];
                $arr['WorkAdd'] = $info['WorkAdd']; //客户所在城市
                $arr['FailureReason'] = isset($info['FailureReason']) ? $info['FailureReason'] : '';  //失败原因，01—是存量、02—非存量客户、03—三个月内有办理过佰仟贷款
                $arr['CreditLimit'] = $info['CreditLimit']; //最新贷款额度
                $arr['TopMonthPayment'] = $info['TopMonthPayment']; //最高月供
                $arr['Periods'] = $info['Periods'];  //最大贷款期数
                $arr['SubProductType'] = $info['SubProductType'];  //产品类型
                $arr['ProductFeatures'] = $info['ProductFeatures'];  //产品特征（常规产品）
                $arr['EventName'] = $info['EventName'];  //活动名称
                $arr['EventDate'] = $info['EventDate']; //活动有效期
                $arr['EventID'] = $info['EventID'];
                $arr['CustomerID'] = $info['CustomerID']; //客户在安硕ID
                $arr['MobileTelephone'] = $info['MobileTelephone'];
                $arr['FamilyAdd'] = $info['FamilyAdd'];
                $arr['customerPhase'] = $info['customerPhase'];
                return $arr;
            }else{
                Logger::info('-----'.$name.'-'.$id_card.'实名认证返回信息为空，不符合货款资格-----','an');
                return false;
            }
        }catch (\Exception $e){
            Logger::error($name.'-'.$id_card.'实名认证出现异常','yunwei');
            throw $e;
        }
    }

    /**
     * 查看是否为存量用户
     * @param $name 用户姓名
     * @param $id_card 身份证号
     */
    public function get_custom_status($name,$id_card){
        try {
            $apiService = new ApiService();
            $info = $apiService->get_cust_auth_message($name,$id_card);
            $arr = array();
            Logger::info($name.'-'.$id_card.'-实名认证返回结果为：','an');
            if (!empty($info)) {
                Logger::info($info,'an');
                if(empty($info['FamilyAdd'])){
                    Logger::info($name.'-'.$id_card.'-该客户的家庭信息为空，不能通过认证','an');
                    return false;
                }
                //获取后台黑名单城市限制
                $city_limit_m = new CityLimitModel();
                $check_limit = $city_limit_m->sel_code_city($info['city']);
                if($check_limit){
                    Logger::error($name.'-'.$id_card.'-该客户工作地址不在城市名单内');
                    return "limit_city";
                }
                $arr['real_name'] = $info['real_name'];
                $arr['id_card'] = $info['id_card'];
                $arr['WorkAdd'] = $info['WorkAdd']; //客户所在城市
                $arr['FailureReason'] = isset($info['FailureReason']) ? $info['FailureReason'] : '';  //失败原因，01—是存量、02—非存量客户、03—三个月内有办理过佰仟贷款
                $arr['CreditLimit'] = $info['CreditLimit']; //最新贷款额度
                $arr['TopMonthPayment'] = $info['TopMonthPayment']; //最高月供
                $arr['Periods'] = $info['Periods'];  //最大贷款期数
                $arr['SubProductType'] = $info['SubProductType'];  //产品类型
                $arr['ProductFeatures'] = $info['ProductFeatures'];  //产品特征（常规产品）
                $arr['EventName'] = $info['EventName'];  //活动名称
                $arr['EventDate'] = $info['EventDate']; //活动有效期
                $arr['EventID'] = $info['EventID'];
                $arr['CustomerID'] = $info['CustomerID']; //客户在安硕ID
                $arr['MobileTelephone'] = $info['MobileTelephone'];
                $arr['FamilyAdd'] = $info['FamilyAdd'];
                $arr['customerPhase'] = $info['customerPhase'];
                return $arr;
            } else {
                Logger::info($name.'-'.$id_card.'实名认证返回信息为空，不符合货款资格','an');
                return false;
            }
        }catch(Exception $e){
            Logger::error($e);
            Logger::error($name.'-'.$id_card.'实名认证出现异常','yunwei');
            return false;
        }
    }

    /**
     * @param $user_id 用户id
     * @param $amount 货款本金
     * @param $period 期数
     * @param bool $isflag 是否投保 是1否2
     * 如果成功将会返回数组
     */
    public function get_data_by_message($user_id,$amount,$period,$city,$isflag= 1){
        $auth = new AuthModel();
        $data = $auth->get_auth_info_by_user_id($user_id);
        if(!empty($data)){
            if($data->SubProductType == '1'){
                $eventData =strtotime($data->EventDate);
                $date = date('Y-m-d',time());
                if($eventData >= strtotime($date)){
                    return $this->create_data_to_sys($amount,$period,$city,$isflag,$data);
                }else{
                    Logger::info('产品日期异常');
                    return false;
                }
            }else{
                return $this->create_data_to_sys($amount,$period,$city,$isflag,$data);
            }
        }else{
            return false;
        }
    }


    private function create_data_to_sys($amount,$period,$city,$isflag,$data){
        $EventId = empty($data->EventID)?'\'\'':$data->EventID;
        $info = $this->asapi->count_param_res_new($amount,$period,$EventId,$data->SubProductType,$data->real_name,$data->id_card,$city,$isflag);
        if(!empty($info)){
            $info = (array)$info->data;
            $info = $info[0];
            $info = (array)$info;
            Logger::info('获取参数内容为：','an',$info);
            if(array_key_exists('Failure',$info)){
                Logger::error('-----试算获取参数出现异常-----','yunwei',$info);
                return false;
            }else{
                return $info;
            }
        }else{
            return false;
        }
    }


    /**
     * 获取还款计划接口
     * @param $construct_no  合同号
     * @return array|bool 如果有值则返回array，否则将返回false
     */
    public function get_load_schedules($construct_no){
        $info = $this->asapi->repayment_plan($construct_no);
        if(!empty($info)){
            if(!empty($info->data)){
                return $info->data;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * 获取订单合同状态
     * @param $construct_no 合同号
     */
    public function get_load_status($construct_no){
        $info = $this->asapi->loan_status($construct_no);
        if(!empty($info)){
            if(isset($info->data)){
                $data = $info->data;
                $data = $data['0'];
                return (array)$data;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * 合同提交注册接口
     * @param $construct_no 订单安硕合同号
     * @return bool 如果是true为注册成功，为false为注册失败
     */
    public function update_loan_status($construct_no){
        $data = $this->asapi->loan_status_commit($construct_no);
        if(!empty($data)){
            if(isset($data->data)){
                $info = $data->data[0];
                Logger::info($info->Status);
                if($info->Status == 'Success'){
                    Logger::info('订单提交注册成功');
                    Logger::info((array)$info);
                    return (array)$info;
                }else{
                    Logger::info('订单提交注册失败');
                    Logger::info((array)$info);
                    Logger::error('------订单提交注册失败-----','yunwei',(array)$info);
                    return (array)$info;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function get_user_is_baiqian($cert_id,$tel){
        $userGroupApi = new UserGroupApi();
        $info = $userGroupApi->user_baiqian_customer($cert_id,$tel);
        try {
            if ($info) {
                $status = $info->data[0];
                if($status){
                    if ($status->Status) {
                        return true;
                    } else {
                        return false;
                    }
                }else{
                    return false;
                }

            } else {
                return false;
            }
        }catch(\Exception $e){
            Logger::error('------------分组返回有异常情况----------','yunwei',(array)$info);
            return false;
        }
    }

}
