<?php
namespace App\Service\admin\sale;

use App\Api\api\AnApi;
use App\Log\Facades\Logger;
use App\Model\Admin\AdminLoanModel;
use App\Model\Base\AuthModel;
use App\Model\Base\LoanModel;
use App\Service\admin\Service;
use App\Service\admin\UserService;

use App\Service\base\AuthService;
use App\Util\AdminAuth;
use App\Util\AdminRule;
use Illuminate\Support\Facades\Cache;

class AdminSaleService extends Service
{
    public function update_auth($array){
        $rule = AdminRule::admin_auth($array);
        if($rule['status']){
            Logger::info('--------------------电销录单--------------------','DxOperator');
            Logger::info('进行了实名认证，提交内容为：CustomerName='.$array['real_name'].'&CertID='.$array['id_card'],'DxOperator');

            $array = $rule['data']['message'];
            $anapi = new AnApi();
            $info = $anapi->get_customer_message($array['real_name'], $array['id_card']);
            if($info){
                $userService = new UserService();
                $userStatus = $userService->get_user_message($array['mobile'],$array['id_card'],$array['real_name']);
                if($userStatus['status']){
                    //过滤不能办单的客户
                    $authService = new AuthService();
                    $authJudge = $authService->filter_auth_user($info);
                    if(!$authJudge['status']){
                        return array('status'=>false,'data'=>array('message'=>'暂不支持线上办理！'));
                    }

                    $user = $userStatus['data']['entry'];
                    $info = array_add($info, 'created_at', date('Y-m-d H:i:s', time()));
                    $info = array_add($info, 'updated_at', date('Y-m-d H:i:s', time()));
                    $info = array_add($info, 'step_status', '101');
                    $info = array_add($info, 'user_id', $user->id);
                    $auth = new AuthModel();
                    $auth_flag = $auth->get_auth_info_by_user_id($user->id);
                    if($auth_flag){
                        if($auth_flag->real_name == $array['real_name'] && $auth_flag->id_card == $array['id_card']){
                            $auth->update_auth_info_by_user_id($info,$user->id);
                            $userService->update_user_message_by_user_id($user->id,array('realname'=>$array['real_name']));
                            return array('status'=>true,'data'=>array('message'=>'实名认证成功','user_id'=>$user->id));
                        }else{
                            return array('status'=>false,'data'=>array('message'=>'该手机号码已绑定过其它身份信息','user_id'=>$user->id));
                        }
                    }else{
                        $auth->insert_auth_info($info);
                        $userService->update_user_message_by_user_id($user->id,array('realname'=>$array['real_name']));
                        return array('status'=>true,'data'=>array('message'=>'实名认证成功','user_id'=>$user->id));
                    }
            //        $auth->insert_or_update_auth_info($info,$user->id);
           //         return array('status'=>true,'data'=>array('message'=>'实名认证成功','user_id'=>$user->id));
                }else{
                    return array('status'=>false,'data'=>array('message'=>'系统异常，请重试'));
                }
            }else{
                Logger::info('-----'.$array['real_name'].'-'.$array['id_card'].'实名认证返回信息为空，不符合货款资格或认证出现异常-----','DxOperator');
                return array('status'=>false,'data'=>array('message'=>'暂不符合贷款资格'));
            }
        }else{
            return array('status'=>false,'data'=>array('message'=>$rule['data']['message']));
        }
    }

    public function check_auth($array){
        $rule = AdminRule::admin_auth_rule($array);
        if ($rule['status']){
            $array = $rule['data']['message'];
            $anapi = new AnApi();
            $info = $anapi->get_customer_message($array['real_name'], $array['id_card']);
            if($info){
                //检测手机
                if(empty($array['mobile']) && empty($array['tel']) && empty($array['newmobile'])){
                    return array('status'=>false,'data'=>array('message'=>'手机号码不能为空'));
                }else{
                    if(!empty($array['mobile'])){
                        $mobile = $array['mobile'];
                    }elseif(!empty($array['tel']) && $array['tel'] != 1){
                        $mobile = $array['tel'];
                    }else{
                        $mobile = $array['newmobile'];
                    }
                    $userService = new UserService();
                    $userStatus = $userService->get_user_message($mobile,$array['id_card'],$array['real_name']);
                    if($userStatus['status']){
                        $user = $userStatus['data']['entry'];
                        $info = array_add($info, 'created_at', date('Y-m-d H:i:s', time()));
                        $info = array_add($info, 'updated_at', date('Y-m-d H:i:s', time()));
                        $info = array_add($info, 'step_status', '101');
                        $info = array_add($info, 'user_id', $user->id);
                        $auth = new AuthModel();
                        $auth_flag = $auth->get_auth_info_by_user_id($user->id);
                        if($auth_flag){
                            if($auth_flag->real_name == $array['real_name'] && $auth_flag->id_card == $array['id_card']){
                                //检测是否有过订单、订单状态
                                $loanModel = new LoanModel();
                                $loanInfo = $loanModel->get_loan_newest($user->id);
                                if($loanInfo){
                                    if ($loanInfo->status == '011' || $loanInfo->status =='070'){
                                        return array('status'=>true,'data'=>array('message'=>'订单待授权或审核中','OrderId'=>$loanInfo->id));
                                    }else{
                                        $auth->update_auth_info_by_user_id($info,$user->id);
                                        return array('status'=>true,'data'=>array('message'=>'继续录单','user_id'=>$user->id));
                                    }
                                }else{
                                    $auth->update_auth_info_by_user_id($info,$user->id);
                                    return array('status'=>true,'data'=>array('message'=>'暂未申请','user_id'=>$user->id));
                                }
                                //$auth->update_auth_info_by_user_id($info,$user->id);
                                //$userService->update_user_message_by_user_id($user->id,array('realname'=>$array['real_name']));
                                //return array('status'=>true,'data'=>array('message'=>'实名认证成功','user_id'=>$user->id));
                            }else{
                                return array('status'=>false,'data'=>array('message'=>'该手机号码已绑定过其它身份信息','user_id'=>$user->id));
                            }
                        }else{
                            $auth->insert_auth_info($info);
                            $userService->update_user_message_by_user_id($user->id,array('realname'=>$array['real_name']));
                            return array('status'=>true,'data'=>array('message'=>'实名认证成功','user_id'=>$user->id));
                        }
                    }else{
                        return array('status'=>false,'data'=>array('message'=>'系统异常，请重试'));
                    }
                }
            }else{
                return array('status'=>false,'data'=>array('message'=>'暂不符合贷款资格'));
            }
        }else{
            return array('status'=>false,'data'=>array('message'=>$rule['data']['message']));
        }
    }


}