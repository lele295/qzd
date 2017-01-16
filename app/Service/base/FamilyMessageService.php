<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/3/16
 * Time: 10:58
 */

namespace App\Service\base;


use App\Model\Base\AsFamilyMessageModel;
use App\Service\mobile\Service;
use App\Util\Loan;

class FamilyMessageService extends Service
{
    public function get_family_message($user_id,$loan_id=0){
        $loan = Loan::get_order_entry($user_id,$loan_id);
        if($loan['status']){
            $loan_id_array = $loan['data'];
            $info = AsFamilyMessageModel::where($loan_id_array)->first();
            return array('status'=>true,'data'=>array('message'=>'找到相应的数据','entry'=>$info));
        }else{
            return array('status'=>false,'data'=>array('message'=>'没有相应的订单需要填写信息'));
        }
    }

    public function update_family_message($array){
        $loan = Loan::get_order_entry($array['user_id']);
        if($loan['status']){
            try {
                $loan_id = $loan['data'];
                $familyMessage = AsFamilyMessageModel::firstOrCreate($loan_id);
                $familyMessage->Marriage = $array['Marriage'];
                $familyMessage->Childrentotal = $array['Childrentotal'];
                $familyMessage->SpouseName = $array['SpouseName'];
                $familyMessage->SpouseTel = $array['SpouseTel'];
                $familyMessage->House = $array['House'];
                $familyMessage->Houserent = $array['Houserent'];
                $familyMessage->KinshipName = $array['KinshipName'];
                $familyMessage->KinshipTel = $array['KinshipTel'];
                $familyMessage->Flag10 = $array['Flag10'];
                $familyMessage->KinshipAdd = $array['KinshipAdd'];
                $familyMessage->RelativeType = $array['RelativeType'];
                $familyMessage->OrderId = $loan_id['OrderId'];
                $familyMessage->OperationTime = date('Y-m-d H:i:s',time());
                if(isset($array['OtherTelephone']) && $array['OtherTelephone']){
                    $familyMessage->OtherTelephone = $array['OtherTelephone'];
                }
                if(isset($array['SPOUSEWORKCORP']) && $array['SPOUSEWORKCORP']){
                    $familyMessage->SPOUSEWORKCORP = $array['SPOUSEWORKCORP'];
                }
                if(isset($array['SPOUSEWORKTEL']) && $array['SPOUSEWORKTEL']){
                    $familyMessage->SPOUSEWORKTEL = $array['SPOUSEWORKTEL'];
                }
                $familyMessage->save();
                return array('status'=>true,'data'=>array('message'=>'订单更新成功'));
            }catch(\Exception $e){
                throw $e;
            }
        }else{
            return array('status'=>false,'data'=>array('message'=>'没有相应订单'));
        }
    }
}