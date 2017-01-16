<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/5/12
 * Time: 13:51
 */

namespace App\Service\admin\sale;

use App\Log\Facades\Logger;
use App\Model\Admin\AdminLoanModel;
use App\Model\Base\AsBaseInformationModel;
use App\Model\Base\AsCommAddModel;
use App\Model\Base\AsCustombaseMessageMobel;
use App\Model\Base\AsFamilyMessageModel;
use App\Model\Base\AsIncomeMessageModel;
use App\Model\Base\AsInsideMessageModel;
use App\Model\Base\AsRepaymentMessage;
use App\Model\Base\AsWordMessageModel;
use App\Model\Base\AuthModel;
use App\Model\Base\LoanModel;
use App\Model\Base\LoanSceneModel;
use App\Model\Base\ResourceErrorModel;
use App\Model\Base\SyncModel;
use App\Model\Base\UserModel;
use App\Service\admin\Service;
use App\Service\api\LoanApiService;
use App\Service\base\AuthService;
use App\Service\base\CommAddService;
use App\Service\base\CustomBaseMessageService;
use App\Service\base\FamilyMessageService;
use App\Service\base\IncomeMessageService;
use App\Service\base\LoanService;
use App\Service\base\RepaymentMessageService;
use App\Service\base\WorkMessageService;
use App\Service\mobile\CenterService;
use App\Util\AdminRule;
use App\Util\DxOperator;

class AdminLoanService extends Service
{

    public function get_fill_out($user_id){
        $loanModel = new LoanModel();
        $loan = $loanModel->get_loan_newest_un_submit($user_id);
        if($loan){
            $loanModel->update_loan_by_id(array('status'=>'100','reason'=>'电销录单，重新试算，取消上一笔订单'.date('Y-m-d H:i:s',time())),$loan->id);
        }
        $loanApiService = new LoanApiService();
        $info = $loanApiService->get_fillout_loan_info($user_id);
        return $info;
    }

    public function post_fill_out($array){
        $loanApiService = new LoanApiService();
        $info = $loanApiService->post_fillout_loan_info($array);
        if($info['status']){
            $sale_array['admin_id']=$array['admin_id'];
            $sale_array['loan_id'] = $info['message']['loan_id'];
            $sale_array['created_at'] = date('Y-m-d H:i:s',time());
            $sale_array['updated_at'] = date('Y-m-d H:i:s',time());
            $adminLoanModel = new AdminLoanModel();
            $get = $adminLoanModel->get_admin_loan_by_admin_id_and_loan_id($sale_array['loan_id'],$sale_array['admin_id']);
            if(!$get){
                $adminLoanModel->add_admin_loan($sale_array);
            }else{
                $update_array['updated_at'] = date('Y-m-d H:i:s',time());
                $adminLoanModel->update_admin_loan_by_id($get->id,$update_array);
            }
        }
        return $info;
    }

    public function get_base_message($user_id){
        $customBaseMessageService = new CustomBaseMessageService();
        $customer_base_message = $customBaseMessageService->get_custom_base_message($user_id);
        return $customer_base_message;
    }

    public function update_base_message($array){
        $rule = AdminRule::custom_base_message_filter($array);
        if($rule['status']){
            $customBaseMessageService = new CustomBaseMessageService();
            $array = $rule['data']['message'];
            $custom_base_message = $customBaseMessageService->update_custom_base_message($array);
            return $custom_base_message;
        }else{
            return array('status'=>false,'data'=>array('message'=>$rule['data']['message']));
        }

    }

    public function get_comm_add_message($user_id){
        $commAddMessageService = new CommAddService();
        $comm_add_message = $commAddMessageService->get_comm_add_message($user_id);
        return $comm_add_message;
    }

    public function update_comm_add_message($array){
        $rule = AdminRule::comm_add_message_filter($array);
        if($rule['status']){
            $commAddMessageService = new CommAddService();
            $array = $rule['data']['message'];
            $comm_add_message = $commAddMessageService->update_comm_add_message($array);
            return $comm_add_message;
        }else{
            return array('status'=>false,'data'=>array('message'=>$rule['data']['message']));
        }

    }

    public function get_family_message($user_id){
        $familyMessageService = new FamilyMessageService();
        $family_message = $familyMessageService->get_family_message($user_id);
        return $family_message;
    }

    public function update_family_message($array){
        $rule = AdminRule::family_message_filter($array);
        if($rule['status']){
            $familyMessageService = new FamilyMessageService();
            $array = $rule['data']['message'];
            $family_message = $familyMessageService->update_family_message($array);
            return $family_message;
        }else{
            return array('status'=>false,'data'=>array('message'=>$rule['data']['message']));
        }
    }

    public function get_income_message($user_id){
        $incomeMessageService = new IncomeMessageService();
        $income_message = $incomeMessageService->get_income_message($user_id);
        return $income_message;
    }

    public function update_income_message($array){
        $rule = AdminRule::income_message_filter($array);
        if($rule['status']){
            $incomeMessageService = new IncomeMessageService();
            $array = $rule['data']['message'];
            $income_message = $incomeMessageService->update_income_message($array);
            return $income_message;
        }else{
            return array('status'=>false,'data'=>array('message'=>$rule['data']['message']));
        }

    }

    public function get_repayment_message($user_id){
        $repaymentMessageService = new RepaymentMessageService();
        $repayment_message = $repaymentMessageService->get_repayment_message($user_id);
        return $repayment_message;
    }

