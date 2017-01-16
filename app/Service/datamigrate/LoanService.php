<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/10/8
 * Time: 15:11
 */

namespace App\Service\datamigrate;


use App\Model\Datamigrate\LoanModel;
use App\Service\mobile\Service;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class LoanService extends Service
{
    public function get_loan_message(){
        $loanModel = new LoanModel();
        $info = $loanModel->get_loan_list();
        foreach($info as $val){
            $this->start_connect();
            $array['id'] = $val->id;
            $array['user_id'] = $val->user_id;
            $array['loan_amount'] = $val->loan_amount;
            $array['status'] = $this->get_status($val->status);
            $array['remark'] = '05';
            $array['loan_period'] = $val->loan_period;
            $array['pact_number'] = $val->pact_number;
            $array['pact_url'] = $val->pact_url;
            $array['created_at'] = $val->created_at;
            $array['updated_at'] = $val->updated_at;
            $array['source'] = $val->source;
            $array['remark_descript'] = $val->remark;
            $array['issure'] = $val->insure == '1'?'1':2;
            $array['sa_id'] = Config::get('myconfig.sa_id');
            $data = $loanModel->insert_data($array);
            $loan_message['loan_id'] = $val->id;
            $loan_message['month_payment'] = $val->month_payment;
            $loan_message['payment_date'] = $val->payment_date;
            $loan_message['month_interest'] = $val->month_interest;
            $loan_message['first_payment'] = $val->first_payment;
            $loan_message['month_serve'] = $val->month_serve;
            $loan_message['month_manage'] = $val->month_manage;
            $loan_message['month_addint'] = $val->month_addint;
            $loan_message['first_payment_date'] = $val->first_payment_date;
            $loan_message['created_at'] = $val->created_at;
            $loan_message['updated_at'] = $val->updated_at;
            $loan_message['stamptax'] = 0;
            $loan_data = $loanModel->insert_loan_message($loan_message);
            $success = $this->end_connect(array($data,$loan_data));
            if($success){
                Log::info('订单更新成功：'.$val->id);
            }else{
                Log::info('订单更新失败：'.$val->id);
                continue;
            }
        }
        Log::info('订单数据迁移成功');
    }

    public function get_status($status){
        if($status == '1'){
            return '010';
        }elseif($status == '2'){
            return '010';
        }elseif($status == '3'){
            return '050';
        }elseif($status == '4'){
            return '100';
        }else{
            return '010';
        }
    }
    //处理特殊
    public function update_except_loan(){
        $loanModel = new LoanModel();
        $loanModel->update_loan_by_user_name('李靖',array('loan.pact_number'=>'14758605004'));
        $info = $loanModel->get_loan_message();
        foreach($info as $val){
            $this->start_connect();
            $array['id'] = $val->id;
            $array['user_id'] = $val->user_id;
            $array['loan_amount'] = $val->loan_amount;
            $array['status'] = '050';
            $array['remark'] = '05';
            $array['loan_period'] = $val->loan_period;
            $array['pact_number'] = $val->pact_number;
            $array['pact_url'] = $val->pact_url;
            $array['created_at'] = $val->created_at;
            $array['updated_at'] = $val->updated_at;
            $array['source'] = $val->source;
            $array['remark_descript'] = $val->remark;
            $array['issure'] = $val->insure == '1'?'1':2;
            $array['sa_id'] = Config::get('myconfig.sa_id');
            $data = $loanModel->insert_data($array);
            $loan_message['loan_id'] = $val->id;
            $loan_message['month_payment'] = $val->month_payment;
            $loan_message['payment_date'] = $val->payment_date;
            $loan_message['month_interest'] = $val->month_interest;
            $loan_message['first_payment'] = $val->first_payment;
            $loan_message['month_serve'] = $val->month_serve;
            $loan_message['month_manage'] = $val->month_manage;
            $loan_message['month_addint'] = $val->month_addint;
            $loan_message['first_payment_date'] = $val->first_payment_date;
            $loan_message['created_at'] = $val->created_at;
            $loan_message['updated_at'] = $val->updated_at;
            $loan_message['stamptax'] = 0;
            $loan_data = $loanModel->insert_loan_message($loan_message);
            $success = $this->end_connect(array($data,$loan_data));
            if($success){
                Log::info('订单更新成功：'.$val->id);
            }else{
                Log::info('订单更新失败：'.$val->id);
                continue;
            }
        }
        Log::info('订单数据迁移成功');
    }
}