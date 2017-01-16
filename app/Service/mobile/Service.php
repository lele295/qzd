<?php
namespace App\Service\mobile;
use App\Model\Base\LoanModel;
use Illuminate\Support\Facades\DB;
class Service{

    //订单状态
    const DENY_LOAN = '010'; //已否决
    const WAIT_SUBMIT = '011';   //待提交
    const HAS_XIANSU = '020'; //已签署
    const HAS_ZHUCE = '050';  //已注册，已经生成还款计划
    const SHENHE_ING = '070'; //审核中
    const SHENHE_PASS = '080'; //审核通过
    const SHEHE_CANCEL = '100';  //已取消，审核员取消
    const LOAN_FINISH = '110';  //已结清，已经还款完
    const LOAN_FINISH_EARLY = '160'; //提交还款结清
    const LOAN_CEXIAO = '210'; // 已撤销，审批通过,已签署，已注册才能撤

    //订单来源
    const app_source = '1';    //订单来源为app
    const wei_xin_source = '2';    //订单来源为借钱么微信
    const pc_source = '3';  //订单来源为PC
    const dx_source = '4';  //订单来源为“电销录单”
    const fqg_source = '5'; //订单来源为分期购微信
    const car_source = '6'; //车主现金贷

    //客户的操作状态
    const WRITE_APP = '101';  //填写贷款
    const WRITE_PERIOSN = '102'; //个人资料
    const WRITE_WORD = '103';  //单位资料
    const WRITE_PIC = '104';  //上传图片
    const BANK_WAIT = '105'; //银行卡待审核
    const BANK_PAY = '106'; //银行卡已打款
    const BANK_FAIL = '107'; //银行卡不通过
    const BANK_PASS = '108'; //银行卡通过
    const RE_LOAN = '200';  //重新货款
    const LOAN_TO_SYS = '201'; //已提单

    //活体验证字段
    const not_vivo = '0'; //0未验证 1验证通过 2验证不通过
    const vivo_pass = '1'; //1验证通过
    const vivo_not_pass = '2'; //2验证不通过

    const vivo_pass_point = '75';  //验证通过分数

    //对数库开启一个事务
    protected function start_connect(){
        DB::beginTransaction();
    }

    //对数据库进行提交或是回滚
    protected function end_connect($array= array()){
        if(in_array(false,$array) || in_array(0,$array)){
            DB::rollback();
            return false;
        }else{
            DB::commit();
            return true;
        }
    }

    protected function commit(){
        DB::commit();
    }

    protected function rollback(){
        DB::rollback();
    }

    public function check_loan_status($user_id,$loan_id = ''){
        $loanMode = new LoanModel();
        if(!$loan_id){
            $loan = $loanMode->get_loan_newest($user_id);
        }else{
            $loan = $loanMode->get_loan_by_id($loan_id);

        }
        if($loan && in_array($loan->status,array('020','050','070','080'))){
            return true;
        }else{
            return false;
        }

    }


}