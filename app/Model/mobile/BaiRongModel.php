<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2016/10/18
 * Time: 14:42
 */

namespace App\Model\mobile;

use App\Log\Facades\Logger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class BaiRongModel extends Model{

    protected $table = "bairong";

    const NO_SUBMIT = '1';//订单未提交
    const END_CHECK = '2';//提交结束

    public function getOneByLoanId($order_id){
        $data = DB::table($this->table)->where('order_id',$order_id)->first();
        if(empty($data)){

            Logger::info('订单号：'.$order_id.'未获取到用户设备号，容错处理！','bairong');

            $order_obj = DB::table('orders')->where(['id'=>$order_id])->first();
            $orders_product_obj = DB::table('orders_product')->where(['id' => $order_obj->product_id])->first();

            $bairong_arr = array(
                'order_id' => $order_id,
                'account_mobile' => $order_obj->mobile,
                'id_number' => $orders_product_obj->applicant_id_card,
                'account_name' => $orders_product_obj->applicant_name,
                'inputdate' => date('Y/m/d H:i'),
                'channeltype' => '01',
                'af_swift_number' => '',
                'event' => '',
                'status' => self::NO_SUBMIT
            );

            $this->addOrUpdate($bairong_arr);
            $data = DB::table($this->table)->where('order_id',$order_id)->first();
        }
        return $data;
    }

    public function addOrUpdate($arr){

        $data = DB::table($this->table)->where('order_id',$arr['order_id'])->first();
        if(empty($data)){
            $res = DB::table($this->table)->insert($arr);
        }else{
            $res = DB::table($this->table)->where('order_id',$arr['order_id'])->update($arr);
        }
        return $res;
    }

    public function updateBaiRong($arr){
        DB::table($this->table)->where('order_id',$arr['order_id'])->update($arr);
    }

    public function sendToBaiRong($order_id,$contract_no){
        try {
            $loan_bairong = $this->getOneByLoanId($order_id);

            $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>
                    <validate>
                    <serialno>" . $contract_no . "</serialno>
                    <id_number>" . $loan_bairong->id_number . "</id_number>
                    <account_mobile>" . $loan_bairong->account_mobile . "</account_mobile>
                    <account_name>" . $loan_bairong->account_name . "</account_name>
                    <inputdate>" . $loan_bairong->inputdate . "</inputdate>
                    <channeltype>" . $loan_bairong->channeltype . "</channeltype>
                    <af_swift_number>" . $loan_bairong->af_swift_number . "</af_swift_number>
                    <event>" . $loan_bairong->event . "</event>
                    </validate>";


            ini_set("default_socket_timeout", 4);//设置4秒超时不等了

            $client_br = new \SoapClient(config('myconfig.br_api_url')."IBcScoreTask?wsdl");
            $response_br = $client_br->createTask(['in0' => 'bqjr', 'in1' => '3014AACFE8053CE17A1206955F97762F', 'in2' => $xml]);
            Logger::info('@' . $contract_no . '百融上传结果：' . json_encode($response_br), 'bairong');

            $client_td = new \SoapClient(config('myconfig.br_api_url')."IScoreTask?wsdl");
            $response_td = $client_td->createTask(['in0' => 'bqjr', 'in1' => '3014AACFE8053CE17A1206955F97762F', 'in2' => $xml]);
            Logger::info('@' . $contract_no . '同盾上传结果：' . json_encode($response_td), 'bairong');

            $bairongInfo = array(
                'order_id' => $order_id,
                'contract_no' => $contract_no,
                'status' => self::END_CHECK,
                'bairong_res' => isset($response_br->out) ? $response_br->out : '崩溃了- -！',
                'tongdun_res' => isset($response_td->out) ? $response_td->out : '崩溃了- -！'
            );
            $this->updateBaiRong($bairongInfo);
        }catch (\Exception $e){
            throw $e;
        }
    }

}