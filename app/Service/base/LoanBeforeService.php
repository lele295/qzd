<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/5/25
 * Time: 16:41
 */

namespace App\Service\base;


use App\Log\Facades\Logger;
use App\Model\Base\AuthModel;
use App\Model\Base\LoanBeforeModel;
use App\Model\Base\LoanModel;
use App\Service\admin\LoanService;
use App\Service\mobile\Service;
use App\Util\Loan;
use App\Util\LocationUtil;

class LoanBeforeService extends Service
{
    public function update_loan_before($loan_id,$array){
        $loanBeforeModel = new LoanBeforeModel();
        $loan_before = $loanBeforeModel->update_loan_before_by_loan_id($loan_id,$array);
        return $loan_before;
    }

    public function get_location($user_id,$latitude,$longitude){
        if($latitude && $longitude){
            $location = LocationUtil::get_location($latitude,$longitude);
            $loan = Loan::get_order_entry($user_id);
            if($loan['status']){
                $loan_id = $loan['data']['OrderId'];
                $this->update_loan_before($loan_id,$location);
            }
        }else{
            return '';
        }
    }

    public function init_loan_before($user_id,$loan_id){
        //register_at    实名时间 auth表create_at
        $auth_model = new AuthModel();
        $auth = $auth_model->get_auth_info_by_user_id($user_id);
        $array['register_at'] = $auth->created_at;
        //create_at   loan表create_at
        $loan_service = new LoanService();
        $loan_info = $loan_service->get_loan_by_id($loan_id);
        $array['create_at'] = $loan_info->created_at;
        $loan_before = $this->update_loan_before($loan_id,$array);
        return $loan_before;
    }

    //初始化update_at，apply_time
    public function updateLoanTime($loan_id){
        $loan_service = new LoanService();
        $loan_info = $loan_service->get_loan_by_id($loan_id);
        $now = time();
        $array['update_at'] = date('Y-m-d H:i:s',$now);
        $array['apply_time'] = $now-strtotime($loan_info->created_at);
        $loan_before = $this->update_loan_before($loan_id,$array);
        return $loan_before;
    }

    public function get_loan_before_by_loan_id($loan_id){
        $loanBeforeModel = new LoanBeforeModel();
        $loan_before = $loanBeforeModel->get_loan_before_by_loan_id($loan_id);
        return $loan_before;
    }

    public function update_pic_upload_by_loan_id($loan_id,$upload){
        foreach ($upload as $item) {
            if(!$item){
                return '';
            }
        }
        $array['cert_time1'] = $upload['cert_face_pic_after_time'] - $upload['cert_face_pic_before_time'];
        $array['cert_time2'] = $upload['cert_opposite_pic_after_time'] - $upload['cert_opposite_pic_before_time'];
        $array['cert_time3'] = $upload['custom_pic_after_time'] - $upload['custom_pic_before_time'];

        $loan_model = new LoanModel();
        $loan_info = $loan_model->get_loan_by_id($loan_id);
        $array['source'] =  $loan_info->source;

        $this->update_loan_before($loan_id,$array);
    }
}