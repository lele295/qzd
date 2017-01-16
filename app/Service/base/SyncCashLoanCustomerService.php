<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/2/25
 * Time: 16:51
 */

namespace App\Service\base;


use App\Model\Base\AsUserAuthModel;
use App\Service\mobile\Service;

class SyncCashLoanCustomerService extends Service
{
    public function update_sync_cash_loan_customer_month_range(){
        AsUserAuthModel::chunk(1000,function($info){
            $this->set_sync_cash_loan_customer_month_range($info);
        });
    }

    public function set_sync_cash_loan_customer_month_range($info){
        $asUserAuthModel = new AsUserAuthModel();
        foreach($info as $val){
            $time = $this->get_month_range(strtotime($val->contract_time),time());
        //    $asUserAuthModel->update_month_range_by_customerid($val->CERTID,array('MONTHRANGE'=>$time));
            $asUserAuthModel->update_month_range_by_cert_id($val->CERTID,array('MONTHRANGE'=>$time));
        }
    }

    public function get_month_range($start,$end){
        if($start >= $end){
            Logger::info('开始时间大于结束时间');
            return 0;
        }
        $time = round(($end-$start)/(30*24*3600),3);
        return $time;
    }
}