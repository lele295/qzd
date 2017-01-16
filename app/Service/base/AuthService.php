<?php

namespace App\Service\base;



use App\Api\api\AnApi;
use App\Api\api\SysApi;
use App\Log\Facades\Logger;
use App\Model\Admin\CityLimitModel;
use App\Model\Base\AsUserAuthModel;
use App\Model\Base\AuthModel;
use App\Model\Base\LoanModel;
use App\Model\Base\UserModel;
use App\Service\Exception\ExceptionService;
use App\Service\mobile\Service;
use App\User;
use App\Util\SwitchFlag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Psy\Exception\ErrorException;

class AuthService extends Service
{
    public function __construct(){

    }


    public function get_auth_by_user_id($user_id){
        $authModel = new AuthModel();
        $info = $authModel->get_auth_info_by_user_id($user_id);
        return $info;
    }

    public function get_auth_by_id_card($id_card,$real_name){
        $authModel = new AuthModel();
        $info = $authModel->get_auth_message_by_id_card_real_name($id_card,$real_name);
        return $info;
    }

    /**
     * 实名认证信息
     * 2016-01-21
     * @param $real_name
     * @param $id_card
     */
    public function add_auth_user_message($real_name,$id_card,$user_message){
        try {
            $userModel = new UserModel();
            $userModel->update_user_info_by_id($user_message['user_id'],array('mark'=>$real_name.':'.$id_card.','));
            $anapi = new AnApi();
            $info = $anapi->get_customer_message($real_name, $id_card);
            if($info) {
                $user = $this->filter_auth_user($info);   //过滤不能办单的特殊用户
                if($user['status']){
                    try {
                        $this->start_connect();
                        $auth = new AuthModel();
                        $info = array_add($info, 'created_at', date('Y-m-d H:i:s', time()));
                        $info = array_add($info, 'updated_at', date('Y-m-d H:i:s', time()));
                        $info = array_add($info, 'user_id', $user_message['user_id']);
                        $userModel->update_user_info_by_id($user_message['user_id'],array('realname' => $real_name,'updated_at' => date('Y-m-d H:i:s', time()),'group'=>'1'));
                        $auth->insert_or_update_auth_info($info,$user_message['user_id']);
                        $this->commit();
                        return array('status'=>true,'message'=>array('data'=>'实名认证成功','auth'=>$info));
                    }catch (\Exception $e){
                        Logger::info($real_name.'-'.$id_card.'-实名认证在数据库操作时出现异常');
                        $this->rollback();
                        throw $e;
                    }
                }else{
                    return array('status'=>false,'message'=>array('data'=>$user['data']['message']));
                   // return $user;
                }
            } else {
                $anApi = new AnApi();
                $user_group = $anApi->get_user_is_baiqian($id_card,$user_message['mobile']);
                if($user_group){
                    $userModel->update_user_info_by_id($user_message['user_id'],array('group'=>'2'));
                }
                Logger::info($real_name.'-'.$id_card.':'.'不符合贷款资格');
                return array('status'=>false,'message'=>array('data'=>'您不是佰仟客户，暂不能申请本产品。'));
            }
        }catch (\Exception $e){
            ExceptionService::exception($e);
        }
    }

    public function update_auth_session($user_id){
        $authModel = new AuthModel();
        $auth = $authModel->get_auth_info_by_user_id($user_id);
        Session::put('user_auth',$auth);
    }

    /**
     * 更新用户的实名信息
     * 2016-01-21
     * @param $real_name
     * @param $id_card
     * @return array
     * @throws \Exception
     */
    public function update_auth_user_message($real_name,$id_card){
        try {
            $anapi = new AnApi();
            $info = $anapi->get_customer_message($real_name, $id_card);
            if ($info) {
                $user = $this->filter_auth_user($info);
                if($user['status']){
                    try {
                        $this->start_connect();
                        $auth = new AuthModel();
                        $info = array_add($info, 'updated_at', date('Y-m-d H:i:s', time()));
                        $auth->update_auth_info_by_user_id($info,Auth::id());
                        $this->commit();
                        return array('status'=>true,'data'=>array('message'=>'贷款信息更新成功'));
                    }catch (\Exception $e){
                        Logger::info($real_name.'-'.$id_card.'-实名认证在更新数据库操作时出现异常');
                        $this->rollback();
                        throw $e;
                    }
                }else{
                    return $user;
                }
            } else {
                return array('status'=>false,'data'=>array('message'=>'感谢支持！您的申请暂未通过，烦请过段时间再来申请！'));
            }
        }catch (\Exception $e){
            ExceptionService::exception($e);
        }
    }

