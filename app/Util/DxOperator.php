<?php
namespace App\Util;
use App\Model\Admin\AdminLoanModel;
use App\Model\Base\LoanModel;
use App\Service\mobile\Service;
use Illuminate\Support\Facades\Auth;

/**
 * 电销录单类
 * Created by PhpStorm.
 * User: hp
 * Date: 2016/5/31
 * Time: 14:29
 */

class DxOperator{
    static public $flag = false;    //（试算时）字段过滤标识
    static public $send = false;  //（录单完成）发送短信、微信标识

    /**
     * 检测是否属于电销录单
     * @return bool
     */
    static public function isDxOperator(){
        if(!Auth::check()){
            return false;
        }
        $loanModel = new LoanModel();
        $loanInfo = $loanModel->get_loan_newest(Auth::id());
        //订单为待提交状态才继续
        if($loanInfo->status != Service::WAIT_SUBMIT){
            return false;
        }
        $adminLoan = new AdminLoanModel();
        $info = $adminLoan->get_admin_loan_by_loan_id($loanInfo->id);   //有数据属于电销
        if(!$info){
            return false;
        }
        return true;
    }
}