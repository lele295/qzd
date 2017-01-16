<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/3/15
 * Time: 16:11
 */

namespace App\Service\base;


use App\Model\Base\AsCommAddModel;
use App\Service\mobile\Service;
use App\Util\Loan;

class CommAddService extends Service
{
    public function get_comm_add_message($user_id,$loan_id=0){
        $loan = new Loan();
        $loan_entry = $loan->get_order_entry($user_id,$loan_id);
        if($loan_entry['status']){
            $loan_id_array = $loan_entry['data'];
            $comm = AsCommAddModel::where($loan_id_array)->first();
            return array('status'=>true,'data'=>array('message'=>'找到相应的数据','entry'=>$comm));
        }else{
            return array('status'=>false,'data'=>array('message'=>'没有相应的订单需要填写信息'));
        }
    }

    public function update_comm_add_message($array,$loan_id=0){
        $loan = Loan::get_order_entry($array['user_id']);
        if($loan['status']){
            $obj = AsCommAddModel::where($loan['data'])->first();
            $array = array_add($array,'OperationTime',date('Y-m-d H:i:s',time()));
            $array['WorkTel'] = $array['area_code'].'-'.$array['WorkTel'];
            $obj->update(array_only($array,['Flag8','WorkTel','MobileTelephone','WorkTelPlus','OperationTime']));
            return array('status'=>true,'data'=>array('message'=>'信息更新成功'));
        }else{
            return array('status'=>false,'data'=>array('message'=>'没有相应的订单'));
        }
    }
}