    /**
     * 过滤不能办单条件的客户
     */
    public function filter_auth_user($auth){
        if($auth){
            if(empty($auth['FamilyAdd']) && $auth['SubProductType'] === '1'){
                Logger::info($auth['real_name'].'-'.$auth['id_card'].'-该客户的家庭信息为空，不能通过认证','an');
                return array('status'=>false,'data'=>array('message'=>'抱歉，暂不支持微信办理！请拨打400-998-7101前往门店办理'));
            }
            $city_limit_m = new CityLimitModel();
            $check_limit = $city_limit_m->sel_code_city(SwitchFlag::$_auth_city?SwitchFlag::$_auth_city:$auth['WorkAdd']);
            if($check_limit){
                Logger::info($auth['real_name'].'-'.$auth['id_card'].'-'.(SwitchFlag::$_auth_city?SwitchFlag::$_auth_city:$auth['WorkAdd']).'-该客户工作地址所在城市不能办单','city_black_list');
                return array('status'=>false,'data'=>array('message'=>'抱歉，您所在的城市暂不支持微信办理！请拨打400-998-7101前往门店办理'));
            }
            return array('status'=>true,'data'=>array('message'=>'可以进行办单操作'));
        }
        throw new \Exception;
    }


    //认证用户信息
    public function auth_user_message($real_name,$id_card,$flag = true){
        $anapi = new AnApi();
        $info = $anapi->get_custom_status($real_name,$id_card);
        if($info){
            if($info == "limit_city"){
                return array('status'=>false,'msg'=>'抱歉，您所在的城市暂不支持微信办理！请拨打400-998-7101前往门店办理');
            }elseif($flag == true && $info['SubProductType'] == 3){
                Logger::info($real_name.'-'.$id_card.':'.'非交叉现金货用户');
                return array('status'=>false,'msg'=>'感谢支持！您的申请暂未通过，烦请过段时间再来申请！');
            }else{
                $this->start_connect();
                $user = new UserModel();
                $auth = new AuthModel();
                $data = $user->update_user_info_by_id(Auth::id(), array('realname'=>$real_name,'updated_at'=>date('Y-m-d H:i:s',time()),'group'=>'1','mark'=>$real_name.':'.$id_card.','));
                $info = array_add($info,'created_at',date('Y-m-d H:i:s',time()));
                $info = array_add($info,'updated_at',date('Y-m-d H:i:s',time()));
                $flag = $auth->insert_or_update_auth_info($info,Auth::id());
                $connect = $this->end_connect(array($data,$flag));
                if($connect){
                    Logger::info($real_name.'-'.$id_card.':'.'用户认证成功');
                    return array('status'=>true,'msg'=>'用户认证成功');
                }else{
                    Logger::info($real_name.'-'.$id_card.':'.'用户认证失败');
                    return array('status'=>false,'msg'=>'用户认证失败');
                }
            }
        }else{
            $anApi = new AnApi();
            $user_group = $anApi->get_user_is_baiqian($id_card,Auth::user()->mobile);
            if($user_group){
                $user = new UserModel();
                $user->update_user_info_by_id(Auth::id(),array('group'=>'2',$real_name.':'.$id_card.','));
            }
            Logger::info($real_name.'-'.$id_card.':'.'不符合贷款资格');
            return array('status'=>false,'msg'=>'感谢支持！您的申请暂未通过，烦请过段时间再来申请！');
        }
    }
    //更新用户认证信息
    public function update_auth_info($real_name,$id_card){
        $anapi = new AnApi();
        $info = $anapi->get_custom_status($real_name,$id_card);
        if($info){
            $auth = new AuthModel();
            $data = $auth->insert_or_update_auth_info($id_card,Auth::id());
            if($data){
                Logger::info($real_name.'-'.$id_card.':'.'用户认证信息更新成功');
                return array('status'=>true,'msg'=>'用户认证信息更新成功');
            }else{
                Logger::error($real_name.'-'.$id_card.':'.'用户认证信息更新失败');
                return array('status'=>false,'msg'=>'用户认证信息更新失败');
            }
        }else{
            Logger::error($real_name.'-'.$id_card.':'.'不符合贷款资格,在更新用户认证信息时发现');
            return array('status'=>false,'msg'=>'感谢支持！您的申请暂未通过，烦请过段时间再来申请！');
        }
    }


