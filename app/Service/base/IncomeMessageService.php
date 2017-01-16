<?php
namespace App\Service\base;


use App\Model\Base\AsIncomeMessageModel;
use App\Service\mobile\Service;
use App\Util\Loan;
class IncomeMessageService extends Service
{
    public function get_income_message($user_id,$loan_id=0){
        $loan = Loan::get_order_entry($user_id,$loan_id);
        if($loan['status']){
            $loan_id_array = $loan['data'];
            $info = AsIncomeMessageModel::where($loan_id_array)->first();
            return array('status'=>true,'data'=>array('message'=>'找到相应的数据','entry'=>$info));
        }else{
            return array('status'=>false,'data'=>array('message'=>'没有相应的订单需要填写信息'));
        }
    }

    public function update_income_message($array){
        $loan = Loan::get_order_entry($array['user_id']);
        if($loan['status']){
            try {
                $loan_id_message = $loan['data'];
                $incomeMessage = AsIncomeMessageModel::firstOrCreate($loan_id_message);
                $incomeMessage->EduExperience = $array['EduExperience'];
                $incomeMessage->FamilyMonthIncome = $array['FamilyMonthIncome'];
                $incomeMessage->JobTime = $array['JobTime'];
                $incomeMessage->JobTotal = $array['JobTotal'];
                $incomeMessage->SelfMonthIncome = $array['SelfMonthIncome'];
                $incomeMessage->OtherRevenue = $array['OtherRevenue'];
//                $incomeMessage->Severaltimes = $array['Severaltimes'];
                $incomeMessage->Falg4 = $array['Falg4'];
                $incomeMessage->OtherContact = $array['OtherContact'];
                $incomeMessage->ContactTel = $array['ContactTel'];
                $incomeMessage->Contactrelation = $array['Contactrelation'];
                $incomeMessage->OrderId = $loan_id_message['OrderId'];
                $incomeMessage->OperationTime = date('Y-m-d H:i:s',time());
                $incomeMessage->save();
                return array('status' => true, 'data' => array('message' => '订单更新成功'));
            }catch(\Exception $e){
                throw $e;
            }
        }else{
            return array('status'=>false,'data'=>array('message'=>'没有相应订单'));
        }
    }
}