    public function update_repayment_message($array){
        $rule = AdminRule::repayment_message_filter($array);
        if($rule['status']){
            $repaymentMessageService = new RepaymentMessageService();
            $array = $rule['data']['message'];
            //重组银行卡验证数据
            $userModel = new UserModel();
            $userInfo = $userModel->get_user_message_by_id($array['user_id']);
            $array ['mobileno'] = $userInfo->mobile;
            $array ['bankcardno'] = $array ['ReplaceAccount'];
            $array ['itemno'] = $array ['OpenBank'];
            $array ['bank_card_name'] = SyncModel::bankCodeName($array ['OpenBank']);
            $repayment_message = $repaymentMessageService->auth_and_update_repayment_message($array);
            return $repayment_message;
        }else{
            return array('status'=>false,'data'=>array('message'=>$rule['data']['message']));
        }
    }

    public function get_work_message($user_id){
        $workMessageService = new WorkMessageService();
        $work_message = $workMessageService->get_work_message($user_id);
        return $work_message;
    }

    public function update_work_message($array){
        $rule = AdminRule::work_message_filter($array);
        if($rule['status']){
            $workMessageService = new WorkMessageService();
            $array = $rule['data']['message'];
            $work_message = $workMessageService->update_work_message($array);
            return $work_message;
        }else{
            return array('status'=>false,'data'=>array('message'=>$rule['data']['message']));
        }
    }

    public function complete($admin_id,$order_id,$array){
        $message = $this->check_customer_message_complete($order_id);
        if($message['status']){
            $loanService = new LoanService();
            $loan = $loanService->get_loan_by_loan_id($order_id);
            if($loan['status']){
                $loan = $loan['message']['data'];
                $authService = new AuthService();
                $authService->update_auth_status($loan->user_id,AuthModel::STEP_STATUS_UPLOAD_IMAGE);
                //更新录单表
                $adminLoanModel = new AdminLoanModel();
                $adminLoanModel->update_admin_loan_by_admin_id_loan_id($admin_id,$order_id,array('updated_at'=>date('Y-m-d H:i:s',time())));
                //提交场景内容
                $loanSceneModel = new LoanSceneModel();
                $scene_array = $loanSceneModel->get_loan_scene_by_loan_id($order_id);

                $new_array = array();
                $new_array = array_add($new_array,'place',$array['place']);
                $new_array = array_add($new_array,'doing',$array['doing']);
                $new_array = array_add($new_array,'music',$array['music']);
                $new_array = array_add($new_array,'reaction',$array['reaction']);
                $new_array = array_add($new_array,'interest',$array['interest']);
                $new_array = array_add($new_array,'updated_at',date('Y-m-d H:i:s',time()));
                if($scene_array){
                    $loanSceneModel->update_loan_scene_by_loan_id($order_id,$new_array);
                }else{
                    $new_array = array_add($new_array,'admin_id',$admin_id);
                    $new_array = array_add($new_array,'loan_id',$order_id);
                    $new_array = array_add($new_array,'created_at',date('Y-m-d H:i:s',time()));
                    $loanSceneModel->add_loan_scene($new_array);
                }
                Logger::info('订单：'.$order_id.'录单完成，准备发送短信、微信通知。','DxOperator');
                //发送短信、微信模板
                DxOperator::$send = true;   //开启发送短信、微信
                $centerService = new CenterService();
                $centerService->send_auth_loan($order_id);
                return array('status'=>true,'data'=>array('message'=>'录单完毕'));
            }else{
                return array('status'=>false,'data'=>array('message'=>$loan['message']['data']));
            }
        }else{
            return array('status'=>false,'data'=>array('message'=>$message['data']['message']));
        }
    }

    public function check_customer_message_complete($loan_id){
        $base = AsBaseInformationModel::tryCheck($loan_id);
        if($base instanceof ResourceErrorModel){
            return array('status'=>false,'data'=>array('message'=>'基本信息有必填项为空'));
        }

        $repay = AsRepaymentMessage::tryCheck($loan_id);
        if($repay instanceof ResourceErrorModel){
            return array('status'=>false,'data'=>array('message'=>'银行还款信息有必填项为空'));
        }

        $inside = AsInsideMessageModel::tryCheck($loan_id);
        if($inside instanceof ResourceErrorModel){
            return array('status'=>false,'data'=>array('message'=>'门店信息有必填项为空'));
        }

        $custom = AsCustombaseMessageMobel::tryCheck($loan_id);
        if($custom instanceof ResourceErrorModel){
            return array('status'=>false,'data'=>array('message'=>'客户基本信息有必填项为空'));
        }


        $word = AsWordMessageModel::tryCheck($loan_id);
        if($word instanceof ResourceErrorModel){
            return array('status'=>false,'data'=>array('message'=>'工作单位信息有必填项为空'));
        }


        $comm = AsCommAddModel::tryCheck($loan_id);
        if($comm instanceof ResourceErrorModel){
            return array('status'=>false,'data'=>array('message'=>'邮寄信息有必填项为空'));
        }


        $family = AsFamilyMessageModel::tryCheck($loan_id);
        if($family instanceof ResourceErrorModel){
            return array('status'=>false,'data'=>array('message'=>'家庭信息有必填项为空'));
        }


        $income = AsIncomeMessageModel::tryCheck($loan_id);
        if($income instanceof ResourceErrorModel){
            return array('status'=>false,'data'=>array('message'=>'收入信息有必填项为空'));
        }

        return array('status'=>true,'data'=>array('message'=>'信息全部填写完整'));

    }


}