    public function get_auth_message_by_user_id($user_id){
        $info = $this->check_auth_is_over($user_id);
         if($info){
            return true;
        }else{
            $loanModel = new LoanModel();
            $authModel = new AuthModel();
            $auth = $authModel->get_auth_info_by_user_id($user_id);
            $auth_result = $this->update_auth_user_message($auth->real_name,$auth->id_card);
             if(!$auth_result['status']){
                 return false;
             }else{
                 $loan = $loanModel->get_loan_newest_un_submit($user_id);
                 if($loan){
                     Logger::info($user_id.'订单已过期');
                     $this->start_connect();
                     $loan_flag = $loanModel->update_loan_by_id(array('status'=>'100','reason'=>'订单已过期'),$loan->id);
                     $auth_flag = $authModel->update_auth_info_by_user_id(array('step_status'=>AuthModel::STEP_STATUS_LOAN_RE),$user_id);
                     $this->end_connect(array($loan_flag,$auth_flag));
                 }
                 return true;
             }
        }
    }
    //判断用户订单是否已超过24小时
    public function get_auth_loan_message_by_user_id($user_id){
        try {
            $authModel = new AuthModel();
            $userModel = new UserModel();
            $loanModel = new LoanModel();
            $user_message = $userModel->get_user_message_by_id($user_id);
            $last_date = strtotime('-1 days', time());
            $login_date = strtotime($user_message->updated_at);
            if ($login_date < $last_date) {
                $loan = $loanModel->get_loan_newest_un_submit_list($user_id);
                foreach ($loan as $val) {
                    $this->start_connect();
                    $loan_flag = $loanModel->update_loan_by_id(array('status' => '100', 'reason' => '订单24小时没有提交，系统自动取消' . date('Y-m-d H:i:s', time())), $val->id);
                    $auth_flag = $authModel->update_auth_info_by_user_id(array('step_status' => AuthModel::STEP_STATUS_LOAN_RE), $user_id);
                    $flag = $this->end_connect(array($loan_flag, $auth_flag));
                    if ($flag) {
                        Logger::info('-------订单id为：' . $val->id . '超过24小时，系统自动取消，取消时间为' . date('Y-m-d H:i:s', time()) . '-------');
                    } else {
                        Logger::info('-------订单id为：' . $val->id . '超过24小时，系统自动失败，取消时间为' . date('Y-m-d H:i:s', time()) . '-------');
                    }
                }
            }
            return true;
        }catch (\Exception $e){

        }
    }



    public function check_auth_is_over($user_id){
        $authModel = new AuthModel();
        $auth = $authModel->get_auth_info_by_user_id($user_id);
        if(!$auth){
            return true;
        }
        $apiService = new ApiService();
        $asUser = $apiService->get_cust_auth_message($auth->real_name,$auth->id_card);
        $eventData =strtotime($auth->EventDate);
        $date = date('Y-m-d',time());
        if($asUser && trim($asUser['EventName']) == trim($auth->EventName) && $eventData >= strtotime($date)){
            return true;
        }else{
            return false;
        }
        /*
        $authModel = new AuthModel();
        $asUserAuthModel = new AsUserAuthModel();
        $auth = $authModel->get_auth_info_by_user_id($user_id);
        if(!$auth){
            return true;
        }

        $asUser = $asUserAuthModel->get_auth_cust_by_id_card_and_real_name($auth->real_name,$auth->id_card);
        $eventData =strtotime($auth->EventDate);
        $date = date('Y-m-d',time());
        if($asUser && trim($asUser->EVENTNAME) == trim($auth->EventName) && $eventData >= strtotime($date)){
            return true;
        }else{
            return false;
        }
        */
    }

    public function update_auth_status($user_id,$status){
        $authModel = new AuthModel();
        $info = $authModel->update_auth_info_by_user_id(array('step_status'=>$status),$user_id);
        return $info;
    }

    public function AppPush(){
        $authModel = new AuthModel();
        $users = $authModel->get_wait_push();

        $loanModel = new LoanModel();
        $has_loans = $loanModel->getInLoans($users);

        $not_has_loans = array();
        foreach($users as $user_id){
            if(in_array($user_id,$has_loans)){
                $not_has_loans[] = $user_id;
            }
        }

        return $has_loans;
    }

}