<?php

/**
 * 订单类
 * Class Order
 */
namespace App\Service\base;
use App\Api\api\AnApi;
use App\Api\api\SysApi;
use App\Log\Facades\Logger;
use App\Model\Base\AsRepaymentMessage;
use App\Model\Base\AuthModel;
use App\Model\Base\CommErrorModel;
use App\Model\Base\LoanModel;
use App\Model\Base\SyncModel;
use App\Model\Base\UserBankCardModel;
use App\Model\Base\UserModel;
use App\Service\admin\SendService;
use App\Service\admin\Service;
use App\Service\admin\UserService;
use App\Service\mobile\CenterService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Order{
    private $_id;
    public $_model;
    public $_filter;
    public $_user_model;

    public function __construct($orderFlag){
        if($orderFlag instanceof LoanModel){
            $this->_model = $orderFlag;
            $this->_id = $this->_model->id;
        }else{
            $this->_model = LoanModel::find($orderFlag);
            $this->_id = $this->_model->id;
        }
        $this->_filter = array('OrderId'=>$this->_id);
    }


    /**
     * 获得资料填写的状态,包括
     * 个人信息，银行卡信息，单位信息，邮寄地址与联系方式，家庭信息，收入及其他信息,附件信息
     */
    public function getResourceStatus(){
        if(AsCustomBaseMessage::tryCheck($this->_id) instanceof ResourceError){
            $customBaseMessageStatus = false;
        }else{
            $customBaseMessageStatus = true;
        }

        if(AsRepaymentMessage::tryCheck($this->_id) instanceof ResourceError){
            $repaymentMessageStatus = false;
        }else{
            $repaymentMessageStatus = true;
        }

        if(AsWorkMessage::tryCheck($this->_id) instanceof ResourceError){
            $workMessage = false;
        }else{
            $workMessage = true;
        }

        if(AsCommAdd::tryCheck($this->_id) instanceof ResourceError){
            $commAdd = false;
        }else{
            $commAdd = true;
        }

        if(AsFamilyMessage::tryCheck($this->_id) instanceof ResourceError){
            $familyMessageStatus = false;
        }else{
            $familyMessageStatus = true;
        }

        if(AsIncomeMessage::tryCheck($this->_id) instanceof ResourceError){
            $incomeMessage = false;
        }else{
            $incomeMessage = true;
        }

        if(AsCustomPic::tryCheckButData($this->_id) instanceof ResourceError){
            $customPic = false;
        }else{
            $customPic = true;
        }


        return array(
            'customBaseMessageStatus'=>$customBaseMessageStatus,
            'repaymentMessageStatus'=>$repaymentMessageStatus,
            'workMessage'=>$workMessage,
            'commAdd'=>$commAdd,
            'familyMessageStatus'=>$familyMessageStatus,
            'incomeMessage'=>$incomeMessage,
            'attachmentMessage'=>$customPic
        );
    }

    public function initUser(){
        return UserModel::find($this->_model->user_id);
    }

    public function ownInfo(){
        $res = DB::table('users')->leftJoin('auth','users.id','=','auth.user_id')->where(array('users.id'=>$this->_model->user_id))->first();
        return $res;
    }

    /**
     * 判断补录是否完成
     */
    public function judgeResourceDone(){
        if($this->_model->status != Loan::ORDER_STATUS_FILL_IN){
            return false;
        }
        $statusArr = $this->getResourceStatus();
        $flag = true;
        foreach($statusArr as $key=>$val){
            if($val === false){
                $flag = false;
                return false;
            }
        }
        if($flag){
            $this->_model->status = Loan::ORDER_STATUS_FILL_DONE;
            $this->_model->save();
            $this->setBankAuth();
            /*
            $api = new api();
            $info = $api->check_bank_user($this->_id);
            if($info && $data && $bankflag){
                DB::commit();
                return true;
            }else{
                DB::rollback();
                return new ResourceError($info['msg']);
            }
            */
            return true;
        }
    }

    /**
     * @param $uid 用户id
     * @return bool
     */
    public function checkUser($uid){
        if($this->_model->user_id == $uid){
            return true;
        }
        return false;
    }

    /**
     * @param $status
     * @return bool
     */
    public function checkStatus($status){
        if($this->_model->status == $status){
            return true;
        }
        return false;
    }

    /**
     * @desc 获取订单详情
     * @return array
     */
    public function detail(){
        return array_merge($this->_model->toArray());
    }

    /**
     * 获得id
     */
    public function id(){
        return $this->_id;
    }

    /**
     * 产品类型 1是交叉线性贷，3车主线性贷
     */
    public function subProductType(){
        $uid = $this->_model->user_id;
        $auth = AuthModel::where(array('user_id'=>$uid))->first();
        return $auth->SubProductType;
    }

    /**
     * 设置银行卡认证
     */
    public function setBankAuth(){
        /**
         * 取填写的银行卡数据
         */
        $bankObj = AsRepaymentMessage::where($this->_filter)->first();
        $ownInfo = $this->ownInfo();
        $number = 0.02;
        $obj = UserBankCardModel::firstOrCreate(array('user_id'=>$this->_model->user_id));
        $obj->real_name = $ownInfo->real_name;
        $obj->identification = $ownInfo->id_card;
        $obj->bank_name = SyncModel::bankCodeName($bankObj->OpenBank);
        $obj->bank = $bankObj->OpenBank;
        $obj->bank_add = $bankObj->City;
        $obj->bank_sub = $bankObj->OpenBranch;
        $obj->check_status = 100;
        $obj->number = $bankObj->ReplaceAccount;
        $obj->money = $number;
        $obj->save();
    }


    public function checkResourceSetp($status = 101){
        $uid = $this->_model->user_id;
        $auth = AuthModel::where(array('user_id'=>$this->_model->user_id))->first();
        $currentStatus = $auth->step_status;
        if(in_array($currentStatus,array(101,102,103)) && ($status <= $currentStatus)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @param $status
     */
    public function setResourceStep($status){
        $uid = $this->_model->user_id;
        $auth = AuthModel::where(array('user_id'=>$this->_model->user_id))->first();
        $auth->step_status = $status;
        $auth->save();
    }

    public function register(){
        $api = new AnApi();
        $response = $api->update_loan_status($this->_model->pact_number);
        if($response) {
            $Status = $response['Status'];
            if ($Status == 'Success') {
                $SerialNo = $response['SerialNo'];
                $affect = LoanModel::update_status_ascode($SerialNo, '020');
                if (!$affect) {
                    Logger::error("安硕合同号" . $this->_model->pact_number . "更新合同状态失败");
                    return (new CommErrorModel('更新失败!'))->toArray();
                } else {
                    Logger::info("安硕合同号" . $this->_model->pact_number . "更新合同状态成功");
                    return array('status' => true,'data'=>'注册成功！');
                }
            } else {
                Logger::error("安硕合同号" . $this->_model->pact_number . "更新合同状态失败");
                return (new CommErrorModel('更新失败!'))->toArray();
            }
        }
    }

    /**
     * @desc 尝试得到用户最新一笔待填写订单
     * @param $uid 用户id
     * @return mixed
     */
    static public function getLastestOrderId($uid){
        $obj = LoanModel::where(array('user_id'=>$uid))->orderBy('id','desc')->first();
        if($obj instanceof LoanModel){
            return $obj->id;
        }else{
            return 0;
        }
    }

    static public function tryInitOrder($id){
        $obj = LoanModel::find($id);
        if($obj instanceof LoanModel){
            return new Order($obj);
        }else{
            return new CommErrorModel('没有找到订单');
        }
    }

    /**
     * @desc 更新订单状态
     * @param $orderId
     */
    static public function updateStatus($orderId){
        $order = self::tryInitOrder($orderId);
        if(false == ($order instanceof Order)){
            return (new CommErrorModel('订单不存在!'))->toArray();
        }
        if(false == in_array($order->_model->status,array('020','050','070','080','0701',''))){
            return (new CommErrorModel('订单状态不需要更新!'))->toArray();
        }

        $asapi = new SysApi();
        $response = $asapi->loan_status($order->_model->pact_number);

        if (isset($response->data[0]) && ($response->data[0]->Status == 'Success')) {
            $ContractStatus = $response->data[0]->ContractStatus;
            if ($ContractStatus != $order->_model->status) {
                $affect = LoanModel::update_status_ascode($order->_model->pact_number, $ContractStatus);
                if (!$affect) {
                    Logger::error("安硕合同号" .$order->_model->pact_number . "同步状态失败");
                } else {

                    if($response->data[0]->ContractStatus == "080"){
                        //2016-07-21修改，若是分期购APP的提单需先经图片审核，不直接提交注册。1：APP 2：微信 3：PC
                        $loanModel = new LoanModel();
                        $loanModel->update_loan_by_id(array("approve_time" => date('Y-m-d H:i:s',time())), $orderId);
                        if($order->_model->source != 1){
                            $order->register(); //提交注册
                        }
                        Logger::info($order->_model->id.'-订单进入到图片审核','picture_audit');
                    }elseif($response->data[0]->ContractStatus == "100"){
                        $res_info = $asapi->loan_audit_status($order->_model->pact_number);
                        if(isset($res_info->data[0]->Status) && $res_info->data[0]->Status == 'Success'){
                            $loanmodel = new LoanModel();
                            $CancelRemark = "default";
                            Logger::error("项目id".$orderId);
                            Logger::error((array)$res_info->data[0]);

                            if(isset($res_info->data[0]->CancelRemark)){
                                $CancelRemark = $res_info->data[0]->CancelRemark;
                            }elseif(isset($res_info->data[0]->CancelReason)){
                                $CancelRemark = $res_info->data[0]->CancelReason;
                            }
                            if(isset($res_info->data[0]->CancelReason)){
                                $cancelType = $res_info->data[0]->CancelReason;
                            }else{
                                $cancelType = '';
                            }

                            $auth_m = new AuthModel();
                           // $server_base = new Service();
                           // $server_base->start_conn();
                            $affect1 = $loanmodel->update_loan_by_id(array("reason" => $CancelRemark,"cancel_type" => $cancelType), $orderId);
                            $affect2 = $auth_m->update_auth_info_by_user_id(array("step_status" => 200), $order->_model->user_id);
                            if(!$affect2){
                                $auth_m->update_auth_info_by_user_id(array("step_status" => 200), $order->_model->user_id);
                            }
                            if(!$affect1){
                                $loanmodel->update_loan_by_id(array("reason" => $CancelRemark), $orderId);
                            }
                            Logger::error("affect1".$affect1);
                            Logger::error("affect2".$affect2);
                           // $server_base->end_conn(array($affect1, $affect2));


                        }else{
                            $auth_m = new AuthModel();
                            $auth_m->update_auth_info_by_user_id(array("step_status" => 200), $order->_model->user_id);
                            Logger::info('用户id' . $order->_model->user_id . '订单' . $order->_model->id . '合同号' . $order->_model->pact_number,'no_cancel_reason');
                            Logger::error((array)$res_info);
                        }
                    }elseif($response->data[0]->ContractStatus == "210" || $response->data[0]->ContractStatus == '160' || $response->data[0]->ContractStatus == '110'){
                        $auth_m = new AuthModel();
                        $affect3 = $auth_m->update_auth_info_by_user_id(array("step_status" => 200), $order->_model->user_id);
                        if(!$affect3){
                            $auth_m->update_auth_info_by_user_id(array("step_status" => 200), $order->_model->user_id);
                        }
                    }
                    $centerservice = new CenterService();
                    $centerservice->send_auth_loan($orderId);
                    return array('status'=>true,'data'=>'订单状态更新成功！');
                }
            }else{
                return (new CommErrorModel('订单状态已是最新！'))->toArray();
            }
        }else{
            return (new CommErrorModel('更新失败！'))->toArray();
        }
    }

}