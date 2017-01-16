<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/3/7
 * Time: 15:33
 */

namespace App\Service\base;


use App\Api\api\BankNoApi;
use App\Log\Facades\Logger;
use App\Service\mobile\Service;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class BankNewService extends Service
{
    /**
     * 进行接口认证访问与数据发送
     * @param $array
     */
    public function send_bank_no_api_auth($array){
        try {
            $out_id = 'jqm' . date('YmdHis', time());
            $data['outid'] = $out_id;
            $data['realname'] = $array['real_name'];
            $data['certno'] = $array['id_card'];
            $data['bankcardtype'] = 'DEBIT_CARD';
            $data['bankcode'] = Config::get('bank.' . $array['itemno']);
            $data['servicetype'] = 'INSTALLMENT';
            $data['bankcardno'] = $array['bankcardno'];
            $data['mobileno'] = $array['mobileno'];
            $data['infotype'] = '2';
            $data['customerid'] = $array['CustomerID'];
            $bankNoApi = new BankNoApi();
            $result = $bankNoApi->send_message_to_web($data);
            $result_status = $this->get_send_result_status($result);
            if($result_status['status'] == '100'){
                return array('status'=>true,'data'=>array('message'=>$result_status['data']['message'],'code'=>$result_status['data']['code'],'out_id'=>$out_id));
            }else{
                return array('status'=>false,'data'=>array('message'=>$result_status['data']['message'],'code'=>$result_status['data']['code'],'out_id'=>$out_id));
            }
        }catch(\Exception $e){
            throw $e;
        }
    }

    /**
     * 进行api接口查询
     * @param $out_id
     * @return array|bool
     */
    public function query_bank_no_auth($out_id){
        $bankNoApi = new BankNoApi();
        $result = $bankNoApi->query_message_to_web($out_id);
        return $result;
    }


    /**
     * 进行银行结果查询
     * @param $out_id
     */
    public function get_bank_no_auth($out_id){
        for($i=0;$i<3;$i++){
            $info = $this->query_bank_no_auth($out_id);
            $result = $this->get_query_result_status($info);
            if($result['status'] !== '200'){
                $data = $result;
                break;
            }else{
                if($i==2){
                    $data = $result;
                }
                sleep(1);
                continue;
            }

        }
        return $data;

    }

    /**
     * 对发送银行卡认证返回结果进行分析
     * @param $result
     * @return array
     * status = 100 成功发送
     * status = 400 发送失败
     * status = 300 程序出现异常
     */
    public function get_send_result_status($result){
        if($result == '9999'){
            return array('status'=>'300','data'=>array('message'=>'程序异常','code'=>''));
        }elseif($result['result'] == '0000'){
            Logger::info('bank银行卡发送验证成功','record');
            Logger::info((array)$result,'record');
            return array('status'=>'100','data'=>array('message'=>'接收成功','code'=>$result['resultcode']));
        }elseif($result['result'] == '1111'){
            Logger::info('bank银行卡发送验证失败','record');
            Logger::info((array)$result,'record');
            return array('status'=>'400','data'=>array('message'=>'接收失败','code'=>$result['resultcode']));
        }else{
            Logger::info('bank银行卡发送验证异常','record');
            Logger::info((array)$result,'record');
            return array('status'=>'300','data'=>array('message'=>'程序异常','code'=>$result['resultcode']));
        }
    }

    /**
     * 根据银行认证的out_id返回查询结果
     * 进行分类
     * @param $result
     * @return array
     * status = 300 为容错操作
     * status = 200 为中间状态
     * status = 400 为用户信息有误，需要更换银行卡
     * status = 100 为正确信息
     */
    public function get_query_result_status($result){
        Logger::info((array)$result);
        if($result['result'] == '0000'){
            Logger::info('bank银行卡认证结果认证成功','record');
            return array('status'=>'100','data'=>array('message'=>'认证成功','code'=>$result['resultcode'],'origin'=>$result));
        }elseif($result['result'] == '1111'){
            $error = $this->return_error_status($result);
            if($error['status']=='300'){
                //易极付出错
                Logger::info('bank银行卡认证结果易极付出错','record');
                return array('status'=>'300','data'=>array('message'=>$error['data']['message'],'code'=>$error['data']['code'],'origin'=>$result));
            }else{
                //银行卡错误
                Logger::info('bank银行卡认证结果认证成功','record');
                return array('status'=>'400','data'=>array('message'=>$error['data']['message'],'code'=>$error['data']['code'],'origin'=>$result));
            }
        }elseif($result['result'] == '1001'){
            Logger::info('bank银行卡认证结果中间状态','record');
            return array('status'=>'200','data'=>array('message'=>'请等待','code'=>$result['resultcode'],'origin'=>$result));
        }else{
            Logger::info('bank银行卡认证结果数据不存在','record');
            return array('status'=>'300','data'=>array('message'=>'数据不存在','code'=>$result['resultcode'],'origin'=>$result));
        }
    }

    public function return_error_status($result){
        $error_code = Config::get('bank_error.bank_error_code');
        if(in_array($result['resultcode'],$error_code)){
            return array('status'=>'300','data'=>array('message'=>'易极付出错或银行卡身份证错误,直接跳过','code'=>$result['resultcode'],'origin'=>$result));
        }else{
            return array('status'=>'400','data'=>array('message'=>'银行账号不正确，请更换','code'=>$result['resultcode'],'origin'=>$result));
        }
    }


}