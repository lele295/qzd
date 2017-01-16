<?php

namespace App\Service\api;


use App\Api\api\CaApi;
use App\Log\Facades\Logger;
use App\Model\Base\LoanModel;
use App\Model\Base\UniqueCodeModel;
use App\Model\mobile\ContractModel;
use App\Model\mobile\OrderModel;
use App\Service\mobile\Service;
use App\Util\FileReader;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class CaApiService extends Service
{

    /**
     * 进行ca数据发送与获取相关挑战码
     * 并将挑战发送到用户的手机
     * @param $order_id
     * @param $user_id
     * @return array
     */
    public function  send_challenge_code_to_ca_by_order_id($order_id,$contractNo){

        //获取进行ca验证需要的相关信息
        $orderModel = new OrderModel();
        $info = $orderModel->get_order_data_by_order_id($order_id);

        $result = '';
        if($info){
            $array = array();
            $array = array_add($array,'application',$info->protocol_url);
            $array = array_add($array,'mobile',$info->mobile);
            $array = array_add($array,'contractNo',$contractNo);
            $array = array_add($array,'real_name',$info->applicant_name);
            $array = array_add($array,'id_card',$info->applicant_id_card);
            $array = array_add($array,'product_type',$info->product_type);
            $array = array_add($array,'cert_face_pic',$info->cert_face_pic);
            $array = array_add($array,'cert_opposite_pic',$info->cert_opposite_pic);
            $array = array_add($array,'cert_hand_pic',$info->cert_hand_pic);

            $result = $this->get_challenge_code_from_ca($array);
            Logger::info('手机号是：'.$info->mobile,'ca-send');
            Logger::info($result,'ca-send');
            if($result['status']){
                $send_sms = $this->send_ca_challenge_code_sms_to_user_mobile($info,$result['data']['challengeCode']);
                if($send_sms['status']){
                    //challengeCode,TransID存到缓存(十分钟)
                    $time = Carbon::now()->addMinutes(10);
                    Cache::put("TransID".$order_id, $result['data']['TransID'], $time);
                    Cache::put("Code".$order_id, $result['data']['challengeCode'], $time);
                    //Logger::info("TransID".$order_id,'ca-cache');
                    //Logger::info("Code".$order_id,'ca-cache');
                    return array('status'=>true,'data'=>array('message'=>$send_sms['data']['message']));

                }else{
                    return array('status'=>false,'data'=>array('message'=>$send_sms['data']['message']));
                }
            }else{
                return array('status'=>false,'data'=>array('message'=>$result['data']['message']));
            }

        }else{
            return array('status'=>false,'data'=>array('message'=>$result['data']['message']="您目前还没有相关订单"));
        }
    }

    /**
     * 将合同信息组装好发送到CA，CA返回合同确认码
     * @param $array
     * @return array
     */
    public function get_challenge_code_from_ca($array){

        $cert_face_pic = FileReader::read_storage_image_resize_file($array['cert_face_pic'],false);
        $cert_opposite_pic = FileReader::read_storage_image_resize_file($array['cert_opposite_pic'],false);
        $cert_hand_pic = FileReader::read_storage_image_resize_file($array['cert_hand_pic'],false);
        //照片信息hash值
        $cert_face_pic_hash = FileReader::read_storage_text_file_to_binary($array['cert_face_pic']);
        $cert_opposite_pic_hash = FileReader::read_storage_text_file_to_binary($array['cert_opposite_pic']);
        $cert_hand_pic_hash = FileReader::read_storage_text_file_to_binary($array['cert_hand_pic']);

        $application = base64_decode(FileReader::read_storage_text_file($array['application']));
        $content = "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />".$application.'<br><br><br><br><br><br><br><br><br><br>';
        /*$content .= "<table><tr><th>图片类型</th><th>上传时间</th><th>图片hash</th></tr>";
        $content .= "<tr><td>身份证正面</td><td>".date('Y/m/d H:i:s',$array['order_create_time'])."</td><td>".$cert_face_pic_hash."</td></tr>";
        $content .= "<tr><td>身份证反面</td><td>".date('Y/m/d H:i:s',$array['order_create_time'])."</td><td>".$cert_opposite_pic_hash."</td></tr>";
        $content .= "<tr><td>手持身份证</td><td>".date('Y/m/d H:i:s',$array['order_create_time'])."</td><td>".$cert_hand_pic_hash."</td></tr></table>";
        */
        $content .= "身份证正面：".$cert_face_pic_hash.'<br>';
        $content .= "身份证反面：".$cert_opposite_pic_hash.'<br>';
        $content .= "手持身份证：".$cert_hand_pic_hash;

        $content= base64_encode($content);
        $data = array(
            "phoneNum"=>$array['mobile'],
            "contractType"=>"001",
            "contractNo"=>$array['contractNo'],
            "customerName"=>$array['real_name'],
            "certId"=>$array['id_card'],
            "contractContent"=>$content,
            "cert_face_pic"=>$cert_face_pic,
            "cert_opposite_pic"=>$cert_opposite_pic,
            "cert_hand_pic"=>$cert_hand_pic,
        );

        $caApi = new CaApi();
        $result = $caApi->send_ca_info($data);

        if($result['Success'] == "true"){
            return array('status'=>true,'data'=>array('message'=>'CA签名合同确认码发送成功','TransID'=>$result['TransID'],'challengeCode'=>$result['challengeCode']));
        }else{
            if($result['TransID']){
                return array('status'=>false,'data'=>array('message'=>$result['TransID']));
            }else{
                return array('status'=>false,'data'=>array('message'=>'服务器繁忙请重试'));
            }
        }
    }

    /**
     * 将挑战码发送到客户手机
     * @param $user_message
     * @param $challengeCode
     * @return array
     */
    public function send_ca_challenge_code_sms_to_user_mobile($user_message,$challengeCode){
        //发送的短信没有入库
        $unique = new UniqueCodeModel();
        $mobile_txt = "尊敬的客户，您的合同确认码为：".$challengeCode."(有效期10分钟)";
        $mess_res = $unique->select_send_supply($mobile_txt, $user_message->mobile);
        if($mess_res['status'] == 1) {
            return array('status'=>true,'data'=>array('message'=>'合同确认码发送成功'));
        }else{
            return array('status'=>false,'data'=>array('message'=>'短信发送失败，请重试"'));
        }
    }

    /**
     * 对用户输入的挑战码进行校验
     * @param $order_id 订单id
     * @param $user_id 用户id
     * @param $code 用户输入的验证码
     * @return array
     */
    public function check_ca_challenge_code_from_user($order_id,$code){
        //Logger::info(Cache::get('TransID'.$order_id),'ca-cache');
        //Logger::info(Cache::get('Code'.$order_id),'ca-cache');
        if(!Cache::has('TransID'.$order_id) || !Cache::has('Code'.$order_id)){
            return array('status'=>false,'data'=>array('message'=>'挑战码已失效，请获取挑战码'));
        }else{
            $orderModel = new OrderModel();
            $contractModel = new ContractModel();
            $data = $orderModel->get_order_data_by_order_id($order_id);
            $contractInfo = $contractModel->get_contract_info_by_order($order_id);
            $data->contract_no = $contractInfo->contract_no;
            $check_result = $this->check_ca_challenge_code($order_id,$data,$code);
            return $check_result;
        }
    }



    /**
     * 对挑战码进行验证并发送结果给CA
     * @param $data 订单内容
     * @param $code 挑战码
     * @return array
     */
    public function check_ca_challenge_code($order_id,$data,$code){

        $transId = Cache::get("TransID".$order_id);
        $challengeCode = Cache::get('Code'.$order_id);

        if($code == $challengeCode){
            $data = array("phoneNum" => $data->mobile, "contractType"=>"001", "transId" => $transId, "challengeCode" => $code, "contractNo" => $data->contract_no);
            $caApi = new CaApi();
            $result = $caApi->check_ca_info($data);

            //验证码检测后，删除相关缓存
            Cache::forget("TransID".$order_id);
            Cache::forget('Code'.$order_id);
            if ($result['Success'] == "true") {
                return array('status'=>true,'data'=>array('message'=>'ca签名成功'));
            }else{
                return array('status'=>false,'data'=>array('message'=>'系统繁忙'));
            }
        }else{
            return array('status'=>false,'data'=>array('message'=>'挑战码输入错误'));
        }
    }
}