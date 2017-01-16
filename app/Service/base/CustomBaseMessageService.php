<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/3/16
 * Time: 10:19
 */

namespace App\Service\base;


use App\Model\Base\AsCustombaseMessageMobel;
use App\Service\mobile\Service;
use App\Util\Loan;

class CustomBaseMessageService extends Service
{
    public function get_custom_base_message($user_id,$loan_id=0){
        $loan = Loan::get_order_entry($user_id,$loan_id);
        if($loan['status']){
            $loan_id_array = $loan['data'];
            $info = AsCustombaseMessageMobel::where($loan_id_array)->first();
            return array('status'=>true,'data'=>array('message'=>'找到相应的数据','entry'=>$info));
        }else{
            return array('status'=>false,'data'=>array('message'=>'没有相应的订单需要填写信息'));
        }
    }

    public function update_custom_base_message($array){
        $loan = Loan::get_order_entry($array['user_id']);
        if($loan['status']){
            try {
                $loan_id_array = $loan['data'];
                $asCustomerBaseMessage = AsCustombaseMessageMobel::firstOrCreate($loan_id_array);
//                $asCustomerBaseMessage->CertType = $array['CertType'];
//                $asCustomerBaseMessage->Issueinstitution = $array['Issueinstitution'];
                $asCustomerBaseMessage->MaturityDate = $array['MaturityDate'];
                $asCustomerBaseMessage->NativePlace = $array['NativePlace'];
                $asCustomerBaseMessage->Villagetown = $array['Villagetown'];
                $asCustomerBaseMessage->Street = $array['Street'];
                $asCustomerBaseMessage->Community = $array['Community'];
                $asCustomerBaseMessage->CellNo = $array['CellNo'];
                $asCustomerBaseMessage->Flag2 = $array['Flag2'];
                $asCustomerBaseMessage->FamilyAdd = $array['FamilyAdd'];
                $asCustomerBaseMessage->Countryside = $array['Countryside'];
                $asCustomerBaseMessage->Villagecenter = $array['Villagecenter'];
                $asCustomerBaseMessage->Plot = $array['Plot'];
                $asCustomerBaseMessage->Room = $array['Room'];
                $asCustomerBaseMessage->OperationTime = date('Y-m-d H:i:s',time());
                if(isset($array['FamilyZip']) && $array['FamilyZip']){
                    $asCustomerBaseMessage->FamilyZip = $array['FamilyZip'];
                }
                $asCustomerBaseMessage->save();
                return array('status'=>true,'data'=>array('message'=>'信息更新成功'));
            }catch(\Exception $e){
                throw $e;
            }
        }else{
            return array('status'=>false,'data'=>array('message'=>'没有相应的订单'));
        }
    }
}