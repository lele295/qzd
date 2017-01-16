<?php

namespace App\Service\base;


use App\Model\mobile\ContractModel;
use App\Model\mobile\StoreInfoModel;
use App\Log\Facades\Logger;
use App\Model\mobile\OrderModel;
use App\Model\mobile\OrderProductModel;
use App\Model\mobile\OrderSchModel;
use App\Model\mobile\OrderWorkModel;
use App\Service\mobile\Service;
use App\Util\FileWrite;
use App\Util\FileAction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DocumentService extends Service
{
    public function __construct(){

    }

    //获取最新的项目id并生成协议
    public function get_new_order_deal($user_id,$contractNo)
    {
        $contractModel = new ContractModel();
        $rs = $contractModel->get_contract_info_by_id($contractNo);
        $order_id = $rs->order_id;
        $orderModel = new OrderModel(); //获取订单的一些信息
        $check_id = $orderModel->get_order_info($order_id);
        if(!$check_id){
            return false;
        }

        //删除旧的协议文件，生成新的协议文件
        if($check_id->protocol_url){
            try {
                FileAction::remove_storage_file($check_id->protocol_url);
            }catch(\Exception $e){
                Logger::info('文件不存在');
                return false;
            }
        }
        //dd($check_id);
        //获取合同上所需的数据
        $info = $this->get_data_to_application($order_id,$contractNo);
        $view = $this->get_view_information($order_id,$info);
        $path = '/uploads/html/'.date("Y-m-d",time()).'/';
        $name = $order_id.'-'.$user_id.'-'.time().'.html';
        $name_path = FileWrite::write_storage_base64_encode_file($path,$name,$view);

        //dd($name_path);
        if($name_path){
            try {
                $this->start_connect();
                $orderModel = new OrderModel();
                //更新生成的协议文件路径放到了orders表里面
                $orderModel->update_order_by_id(array('protocol_url' => $name_path), $order_id);
                $this->commit();
            }catch(\Exception $e){
                $this->rollback();
                throw $e;
            }
        }else{
            return false;
        }

        //返回订单id
        return $info;
    }


    public function get_view_information($order_id,$info){
        try {
            $orderModel = new OrderModel();
            $res = $orderModel->get_contract_types($order_id);

            if($res == '2015011700000003'){
                //中信信托有限责任公司
                $view = view('mobile/document/fenqi_zx_application', $info);
            }else if($res == '2014060300000001') {
                //中泰信托有限责任公司
                $view = view('mobile/document/fenqi_zt_application', $info);
            }
            return $view;

        }catch(\Exception $e){
            Logger::info('生成协议出现异常');
        }
    }


    //获取数据到用户协议
    public function get_data_to_application($order_id,$contractNo){

        $orderModel = new OrderModel();
        $data = $orderModel->get_order_data_by_order_id($order_id);

        //orders_product,orders_school,orders_work
        $orderWorkModel = new OrderWorkModel();//工作信息
        $w_data = $orderWorkModel->get_user_work_info($data->work_id);
        $orderSchModel = new OrderSchModel();//学校信息
        $s_data = $orderSchModel->get_user_sch_info($data->school_id);
        $orderProModel = new OrderProductModel();//贷款信息
        $p_data = $orderProModel->get_user_pro_info($data->product_id);
        $storeModel = new StoreInfoModel();
        $store_data = $storeModel->get_store_info_by_order_id($order_id);//门店信息
        $rate_data = $storeModel->get_rate_info_by_order_id($order_id,$p_data->loan_money,$p_data->periods);//费率信息
        $conModel = new ContractModel();//合同还款信息
        $con_data = $conModel->get_contract_info_by_id($contractNo);
        //判断中信，中泰
        $contract_types = $orderModel->get_contract_types($order_id);

        return array(
            'work_data'=>$w_data,
            'sch_data'=>$s_data,
            'product_data'=>$p_data,
            'order_id'=>$order_id,
            'order_data'=>$data,
            'store_data'=>$store_data,
            'contract_data'=>$con_data,
            'rate_data'=>$rate_data,
            'contract_types'=>$contract_types
        );
    }

}