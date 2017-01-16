<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/3/16
 * Time: 13:56
 */

namespace App\Service\base;


use App\Model\Base\AsWordMessageModel;
use App\Service\mobile\Service;
use App\Util\Loan;

class WorkMessageService extends Service
{
    public function get_work_message($user_id,$loan_id=0){
        $loan = Loan::get_order_entry($user_id,$loan_id);
        if($loan['status']){
            $loan_id_array = $loan['data'];
            $info = AsWordMessageModel::where($loan_id_array)->first();
            return array('status'=>true,'data'=>array('message'=>'可以进行相关操作','entry'=>$info));
        }else{
            return array('status'=>false,'data'=>array('message'=>'没有相应的订单需要填写信息'));
        }
    }

    public function update_work_message($array,$loan_id=0){
        $loan = Loan::get_order_entry($array['user_id'],$loan_id);
        if($loan['status']){
            $loanService = new LoanService();
            if(!$loanService->check_firm_info_is_allow($array['WorkAdd'])){
                return array('status'=>false,'data'=>array('message'=>'工作所在城市暂不支持申请'));
            }else{
                try {
                    $order_id_array = $loan['data'];
                    $workMessage = AsWordMessageModel::firstOrCreate($order_id_array);
                    $workMessage->WorkAdd = $array['WorkAdd'];
                    $workMessage->WorkCorp = $array['WorkCorp'];
                    $workMessage->EmployRecord = $array['EmployRecord'];
                    $workMessage->HeadShip = $array['HeadShip'];
                    $workMessage->CellProperty = $array['CellProperty'];
                    $workMessage->UnitKind = $array['UnitKind'];
                    $workMessage->Flag3 = $array['Flag3'];
                    $workMessage->UnitCountryside = $array['UnitCountryside'];
                    $workMessage->UnitStreet = $array['UnitStreet'];
                    $workMessage->UnitRoom = $array['UnitRoom'];
                    $workMessage->UnitNo = $array['UnitNo'];
                    $workMessage->OperationTime = date('Y-m-d H:i:s',time());
                    if(isset($array['WorkZip']) && $array['WorkZip']){
                        $workMessage->WorkZip = $array['WorkZip'];
                    }
                    $workMessage->save();
                    return array('status'=>true,'data'=>array('message'=>'订单工作单位更新成功'));
                }catch(\Exception $e){
                    throw $e;
                }
            }
        }else{
            return array('status'=>false,'data'=>array('message'=>'没有相应的订单'));
        }
    }
}