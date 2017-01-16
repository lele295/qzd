<?php
namespace App\Service\base;


use App\Api\api\AnApi;
use App\Api\api\SysApi;
use App\Log\Facades\Logger;
use App\Model\Base\AsCommAddModel;
use App\Model\Base\AsCustombaseMessageMobel;
use App\Model\Base\AsCustomPicModel;
use App\Model\Base\AsFamilyMessageModel;
use App\Model\Base\AsIncomeMessageModel;
use App\Model\Base\AsRepaymentMessage;
use App\Model\Base\AsUserAuthModel;
use App\Model\Base\AsWordMessageModel;
use App\Model\Base\AuthModel;
use App\Model\Base\LoanMessageModel;
use App\Model\Base\LoanModel;
use App\Model\Base\ResourceErrorModel;
use App\Model\Base\SyncBusinessTypeModel;
use App\Model\Base\SyncStoreInfoModel;
use App\Model\Base\UserBankCardModel;
use App\Model\Base\UserBankNoModel;
use App\Model\Base\UserModel;
use App\Service\mobile\Service;
use App\Util\FileReader;
use App\Util\IntersetRate;
use App\Util\Loan;
use App\Util\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class LoanService extends Service
{
    private $loan;
    public function __construct(){
        $this->loan = new Loan();
    }

    public function get_user_is_can_loan_message_v2($user_id){
        $authModel = new AuthModel();
        $auth = $authModel->get_auth_info_by_user_id($user_id);
        if($auth){
            $authService = new AuthService();
            $auth_result = $authService->update_auth_user_message($auth->real_name,$auth->id_card);
            if($auth_result['status']){
                return array('status'=>true,'message'=>array('data'=>'可以进行货款'));
            }else{
                return array('status'=>false,'message'=>array('data'=>$auth_result['data']['message']));
            }
        }else{
            return array('status'=>false,'message'=>array('data'=>'请先完成实名认证'));
        }
    }

    /**
     * 获取用户是否可以贷款的信息
     * 2016-02-22
     * @param $user_id
     * @return array
     */
    public function get_user_is_can_loan_message($user_id){
        $authModel = new AuthModel();
        $auth = $authModel->get_auth_info_by_user_id($user_id);
        if($auth){
            if($auth->step_status == '201'){
                return array('status'=>false,'message'=>array('data'=>'请勿重复提单'));
            }
            $info = $this->user_event_date_check(array('event_date'=>$auth->EventDate,'real_name'=>$auth->real_name,'id_card'=>$auth->id_card));
            if($info['status']){
                $loan_message = $this->user_old_order_deal($auth->real_name,$auth->id_card);
                if($loan_message['status']){
                    $auth_new = $authModel->get_auth_info_by_user_id($user_id);
                    Session::put('user_auth',$auth_new);
                    return array('status'=>true,'message'=>array('data'=>$auth_new));
                }else{
                    return $loan_message;
                }

            }else{
                return $info;
            }
        }else{
            return array('status'=>false,'message'=>array('data'=>'请先完成实名认证'));
        }
    }



    /**
     * 检查客户的活动是否已过期
     * @param array $event
     * @return array
     */
    public function user_event_date_check($event = array()){
        $authService = new AuthService();
        $date = date('Y-m-d',time()).' 00:00:00';
        if(strtotime($event['event_date']) < strtotime($date)){
           $info = $authService->update_auth_user_message($event['real_name'],$event['id_card']);
           if($info['status'] == true){
              return array('status'=>true,'message'=>array('data'=>'更新成功'));
           }else{
              return array('status'=>false,'message'=>array('data'=>$info['data']['message']));
           }
        }else{
            return array('status'=>true,'message'=>array('data'=>'用户信息已是最新，不需要进行更新操作'));
        }
    }

    /**处理用户是否在3个月办理过相关货款
     * @param $user_id
     * @return array
     */
    public function user_old_order_deal($real_name,$id_card){
        $anapi = new AnApi();
        $info = $anapi->get_customer_message($real_name, $id_card);
        if($info){
            return array('status'=>true,'message'=>array('data'=>'符合货款条件'));
        }else{
            return array('status'=>false,'message'=>array('data'=>'您的贷款资格已失效或存在一笔审核中的贷款！详询：400-998-7101'));
        }
        /*
        $asUserAuthModel = new AsUserAuthModel();
        $authMessage = $asUserAuthModel->get_auth_cust_by_id_card_and_real_name($real_name,$id_card);
        if($authMessage){
            return array('status'=>true,'message'=>array('data'=>'符合货款条件'));
        }else{
            return array('status'=>false,'message'=>array('data'=>'您的贷款资格已失效，请保持良好的还款记录以便于下次邀请！'));
        }
        */
    }

    /**
     * 处理用户24小时内没有处理的订单
     * @param $user_id
     */
    public function deal_over_time_order($user_id){
        $authService = new AuthService();
        $authService->get_auth_loan_message_by_user_id($user_id);
    }



    /***
     * 获取最大代款额与期数信息
     */
    public function  get_loan_amount_period_message($user_id){
        $authModel = new AuthModel();
        $authService = new AuthService();
        $auth = $authModel->get_auth_info_by_user_id($user_id);
        $eventData =strtotime($auth->EventDate);
        $date = date('Y-m-d',time());
        if(($eventData < strtotime($date) || empty($auth->EventDate)) && $auth->SubProductType == '1'){
            Logger::error($auth->real_name.'-'.$auth->id_card.'-'.$auth->EventDate.'活动已过期');
            $update_info = $authService->auth_user_message($auth->real_name,$auth->id_card);
            if($update_info['status'] == true){
                Logger::info($auth->real_name.'-'.$auth->id_card.':试算时更新客户认证信息成功');
                $auth = $authModel->get_auth_info_by_user_id($user_id);
                return array('status'=>true,'msg'=>$auth);
            }else{
                Logger::error($auth->real_name.'-'.$auth->id_card.':试算时调用客户认证信息更新出错');
                return array('status'=>false,'msg'=>'暂时不能申请，请等待下次电话或短信邀约');
            }
        }else{
            $info = $authService->check_auth_is_over($user_id);
            if($info){
                return array('status'=>true,'msg'=>$auth);
            }else{
                return array('status'=>false,'msg'=>'您的贷款资格已失效，请保持良好的还款记录以便于下次邀请！');
            }

        }
    }
    /**
     * 获取最新一条待提交订单数据
     */
    public function get_wain_sumbit_nextest_loan($user_id)
    {
        $loanModel = new LoanModel();
        $info = $loanModel->get_loan_wain_submit_loan($user_id,Service::WAIT_SUBMIT);
        return $info;
    }

    /**
     * 获取用户可选的期数与金额产品
     */
    public function get_loan_product($user_id){
        $authModel = new AuthModel();
        $auth = $authModel->get_auth_info_by_user_id($user_id);
        if($auth){
            if($auth->SubProductType == '3'){ //车主现金货
                return $this->get_car_amount_period_message($auth->CreditLimit,$auth->Periods);
            }else if($auth->SubProductType == '1'){  //交叉现金货
                return $this->get_cash_amount_period_message($auth->CreditLimit,$auth->TopMonthPayment,$auth->ProductFeatures);
    //            $info = $this->get_cash_period_message($auth->CreditLimit,$auth->TopMonthPayment,$auth->ProductFeatures);
   //             return $info['data']['message'];
     //           return $this->get_cash_period_message($auth->CreditLimit,$auth->TopMonthPayment,$auth->ProductFeatures);
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * 获取用户可选的期数与金额
     * 2016-01-26
     * @param $user_id
     * @return array
     */
    public function get_loan_amount_product($user_id){
        $authModel = new AuthModel();
        $auth = $authModel->get_auth_info_by_user_id($user_id);
        if($auth){
            $info = $this->get_cash_period_message($auth->CreditLimit,$auth->TopMonthPayment,$auth->ProductFeatures);
            return $info;
        }else{
            return array('status'=>false,'message'=>array('data'=>'请先进行实名认证'));
        }
    }

    //初始化用户的产品列表
    public function init_cash_amount_period_message(){
        $businessTypeModel = new SyncBusinessTypeModel();
        $info = $businessTypeModel->get_all_product_cash_list();
        $array_loan = array();
        foreach($info as $val){
            $intersetRate = new IntersetRate($val);
            $pay_message = $intersetRate->get_month_pay();
            $array = (array)$val;
            $array = array_add($array,'origin_pay',round($pay_message['origin_pay']));
            $array = array_add($array,'month_pay',round($pay_message['month_pay'],2));
            array_push($array_loan,$array);
        }
        $expiresAt = Carbon::now()->addHours(4);
        Cache::put('loan_product',$array_loan,$expiresAt);
    }

    public function get_cash_period_message($creditlimit,$toppayment,$ProductFeatures){
        if(!Cache::has('loan_product')){
            $this->init_cash_amount_period_message();
        }
        $product_info = Cache::get('loan_product');
        $flag = '';
        $array = array();
        $period_array = array();
        foreach($product_info as $val){
            if($val['origin_pay'] < $toppayment && $val['LOWPRINCIPAL'] <=$creditlimit && strstr($val['typename'],mb_substr($ProductFeatures,0,2))){
                if(empty($flag)){
                    $flag = round($val['LOWPRINCIPAL']);
                }
                if($flag == $val['LOWPRINCIPAL']){
                    $period_array = array_add($period_array,$val['TERM'],$val['month_pay']);
                }else{
                    if(!empty($period_array)){
                        $array[$flag] = $period_array;
                        $flag = round($val['LOWPRINCIPAL']);
                        $period_array = array();
                        $period_array = array_add($period_array,$val['TERM'],$val['month_pay']);
                    }
                }
            }
        }
        $array[$flag] = $period_array;
        if(!empty($array)){
            return array('status'=>true,'message'=>array('data'=>$array));
        }else{
            return array('status'=>false,'message'=>array('data'=>'暂不支持微信申请，请拨打4009987101前往佰仟门店办理'));
        }
    }


    //获取交叉现金货相关信息
    public function get_cash_amount_period_message($creditlimit,$toppayment,$ProductFeatures){
        $businessTypeModel = new SyncBusinessTypeModel();
        $amount = $businessTypeModel->get_product_cash_list($creditlimit);
        $info = array();
        foreach($amount as $val){
            $month_pay = array();
            if(strstr($ProductFeatures,'优惠')){
                $period = $businessTypeModel->get_peroid_to_cash_youhui($val->LOWPRINCIPAL);
            }else{
                $period = $businessTypeModel->get_peroid_to_cash($val->LOWPRINCIPAL);
            }
            if(!empty($period)){
                foreach($period as $data){
                    $intersetRate = new IntersetRate($data);
                    $pay_message = $intersetRate->get_month_pay();
                    if(round($pay_message['origin_pay'],0) < $toppayment){
                        $month_pay = array_add($month_pay,$data->TERM,$pay_message['month_pay']);
                    }
                }
                if(!empty($month_pay)){
                    $info[$val->LOWPRINCIPAL] = $month_pay;
                }
            }
        }
        return $info;
    }

    //获取车主现金货相关信息
    public function get_car_amount_period_message($creditlimit,$period){
        $info = array();
        $businessTypeModel = new SyncBusinessTypeModel();
        $amount = $businessTypeModel->get_product_car_list($creditlimit);
        foreach($amount as $val){
            $month_pay = array();
            $period = $businessTypeModel->get_period_to_car($val->LOWPRINCIPAL,$period);
            foreach ($period as $data) {
                $intersetRate = new IntersetRate($data,false);
                $pay_month = $intersetRate->get_month_pay();
                $month_pay = array_add($month_pay,$data->TERM,$pay_month);
            }
            $info[$val->LOWPRINCIPAL] = $month_pay;
        }
        return $info;
    }


    /**
     * 进行试算，更新于
     * 2016-01-29
     * @param array $array
     * @return array
     * @throws \Exception
     */
    public function get_loan_message_by_api($array = array()){
        try {
            $api = new AnApi();
            $authModel = new AuthModel();
            $auth = $authModel->get_auth_info_by_user_id($array['user_id']);
            $rule = Rule::loan_shisuan_filter($array);
            if($rule['status']) {
                $info = $api->get_data_by_message($array['user_id'], $array['amount'], $array['period'], $array['city']);
                if ($info) {
                    if($auth->SubProductType == '1'){
                        $validator = Rule::loan_filter($info);
                    }else{
                        $validator = Rule::car_loan_filter($info);
                        $sex = $this->get_sex($auth->id_card);
                        $info['Sex'] = $sex;
                    }
                    if ($validator['status']) {
                        try {
                            $this->start_connect();
                            $array = array_add($array, 'EventName', $auth->EventName);
                            $array = array_add($array, 'EventID', $auth->EventID);
                            $array = array_add($array, 'id_card', $auth->id_card);
                            $array = array_add($array, 'real_name', $auth->real_name);
                            $array = array_add($array, 'CustomerID', $auth->CustomerID);
                            $loan_id = $this->when_exist_cancel_then_insert($array);
                            $array = array_add($array, 'loan_id', $loan_id);
                            $this->insert_update_loan_message($loan_id, $info);
                            $this->update_auth_status(Service::WRITE_PERIOSN, $array['user_id']);
                            $this->insert_update_bank($info, $array);
                            $this->init_loan_message($info, $array);
                            $this->commit();
                            return array('status' => true, 'message' => array('data' => '试算成功', 'loan_id' => $loan_id));
                        } catch (\Exception $e) {
                            $this->rollback();
                            throw $e;
                        }
                    } else {
                        return array('status' => false, 'message' => array('data' => '暂不支持线上申请，请联系400-998-7101前往佰仟金融门店办理'));
                    }
                } else {
                    Logger::info($auth->real_name . '-' . $auth->id_card . '-获取产品出现异常');
                    return array('status' => false, 'message' => array('data' => '系统繁忙，请重试'));
                }
            }else{
                return array('status'=>false,'message'=>array('data'=>$rule['data']['message']));
            }
        }catch (\Exception $e){
            Logger::info('试算发生异常，请查看');
           throw $e;
        }
    }


    /**
     * 新增一条新的订单信息
     * @param $array
     * @return bool
     * @throws \Exception
     */
    public function when_exist_cancel_then_insert($array){
        try {
            $array = array(
                'user_id' => $array['user_id'],
                'loan_amount' => $array['amount'],
                'status' => Service::WAIT_SUBMIT,
                'remark' => $array['remark'],
                'loan_period' => $array['period'],
                'created_at' => date('Y-m-d H:i:s', time()),
                'source' => $array['source'],
                'remark_descript' => $array['remark_descript'],
                'issure' => $array['issure'],
                'EventName' => $array['EventName'],
                'sa_id' => Config::get('myconfig.sa_id'),
            );
            $loanModel = new LoanModel();
            $loan_id = $loanModel->when_exist_cancel_then_insert($array);
            return $loan_id;
        }catch (\Exception $e){
            Logger::info('试算时更新订单信息出现异常');
            throw $e;
        }
    }

    /**
     * 新增或更新一条订单信息
     * @param $array
     * @return bool
     * @throws \Exception
     */
    public function insert_update_loan($array){
        try {
            $array = array(
                'user_id' => $array['user_id'],
                'loan_amount' => $array['amount'],
                'status' => Service::WAIT_SUBMIT,
                'remark' => $array['remark'],
                'loan_period' => $array['period'],
                'created_at' => date('Y-m-d H:i:s', time()),
                'source' => Service::wei_xin_source,
                'remark_descript' => $array['remark_descript'],
                'issure' => $array['issure'],
                'EventName' => $array['EventName'],
                'sa_id' => Config::get('myconfig.sa_id'),
            );
            $loanModel = new LoanModel();
            $loan_id = $loanModel->insert_or_update_loan($array);
            return $loan_id;
        }catch (\Exception $e){
            Logger::info('试算时更新订单信息出现异常');
            throw $e;
        }
    }

    /**
     * 更新或是新增一条订单的附加信息
     * @param $loan_id
     * @param $info
     * @throws \Exception
     */
    public function insert_update_loan_message($loan_id,$info){
        try {
            $loan_message = array(
                'loan_id' => $loan_id,
                'month_payment' => $info['MonthRepayment'],
                'payment_date' => $info['PayDate'],
                'month_interest' => $info['MonthlyInterestrate'],
                'first_payment' => $info['FirstDrawingDate'],
                'month_serve' => $info['CustomerServiceRates'],
                'month_manage' => $info['ManagementFeesrate'],
                'month_addint' => $info['AddServiceRates'],
                'first_payment_date' => $info['OriginalPutoutDate'],
                'stamptax' => $info['stamptax'],
                'created_at' => date('Y-m-d H:i:s', time()),
                'updated_at' => date('Y-m-d H:i:s', time()),
            );
            $loanMessageModel = new LoanMessageModel();
            $loanMessageModel->insert_or_update_loan_message($loan_message);
        }catch (\Exception $e){
            Logger::info('试算时更新订单额外信息出现异常');
            throw $e;
        }
    }

    /**
     * 更新用户认证表中的状态
     * @param $status
     * @param $user_id
     * @throws \Exception
     */
    public  function update_auth_status($status,$user_id){
        try {
            $authModel = new AuthModel();
            $authModel->update_auth_info_by_user_id(array('step_status' => $status, 'updated_at' => date('Y-m-d H:i:s', time())), $user_id);
            $info = Session::get('user_auth_'.$user_id,$authModel->get_auth_info_by_user_id($user_id));
            $info->step_status = $status;
            Session::put('user_auth_'.$user_id,$info);
        }catch (\Exception $e){
            Logger::info('试算时更新用户状态失败');
            throw $e;
        }
    }

    /**
     * 初始化订单的其他到信息到as开头的表中
     * @param $info
     * @param $array
     * @throws \Exception
     */
    public function init_loan_message($info,$array){
        try {
            $info = array_add($info, 'EventID', $array['EventID']);
            $info = array_add($info, 'EventName', $array['EventName']);
            $info = array_add($info, 'StoreCityCode', $array['city']);
            $info['TurnAccountNumber'] = $info['TurnAccountNumber'] . $array['CustomerID'];
            $order_result = new OrderResource($array['loan_id']);
            $order_result->init($info);
        }catch (\Exception $e){
            Logger::info('试算初始化出现错误');
            throw $e;
        }
    }

    /**
     *
     */

    public function get_sex($id_card){
        $idCard = trim($id_card);
        $val = substr($idCard,-2,1);
        $sex = ($val%2==0)?2:1;
        return $sex;
    }

    /**
     * 将试算返回银行卡信息保存
     * @param $info
     * @param $array
     * @throws \Exception
     */
    public function insert_update_bank($info,$array){
        try {
            $userModel = new UserModel();
            $user = $userModel->get_user_message_by_id($array['user_id']);
            $bank = array(
                'open_bank' => $info['RepaymentBankCode'],
                'open_bank_name' => $info['RepaymentBank'],
                'bank_card_no' => $info['RepaymentNo'],
                'user_id' => $array['user_id'],
                'mobile' => $user->mobile,
                'real_name' => $array['real_name'],
                'id_card' => $array['id_card'],
                'created_at' => date('Y-m-d H:i:s', time()),
                'updated_at' => date('Y-m-d H:i:s', time()),
            );
            $userBankNoModel = new UserBankNoModel();
            $userBankNoModel->insert_or_update_bank_no($bank);
        }catch (\Exception $e){
            Logger::info('试算时更新银行卡信息失败');
            throw $e;
        }
    }





    /**进入试算，获取相关参数
     * @param $user_id
     * @param $amount
     * @param $period
     * @param $city
     * @param $toubao
     * @param string $remark
     */
    public function get_loan_message_by_an_api($user_id,$amount,$period,$city,$toubao,$remark,$remark_descipt = '',$month_pay=''){
        $check = $this->check_loan_status($user_id);
        if($check){
            Logger::info($user_id.'进行重复提单,页面位于填写贷款页面');
            return array('status'=>false,'msg'=>'请勿重新提单','data'=>array('loan_id'=>''));
        }
        if(Session::has('amount_period') && Session::get('amount_period') == $amount.'-'.$period){
            Logger::info('期数:'.$period.'-'.'贷款金额：'.$amount.'-已存在');
            return array('status'=>true,'msg'=>'获取参数成功','data'=>array('loan_id'=>''));
        }else{
            $storeService = new StoreService();
            $authModel  = new AuthModel();
            $auth = $authModel->get_auth_info_by_user_id($user_id);
            if($auth->step_status==109){
                return array('status'=>"ca",'msg'=>'请进行合同确认');
            }
            $cityInfo = $storeService->get_userable_store_info($city);
            if($cityInfo){
                $api = new AnApi();
                $loanModel = new LoanModel();
                $loanMessageModel = new LoanMessageModel();
                $info = $api->get_data_by_message($user_id,$amount,$period,$city,$toubao);
                if($info){
                    if($month_pay){
                        $info['MonthRepayment']=$month_pay;
                    }
                    //验证必填字段
                    $rule = array(
                        'Sex' => 'required',
                        'CertType' => 'required',
                        'NativePlace' => 'required',
                        'Villagetown' => 'required',
                        'Street' => 'required',
                        'Community' => 'required',
                        'CellNo' => 'required',
                        'FamilyAdd' => 'required',
                        'EmployRecord' => 'required',
                        'HeadShip' => 'required',
                        'UnitKind' => 'required',
                        'CellProperty' => 'required',
                        'Flag3' => 'required',
                        'House' => 'required',
                        'HouseRent' => 'required',
                        'Flag10' => 'required',
                        'KinshipAdd' => 'required',
                        'EduExperience' => 'required',
                        'FamilyMonthIncome' => 'required',
                        'JobTime' => 'required',
                        'JobTotal' => 'required',
                        'OtherRevenue' => 'required',
                        'Severaltimes' => 'required',
                        'Falg4' => 'required',
                        'Contactrelation' => 'required',
                    );
                    $validator = Validator::make($info, $rule);
                    if(!$validator->passes()){
                        $message = $validator->messages();
                        $info_txt = "内容".$message->first();
                        $info_txt1 = "用户:".Auth::user()->mobile."-".Auth::user()->realname."-信息不完整，不符合贷款,指引到门店办理";
                        Logger::info($info_txt1);
                        Logger::info($info_txt);
                        return array('status'=>false,'msg'=>'暂不支持微信申请，请联系400-998-7101前往佰仟金融门店办理');
                    }

                    $this->start_connect();
                    $loan = array(
                        'user_id' => $user_id,
                        'loan_amount' => $amount,
                        'status'  => Service::WAIT_SUBMIT,
                        'remark'  => $remark,
                        'loan_period' => $period,
                        'created_at' => date('Y-m-d H:i:s',time()),
                        'source' => Service::wei_xin_source,
                        'remark_descript' => $remark_descipt,
                        'contact_time'=> '',
                        'issure' => $toubao,
                        'EventName'=>$auth->EventName,
                        'sa_id' => Config::get('myconfig.sa_id'),
                    );
                    $loan_id = $loanModel->insert_or_update_loan($loan);
                    $loan_message = array(
                        'loan_id' => $loan_id,
                        'month_payment' => $info['MonthRepayment'],
                        'payment_date' =>$info['PayDate'],
                        'month_interest' => $info['MonthlyInterestrate'],
                        'first_payment' => $info['FirstDrawingDate'],
                        'month_serve' => $info['CustomerServiceRates'],
                        'month_manage' =>$info['ManagementFeesrate'],
                        'month_addint' => $info['AddServiceRates'],
                        'first_payment_date' => $info['OriginalPutoutDate'],
                        'stamptax' => $info['stamptax'],
                        'created_at' => date('Y-m-d H:i:s',time()),
                        'updated_at' => date('Y-m-d H:i:s',time()),
                    );
                    $loan_message_flag = $loanMessageModel->insert_or_update_loan_message($loan_message);
                    $update_auth = $authModel->update_auth_info_by_id(array('step_status'=>Service::WRITE_PERIOSN,'updated_at'=>date('Y-m-d H:i:s',time())),$auth->id);
                    $info = array_add($info,'EventID',$auth->EventID);
                    $info = array_add($info,'EventName',$auth->EventName);
                    $info = array_add($info,'StoreCityCode',$city);
                    $info['TurnAccountNumber'] = $info['TurnAccountNumber'].$auth->CustomerID;
                    $bank = array(
                        'open_bank'=>$info['RepaymentBankCode'],
                        'open_bank_name'=>$info['RepaymentBank'],
                        'bank_card_no'=>$info['RepaymentNo'],
                        'user_id'=>$user_id,
                        'mobile'=>Auth::user()->mobile,
                        'real_name'=>$auth->real_name,
                        'id_card'=>$auth->id_card,
                        'created_at' =>date('Y-m-d H:i:s',time()),
                        'updated_at' =>date('Y-m-d H:i:s',time()),
                    );
                    $userBankNoModel = new UserBankNoModel();
                    $bank_flag = $userBankNoModel->insert_or_update_bank_no($bank);
                    $order_result = new OrderResource($loan_id);

                    $init = $order_result->init($info);
                    $flag = $this->end_connect(array($init,$loan_message_flag,$loan_id,$update_auth));
                    if($flag == true){
                        Logger::info('---------'.$auth->real_name.'-'.$auth->id_card.'-获取参数成功'.'---------');
                        Session::put('amount_period',$amount.'-'.$period);
                        return array('status'=>true,'msg'=>'获取参数成功','data'=>array('loan_id'=>$loan_id));
                    }else{
                        Logger::error('---------'.$auth->real_name.'-'.$auth->id_card.'-获取参数出现异常'.'---------');
                        Logger::info('---------'.$auth->real_name.'-'.$auth->id_card.'-获取参数出现异常'.'---------');
                        return array('status'=>false,'msg'=>'系统繁忙，请重试');
                    }
                }else{
                    Logger::info($auth->real_name.'-'.$auth->id_card.'-获取产品出现异常');
                    Logger::error($auth->real_name.'-'.$auth->id_card.'-获取产品出现异常');
                    return array('status'=>false,'msg'=>'系统繁忙，请重试');
                }
            }else{
                Logger::error($auth->real_name.'-'.$auth->id_card.'-暂未开放该城市申请');
                return array('status'=>false,'msg'=>'暂未开放该城市申请');
            }
        }
    }


    /*
     * 查看或下载pdf合同
     */
    public function get_loanpdf($oid, $param)
    {
        $loanm = new LoanModel();
        $pdfService = new PdfService();
        $check_loan = $loanm->get_uid_oid_loan(Auth::id(), $oid);
    //    $html = file_get_contents($check_loan->pact_url);
        $html = base64_decode(FileReader::read_storage_text_file($check_loan->pact_url));
        $pdfService->put_content_to_pdf($html,'借钱么借款协议');
    }

    /**
     * 检查是否可以重复提单
     */
    public function check_loan_is_over($loan_id,$user_id){
        $authModel = new AuthModel();
        $data = $authModel->get_auth_info_by_user_id($user_id);
        $info = $authModel->get_auth_info_list_by_id_card($data->id_card);
        $flag = true;
        foreach($info as $val){
            if($val->step_status == AuthModel::STEP_STATUS_LOAN_SYS){
                $flag = false;
                break;
            }
        }
        if(!$flag){
            return array('status'=>false,'message'=>'请勿使用'.$data->id_card.'重复申请！');
        }
        return array('status'=>true,'message'=>'没有提过单');
    }

    /**
     * 对重复提单的订单进行取消操作
     */
    public function revert_loan_to_over($loan_id,$user_id){
        $loanModel = new LoanModel();
        $authModel = new AuthModel();
        Logger::info($user_id.'-'.$loan_id.'-重复提单');
        $this->start_connect();
        $loan_flag = $loanModel->update_loan_by_id(array('status'=>'100','reason'=>'订单已过期'),$loan_id);
        $auth_flag = $authModel->update_auth_info_by_user_id(array('step_status'=>AuthModel::STEP_STATUS_LOAN_RE),$user_id);
        return $this->end_connect(array($loan_flag,$auth_flag));
    }

    /**
     * 为个人资料页面提供数据
     * @param $user_id
     * @param int $loan_id
     * @return array
     */
    public function get_loan_person_info($user_id,$loan_id = 0){
        $loan_status = $this->loan->get_order_entry($user_id,$loan_id);
        $array = array();
        if($loan_status['status']){
            $loan_id = $loan_status['data'];
            $authModel = new AuthModel();
            $userModel = new UserModel();
            $user = $userModel->get_user_message_by_id($user_id);
            $auth = $authModel->get_auth_info_by_user_id($user_id);

            $family = AsFamilyMessageModel::where($loan_id)->first();
            $custom = AsCustombaseMessageMobel::where($loan_id)->first();
            $message = AsRepaymentMessage::where($loan_id)->first();
            $income = AsIncomeMessageModel::where($loan_id)->first();

            $array = array_add($array,'Marriage',$family->Marriage);
            $array = array_add($array,'UserMobile',$user->mobile);
            $array = array_add($array,'SpouseName',$family->SpouseName);
            $array = array_add($array,'SpouseTel',$family->SpouseTel);
            $array = array_add($array,'KinshipName',$family->KinshipName);
            $array = array_add($array,'KinshipTel',$family->KinshipTel);
            $array = array_add($array,'Childrentotal',$family->Childrentotal);
            $array = array_add($array,'RelativeType',$family->RelativeType);
            $array = array_add($array,'OtherTelephone',$family->OtherTelephone);
            $array = array_add($array,'MaturityDate',$custom->MaturityDate);
            $array = array_add($array,'Flag2',$custom->Flag2);
            $array = array_add($array,'FamilyAdd',$custom->FamilyAdd);
            $array = array_add($array,'Countryside',$custom->Countryside);
            $array = array_add($array,'Villagecenter',$custom->Villagecenter);
            $array = array_add($array,'Plot',$custom->Plot);
            $array = array_add($array,'Room',$custom->Room);
            $array = array_add($array,'City',$message->City);
            $array = array_add($array,'OpenBranch',$message->OpenBranch);
            $array = array_add($array,'OpenBank',$message->OpenBank);
            $array = array_add($array,'ReplaceAccount',$message->ReplaceAccount);
            $array = array_add($array,'OrderId',$loan_id['OrderId']);
            $array = array_add($array,'realname',$auth->real_name);
            $array = array_add($array,'ContactTel',$income->ContactTel);

            $array = array_add($array,'OtherContact',$income->OtherContact);
            $array = array_add($array,'SelfName',Auth::user()->realname);

            return array('status'=>true,'message'=>array('data'=>$array));
        }else{
            return array('status'=>false,'message'=>array('data'=>'暂未查询到相关订单'));
        }
    }

    /**
     * 更新订单的个人资料信息
     * @param $array
     * @param int $loan_id
     * @return array
     * @throws \Exception
     */
    public function update_person_info($array,$loan_id = 0)
    {
        try {
            $info = Rule::person_info_filter($array);
            if ($info['status']) {
                $info = $info['data']['message'];
                $bankService = new BankService();
                if(App::environment('product')){
                    $result = $bankService->bank_auth($info);
                }else{
                    if(config('extension.bank_auth')){
                        $result = $bankService->bank_auth($info);
                    }else{
                        $result['status'] = true;
                    }
                }
                if (!$result['status']) {
                    return array('status'=>false, 'message'=>array('data'=>$result['message']['data']));
                } else {
                    $res_info = $this->deal_person_info($info,$loan_id,$result['status']);
                    if($res_info['status']){
                        return array('status'=>true,'message'=>array('data'=>$res_info['message']['data']));
                    }else{
                        return array('status'=>false,'message'=>array('data'=>$res_info['message']['data']));
                    }
                }
            } else {
                return array('status'=>false, 'message'=>array('data'=>$info['data']['message']));
            }
        }catch(\Exception $e){
            Logger::info('--------'.$array['user_id'].'提交个人资料出现异常--------');
            throw $e;
        }
    }

    public function deal_person_info($array,$loan_id,$status){
        try{
      //      $loan_status = $this->loan->get_order_entry($array['user_id'],$loan_id);
      //      $loan_id = $loan_status['data'];
            $loan_status = $this->check_loan_status_is_deal($array['user_id'],$loan_id);
            if($loan_status['status']){
                $this->start_connect();
                $loan_entry = $loan_status['message']['data'];
                $loan_id = $loan_entry['data'];
                $family = AsFamilyMessageModel::firstOrCreate($loan_id);
                $custom = AsCustombaseMessageMobel::firstOrCreate($loan_id);
                $message = AsRepaymentMessage::firstOrCreate($loan_id);
                $user_bank_card = new UserBankCardModel();
                $auth_user = new AuthModel();
                $family->wxUpdate($array);
                $custom->wxUpdate($array);
                $message->wxUpdate($array);
                $auth_user->update_auth_info_by_user_id(array('step_status'=>AuthModel::STEP_STATUS_FIRM_INFO),$array['user_id']);
                if($status == 'true'){
                    $array = array_add($array,'check_status',UserBankCardModel::CONFIRM_PASS);
                }else{
                    $array = array_add($array,'check_status',UserBankCardModel::NOT_CONFIRM);
                }
                $authMessage = $auth_user->get_auth_info_by_user_id($array['user_id']);
                $array = array_add($array,'real_name',$authMessage->real_name);
                $array = array_add($array,'id_card',$authMessage->id_card);
                $user_bank_card->insert_user_bank_card($array);
                $this->commit();
                return array('status'=>true,'message'=>array('data'=>'个人资料更新成功'));
            }else{
                return array('status'=>false,'message'=>array('data'=>'请勿重复提交'));
            }
        }catch(\Exception $e){
            $this->rollback();
            throw $e;
        }
    }

    /**
     * 获取用户单位资料信息
     * @param $user_id
     * @param int $loan_id
     * @return array
     * @throws \Exception
     */
    public function get_firm_info($user_id,$loan_id=0)
    {
        try {
            //$loan_status = $this->loan->get_order_entry($user_id, $loan_id);
            $loan_status = $this->check_loan_status_is_deal($user_id,$loan_id);
            if($loan_status['status']) {
                $loan_info = $loan_status['message']['data'];
                $loan_id = $loan_info['data'];

                $work = AsWordMessageModel::where($loan_id)->first();
                $income = AsIncomeMessageModel::where($loan_id)->first();
                $add = AsCommAddModel::where($loan_id)->first();
                $family = AsFamilyMessageModel::where($loan_id)->first();

                $array = array();
                $array = array_add($array,'WorkCorp',$work->WorkCorp);
                $array = array_add($array,'WorkAdd',$work->WorkAdd);
                $array = array_add($array,'UnitCountryside',$work->UnitCountryside);
                $array = array_add($array,'UnitStreet',$work->UnitStreet);
                $array = array_add($array,'UnitRoom',$work->UnitRoom);
                $array = array_add($array,'UnitNo',$work->UnitNo);
                $array = array_add($array,'WorkTel',$add->WorkTel);
                $array = array_add($array,'SelfMonthIncome',$income->SelfMonthIncome);
                $array = array_add($array,'OtherContact',$income->OtherContact);
                $array = array_add($array,'ContactTel',$income->ContactTel);
                $array = array_add($array,'Flag8',$add->Flag8);
                $array = array_add($array,'OrderId',$loan_id['OrderId']);
                $array = array_add($array,'KinshipTel',$family->KinshipTel);
                //zl  07-30 +姓名验证
                $array = array_add($array,'KinshipName',$family->KinshipName);
                $array = array_add($array,'SpouseName',$family->SpouseName);
                $array = array_add($array,'SelfName',Auth::user()->realname);
                $array = array_add($array,'Marriage',$family->Marriage);

                $array = array_add($array,'SpouseTel',$family->SpouseTel);
                $array = array_add($array,'SPOUSEWORKTEL',$family->SPOUSEWORKTEL);
                $array = array_add($array,'OtherTelephone',$family->OtherTelephone);
                return array('status'=>true,'message'=>array('data'=>$array));
            }else{
                return array('status'=>false,'message'=>array('data'=>'没有需要进行填写单位资料的信息'));
            }
        }catch(\Exception $e){
            Logger::info($user_id.'获取单位资料发生异常');
            throw $e;
        }

    }

    /**
     * 进行单位资料填写提交
     * @param $array
     * @param int $loan_id
     * @return array
     */
    public function update_firm_info_message($array,$loan_id=null){
        try {
            $info = Rule::firm_info_filter($array);
            if ($info['status']) {
                if (!$this->check_firm_info_is_allow($array['WorkAdd'])) {
                    return array('status' => false, 'message' => array('data' => '工作所在城市暂不支持申请'));
                } else {
                    $this->deal_firm_info($array,$loan_id);
                    return array('status'=>true,'message'=>array('data'=>'单位资料更新成功'));
                }
            } else {
                return array('status' => false, 'message' => array('data' => $info['data']['message']));
            }
        }catch(\Exception $e){
            Logger::info('--------'.$array['user_id'].'提交单位资料出现异常------------');
            throw $e;
        }
    }

    /**
     * 对单位资料提交内容进行不可以办单信息过滤
     * @param $city
     * @return mixed
     */
    public function check_firm_info_is_allow($city)
    {
        $syncStoreInfoModel = new SyncStoreInfoModel();
        $info = $syncStoreInfoModel->get_store_info_by_city($city);
        return $info;
    }

    /**
     * 对单位资料内容进行保存操作
     * @param $array
     * @param $loan_id
     * @return array
     * @throws \Exception
     */
    public function deal_firm_info($array,$loan_id){
 //       $loan_status = $this->loan->get_order_entry($array['user_id'],$loan_id);
//        $loan_id = $loan_status['data'];
        try{
            $loan_status = $this->check_loan_status_is_deal($array['user_id'],$loan_id);
            if($loan_status['status']){
                $this->start_connect();
                $loan_message = $loan_status['message']['data'];
                $loan_id = $loan_message['data'];
                $work = AsWordMessageModel::firstOrCreate($loan_id);
                $income = AsIncomeMessageModel::firstOrCreate($loan_id);
                $add = AsCommAddModel::firstOrCreate($loan_id);
                $work->wxUpdate($array);
                $income->wxUpdate($array);
                $add->wxUpdate($array);
                $authModel = new AuthModel();
                $authModel->update_auth_info_by_user_id(array('step_status'=>AuthModel::STEP_STATUS_UPLOAD_IMAGE),$array['user_id']);
                $this->commit();
                return array('status'=>true,'message'=>array('data'=>'单位资料更新成功'));
            }else{
                return array('status'=>false,'message'=>array('data'=>'没有需要进行单位资料填写的订单'));
            }
        }catch(\Exception $e){
            $this->rollback();
            throw $e;
        }
    }

    /**
     * 保存客户图片到本地服务器
     * 2016-02-19
     * @param $array
     * @param null $loan_id
     * @return array
     * @throws \Exception
     */
    public function update_file_picture($array,$loan_id=null){
        try {
            $info = Rule::file_picture_filter($array);
            if($info['status']){
                $loan_status = $this->check_loan_status_is_deal($array['user_id'],$loan_id);
                if($loan_status['status']){
                    $this->start_connect();
                    $loan_message = $loan_status['message']['data'];
                    $loan_id = $loan_message['data'];
                    $authModel = new AuthModel();
                    $pic = AsCustomPicModel::firstOrCreate($loan_id);
                    $pic->wxUpdate($array);
                    //$authModel->update_auth_info_by_user_id(array('step_status' => AuthModel::STEP_STATUS_BANK_PASS),$array['user_id']);
                    $authModel->update_auth_info_by_user_id(array('step_status' => AuthModel::STEP_STATUS_CA_AUTH),$array['user_id']);
                    $this->commit();
                    return array('status'=>true,'message'=>array('data'=>'图片上传成功'));
                }else{
                    return array('status'=>false,'message'=>array('data'=>'图片上传失败'));
                }
            }else{
                return array('status'=>false,'message'=>array('data'=>$info['message']['data']));
            }

        }catch(\Exception $e){
            $this->rollback();
            Logger::info('--------'.$array['user_id'].'提交图片上传出现异常------------');
            throw $e;
        }
    }

    /**
     * 进行提单操作
     * 2016-02-19
     * @param $loan_id
     * @return array
     */
    public function commit_loan_to_an_system($loan_id){
        $orderResource = new OrderResource($loan_id);
        $response = $orderResource->check_loan_message_is_complete();
        if($response['status']){
            $array = $response['message']['data'];
            $res = (new Asapi())->submit_loan($array);
            $pic_response = $this->deal_loan_submit_response($res,$array['InputUserID'],$loan_id);
            return $pic_response;
        }else{
            return $response;
        }
    }

    public function deal_loan_submit_response($response,$sa_id,$loan_id){
        if(isset($response['RequestStatus']) && ($response['RequestStatus']=='1')){
            $result = $this->deal_loan_picture_message($loan_id,$sa_id,$response['SerialNo']);
            if($result['status']){
                return array('status'=>false,'message'=>array('data'=>$result['message']['data']));
            }else{
                $picture_response = $this->upload_loan_picture_to_system($result['message']['data']);
                if($picture_response['status']){
                    Logger::info('------------'.$loan_id.'：订单提单成功'.'---------------');
                    return array('status'=>true,'message'=>array('data'=>'提单成功','serial_no'=>$response['SerialNo']));
                }else{
                    Logger::info('------------'.$loan_id.'：订单提单过程中上传图片失败'.'---------------');
                    return array('status'=>false,'message'=>array('data'=>'提交订单失败，请重试'));
                }
            }
        }else{
            return array('status'=>false,'message'=>array('data'=>'通信故障，无法提交！'));
        }
    }

    public function deal_loan_picture_message($loan_id,$sa_id,$serial_no){
        $formData = AsCustomPicModel::tryCheck($loan_id);
        if($formData instanceof ResourceErrorModel){
            return array('status'=>false,'message'=>array('data'=>$formData->data()));
        }else{
            $formData = array(
                'objType'=>'Business',
                'objNo'=>$serial_no,
                'userId'=>$sa_id,
                'orgId'=>15,
                'image'=>$formData
            );
            return array('status'=>true,'message'=>array('data'=>$formData));
        }
    }

    /**
     * 通过接口将图片上传到安硕系统
     * 2016-02-19
     * @param $array
     * @return array
     */
    public function upload_loan_picture_to_system($array){
        $response = (new Asapi())->uploadImageFile($array);
        if(isset($response['data']) && ($response['data'][0]['Status']=='Success')){
            return array('status'=>true,'message'=>array('data'=>'图片上传成功'));
        }else{
            Logger::info('----------------图片上失败------------------');
            return array('status'=>false,'message'=>array('data'=>'图片上传失败'));
        }
    }

    /**
     *查看用户是否可以对待提交的订单操作
     */
    public function check_loan_status_is_deal($user_id,$loan_id=0){
        $info = $this->loan->get_order_entry($user_id,$loan_id);
        if($info['status']){
            $result = $info['entry'];
            if(isset($result->status) && $result->status == '011'){
                return array('status'=>true,'message'=>array('data'=>$info));
            }else{
                return array('status'=>false,'message'=>array('data'=>'没有待提交订单需要处理'));
            }
        }else{
            return array('status'=>false,'message'=>array('data'=>'没有待提交订单需要处理'));
        }
    }

    public function get_customer_pic($user_id,$loan_id=0){
        $loan_status = $this->loan->get_order_entry($user_id,$loan_id);
        if($loan_status['status']){
            $loan_id = $loan_status['data'];
            $pic = AsCustomPicModel::firstOrCreate($loan_id);
            return array('status'=>true,'message'=>array('data'=>$pic));
        }else{
            return array('status'=>false,'message'=>array('data'=>'没有需要上传照片的订单'));
        }
    }


    public function get_loan_by_loan_id($loan_id){
        $loanModel = new LoanModel();
        $info = $loanModel->get_loan_by_id($loan_id);
        if($info){
            return array('status'=>true,'message'=>array('data'=>$info));
        }else{
            return array('status'=>false,'message'=>array('data'=>'没有相关的订单'));
        }
    }

    public function cancel_order($user_id){
        $loanModel = new LoanModel();
        $loanModel->update_loan_unsubmit_status_by_user_id($user_id,array('status'=>'100'));
        $authModel = new AuthModel();
        $authModel->update_auth_info_by_user_id(array('step_status'=>'200'),$user_id);
    }


    /**
     * 供定时任务撤销订单，
     * 条件  080状态，approve_time   72小时之前
     *
     * 没有批量撤销接口~~就一个个发送吧，后期改
     */
    public function cancelLoans(){
        $loan_model = new LoanModel();
        $loans = $loan_model->getUnVivoLoans();

        $sysApi = new SysApi();
        $auth_model = new AuthModel();

        $array_loanids = array();
        foreach($loans as $loan){
            $response = $sysApi->loan_revoke($loan->pact_number,'jieqianmedsm');
            if ($response['data'][0]['STATUS'] == 'Fail') {
                Logger::error('批量撤销订单中  ' . $loan->pact_number . '失败'.$response['data'][0]['Message']);
                print_r($loan->pact_number . '失败'.$response['data'][0]['Message'].'</br>');
            } else {
                $array = array();
                $array = array_add($array, 'revoke_reason', date('Y-m-d') . '活体超时');
                $array = array_add($array, 'revoke_remark', date('Y-m-d') . '活体超时');
                $array = array_add($array, 'revoke_time', date('Y-m-d H:i:s', time()));
                $array = array_add($array, 'status', Service::LOAN_CEXIAO);
                $affect = $loan_model->update_loan_by_id($array, $loan->id);
                if ($affect) {
                    $auth_model->update_auth_info_by_user_id(array('step_status' => Service::RE_LOAN), $loan->user_id);
                }

                $array_loanids[] = $loan->id;
            }

        }

        Logger::info('未活体合同自动撤销结束！已成功撤销：'.json_encode($array_loanids));
        print_r('未活体合同自动撤销结束！已成功撤销：'.json_encode($array_loanids));
    }

}