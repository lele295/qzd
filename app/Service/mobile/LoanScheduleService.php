<?php
namespace App\Service\mobile;

use App\Model\Base\LoanModel;
use App\Service\mobile\Service;
use Illuminate\Support\Facades\Log;

class LoanScheduleService extends Service
{
    /*
     * 获取还款信息
     */
    public function get_loan_schedules($loan_id)
    {
        $loanm = new LoanModel();
        $data["loan_schedules"] = $loanm->getloan_schedules($loan_id);
        return $data;
    }

    public function deal_loan_schedules($loan_id){
        $loanModel = new LoanModel();
        $info = $loanModel->getloan_schedules($loan_id);
        $schedule = array();
        if($info){
            foreach($info as $item){
                $array = array();
                $array = array_add($array,'Periods',$item->Periods);
                $array = array_add($array,'PayDate',date('Y-m-d',strtotime($item->PayDate)));
                $array = array_add($array,'TotalAmt',$item->TotalAmt);
                $array = array_add($array,'ActualTotalAmt',$item->ActualTotalAmt);
                if($item->ContractStatus==0){
                    $array = array_add($array,'Status','未还清');
                }else{
                    $array = array_add($array,'Status','已还清');
                }
                array_push($schedule,$array);
            }
        }
        return $schedule;
    }
}
