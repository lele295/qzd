<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/3/15
 * Time: 11:03
 */

namespace App\Service\api;


use App\Log\Facades\Logger;
use App\Service\base\AuthService;
use App\Service\base\CommAddService;
use App\Service\base\CustomBaseMessageService;
use App\Service\base\DocumentService;
use App\Service\base\FamilyMessageService;
use App\Service\base\IncomeMessageService;
use App\Service\base\LoanAfterService;
use App\Service\base\LoanBeforeService;
use App\Service\base\LoanService;
use App\Service\base\RepaymentMessageService;
use App\Service\base\UserService;
use App\Service\base\WorkMessageService;
use App\Service\car\LoanCarService;
use App\Service\mobile\Service;
use App\Util\CodeLibrary;
use App\Util\FileReader;
use App\Util\Loan;
use App\Util\Rule;
use Illuminate\Support\Facades\Log;

class LoanCarApiService extends Service
{

    /**
     * 为试算提供数据
     * @param $user_id
     * @return array
     */
    public function get_fill_out_message($user_id){
        $loanService  = new LoanService();
        $check = $loanService->get_user_is_can_loan_message($user_id);
        if($check['status']){
            $loanCarService = new LoanCarService();
            $info = $loanCarService->get_car_loan_amount_product($user_id);
            $default = $loanService->get_wain_sumbit_nextest_loan($user_id);
            if($info['status']){
                return array('status'=>true,'data'=>array('message'=>$info['data']['message'],'default'=>$default));
            }else{
                return array('status'=>false,'data'=>array('message'=>$info['data']['message'],'default'=>''));
            }
        }else{
            return array('status'=>false,'data'=>array('message'=>$check['message']['data'],'default'=>''));
        }
    }

    /*
     * 调用接口进行试算
     * */
    public function post_fill_out_message($user_id,$array){
        $array = array_add($array,'user_id',$user_id);
        $loanCarService = new LoanCarService();
        $info = $loanCarService->post_fill_out_amount($array);
        if($info['status']){
            //zl 0801  这个普通PC录单不走啊~~
//            $loan_id = $info['data']['loan_id'];
//            $loanBeforeService = new LoanBeforeService();
//            $loanBeforeService->init_loan_before($user_id,$loan_id);
//            $loanAfterService = new LoanAfterService();
//            $loanAfterService->init_loan_after($user_id,$loan_id);
            return array('status'=>true,'data'=>array('message'=>$info['data']['message']));
        }else{
            return array('status'=>false,'data'=>array('message'=>$info['data']['message']));
        }
    }

    public function get_car_user_message($user_id){
        $authService = new AuthService();
        $auth = $authService->get_auth_by_user_id($user_id);
        if($auth){
            return array('status'=>true,'data'=>array('message'=>'获取用户信息成功','entry'=>$auth));
        }else{
            return array('status'=>false,'data'=>array('message'=>'请先完成实名认证'));
        }
    }


    public function get_comm_add_message($user_id,$loan_id=0){
        $commAddService = new CommAddService();
        $info = $commAddService->get_comm_add_message($user_id,$loan_id);
        if($info['status']){
            return array('status'=>true,'data'=>array('message'=>$info['data']['message'],'entry'=>$info['data']['entry']));
        }else{
            return array('status'=>false,'data'=>array('message'=>$info['data']['message']));
        }
    }

    public function update_comm_add_message($user_id,$array){
        $rule_filter = Rule::comm_add_message_filter($array);
        if($rule_filter['status']){

            $commAddService = new CommAddService();
            $input_array = $rule_filter['data']['message'];
            $input_array = array_add($input_array,'user_id',$user_id);
            $input_array['WorkTel'] = $input_array['area_code'].'-'.$input_array['WorkTel'];
            $info = $commAddService->update_comm_add_message($input_array);
            if($info['status']){
                return array('status'=>true,'data'=>array('message'=>$info['data']['message']));
            }else{
                return array('status'=>false,'data'=>array('message'=>$info['data']['message']));
            }
        }else{
            return array('status'=>false,'data'=>array('message'=>$rule_filter['data']['message']));
        }
    }

    public function get_custom_base_message($user_id,$loan_id=0){
        $customBaseMessageService = new CustomBaseMessageService();
        $info = $customBaseMessageService->get_custom_base_message($user_id,$loan_id);
        if($info['status']){
            return array('status'=>true,'data'=>array('message'=>$info['data']['message'],'entry'=>$info['data']['entry']));
        }else{
            return array('status'=>false,'data'=>array('message'=>$info['data']['message']));
        }
    }

    public function get_custom_base_message_v2($user_id,$loan_id=0){
        $loanCarService = new LoanCarService();
        $info = $loanCarService->get_customer_message($user_id,$loan_id);
        if($info['status']){
            return array('status'=>true,'data'=>array('message'=>$info['data']['message'],'entry'=>$info['data']['entry']));
        }else{
            return array('status'=>false,'data'=>array('message'=>$info['data']['message'],'entry'=>''));
        }
    }

    public function update_custom_base_message($user_id,$array){
        $rule_filter = Rule::custom_base_message_filter($array);
        if($rule_filter['status']){
            $customBaseMessageService = new CustomBaseMessageService();
            $input_array = $rule_filter['data']['message'];
            $input_array = array_add($input_array,'user_id',$user_id);
            $info = $customBaseMessageService->update_custom_base_message($input_array);
            if($info['status']){
                return array('status'=>true,'data'=>array('message'=>$info['data']['message']));
            }else{
                return array('status'=>false,'data'=>array('message'=>$info['data']['message']));
            }
        }else{
            return array('status'=>false,'data'=>array('message'=>$rule_filter['data']['message']));
        }
    }

    public function update_custom_base_message_v2($user_id,$array){
        $rule_filter = Rule::custom_base_message_filter($array);
        if($rule_filter['status']){
            $bank_rule_filter = Rule::repayment_message_filter($array);
            if($bank_rule_filter['status']){
                $authService = new AuthService();
                $userService = new UserService();
                $auth = $authService->get_auth_by_user_id($user_id);
                $user = $userService->get_user_message_by_user_id($user_id);
                $input_array = $rule_filter['data']['message'];
                $input_array = array_add($input_array,'user_id',$user_id);

                $input_array = array_add($input_array,'mobileno',$user->mobile);
                $input_array = array_add($input_array,'itemno',$input_array['OpenBank']);
                $input_array = array_add($input_array,'bankcardno',$input_array['ReplaceAccount']);
                $input_array = array_add($input_array,'real_name',$auth->real_name);
                $input_array = array_add($input_array,'bank_card_name',CodeLibrary::get_bank_branch_name_by_code($input_array['ReplaceAccount']));

                $loanCarServer = new LoanCarService();
                $info = $loanCarServer->update_custom_base_message($input_array);
                if($info['status']){
                    return array('status'=>true,'data'=>array('message'=>$info['data']['message']));
                }else{
                    return array('status'=>false,'data'=>array('message'=>$info['data']['message']));
                }
            }else{
                return array('status'=>false,'data'=>array('message'=>$bank_rule_filter['data']['message']));
            }
        }else{
            return array('status'=>false,'data'=>array('message'=>$rule_filter['data']['message']));
        }
    }



    public function get_family_message($user_id,$loan_id=0){
        $familyMessageService = new FamilyMessageService();
        $info = $familyMessageService->get_family_message($user_id,$loan_id);
        if($info['status']){
            return array('status'=>true,'data'=>array('message'=>$info['data']['message'],'entry'=>$info['data']['entry']));
        }else{
            return array('status'=>false,'data'=>array('message'=>$info['data']['message'],'entry'=>''));
        }
    }

    public function get_family_message_v2($user_id,$loan_id=0){
        $loanCarService = new LoanCarService();
        $info = $loanCarService->get_family_message($user_id,$loan_id);
        if($info['status']){
            return array('status'=>true,'data'=>array('message'=>$info['data']['message'],'entry'=>$info['data']['entry']));
        }else{
            return array('status'=>false,'data'=>array('message'=>$info['data']['message'],'entry'=>''));
        }
    }

    public function update_family_message_v2($user_id,$array){
        $filter_array = Rule::family_message_filter($array);
        if($filter_array['status']){
            $email_array_filter = Rule::comm_add_message_filter($array);
            if($email_array_filter['status']){
                $input_array = $filter_array['data']['message'];
                $input_array = array_add($input_array,'user_id',$user_id);
                $loanCarService = new LoanCarService();
                $family = $loanCarService->update_family_message($input_array);
                if($family['status']){
                    return array('status'=>true,'data'=>array('message'=>$family['data']['message']));
                }else{
                    return array('status'=>false,'data'=>array('message'=>$family['data']['message']));
                }
            }else{
                return array('status'=>false,'data'=>array('message'=>$email_array_filter['data']['message']));
            }
        }else{
            return array('status'=>false,'data'=>array('message'=>$filter_array['data']['message']));
        }
    }

    public function update_family_message($user_id,$array){
        $filter_array = Rule::family_message_filter($array);
        if($filter_array['status']){
            $familyMessageService = new FamilyMessageService();
            $input_array = $filter_array['data']['message'];
            $input_array = array_add($input_array,'user_id',$user_id);
            $family = $familyMessageService->update_family_message($input_array);
            if($family['status']){
                return array('status'=>true,'data'=>array('message'=>$family['data']['message']));
            }else{
                return array('status'=>false,'data'=>array('message'=>$family['data']['message']));
            }
        }else{
            return array('status'=>false,'data'=>array('message'=>$filter_array['data']['message']));
        }
    }

    public function get_income_message($user_id,$loan_id=0){
        $incomeMessageService = new IncomeMessageService();
        $info = $incomeMessageService->get_income_message($user_id,$loan_id);
        if($info['status']){
            return array('status'=>true,'data'=>array('message'=>$info['data']['message'],'entry'=>$info['data']['entry']));
        }else{
            return array('status'=>false,'data'=>array('message'=>$info['data']['message'],'entry'=>''));
        }
    }

    public function update_income_message($user_id,$array){
        $rule_filter = Rule::income_message_filter($array);
        if($rule_filter['status']){
            $incomeMessageService = new IncomeMessageService();
            $input_array = $rule_filter['data']['message'];
            $input_array = array_add($input_array,'user_id',$user_id);
            $info = $incomeMessageService->update_income_message($input_array);
            if($info['status']){
                return array('status'=>true,'data'=>array('message'=>$info['data']['message']));
            }else{
                return array('status'=>false,'data'=>array('message'=>$info['data']['message']));
            }
        }else{
            return array('status'=>false,'data'=>array('message'=>$rule_filter['data']['message']));
        }
    }

    public function get_repayment_message($user_id,$loan_id=0){
        $repaymentMessageService = new RepaymentMessageService();
        $info= $repaymentMessageService->get_repayment_message($user_id,$loan_id);
        if($info['status']){
            $authService = new AuthService();
            $auth = $authService->get_auth_by_user_id($user_id);
            return array('status'=>true,'data'=>array('message'=>$info['data']['message'],'entry'=>$info['data']['entry'],'auth'=>$auth));
        }else{
            return array('status'=>false,'data'=>array('message'=>$info['data']['message']));
        }
    }

    public function update_repayment_message($user_id,$array){
        $rule_array = Rule::repayment_message_filter($array);
        if($rule_array['status']){
            $repaymentMessageService = new RepaymentMessageService();
            $input_array = $rule_array['data']['message'];
            $input_array = array_add($input_array,'user_id',$user_id);
            $info = $repaymentMessageService->auth_and_update_repayment_message($input_array);
            if($info['status']){
                return array('status'=>true,'data'=>array('message'=>$info['data']['message']));
            }else{
                return array('status'=>false,'data'=>array('message'=>$info['data']['message']));
            }
        }else{
            return array('status'=>false,'data'=>array('message'=>$rule_array['data']['message']));
        }
    }

    public function get_work_message($user_id,$loan_id=0){
        $workMessageService = new WorkMessageService();
        $info = $workMessageService->get_work_message($user_id,$loan_id);
        if($info['status']){
            return array('status'=>true,'data'=>array('message'=>$info['data']['message'],'entry'=>$info['data']['entry']));
        }else{
            return array('status'=>false,'data'=>array('message'=>$info['data']['message']));
        }
    }

    public function update_work_message($user_id,$array){
        $rule_filter = Rule::work_message_filter($array);
        if($rule_filter['status']){
            $workMessageService = new WorkMessageService();
            $input_array = $rule_filter['data']['message'];
            $input_array = array_add($input_array,'user_id',$user_id);
            $info = $workMessageService->update_work_message($input_array);
            if($info['status']){
                return array('status'=>true,'data'=>array('message'=>$info['data']['message']));
            }else{
                return array('status'=>false,'data'=>array('message'=>$info['data']['message']));
            }
        }else{
            return array('status'=>false,'data'=>array('message'=>$rule_filter['data']['message']));
        }
    }

    public function get_ca_auth($user_id){
        $documentService = new DocumentService();
        $documentService->get_new_loan_deal($user_id);
        $check = Loan::check_loan_input_is_finish($user_id);
        if($check){
            $loan = Loan::get_order_entry($user_id);
            $view = FileReader::read_storage_text_file($loan['entry']->pact_url);
            return array('status'=>true,'data'=>array('message'=>'可以进入到CA页面','entry'=>$view));
        }else{
            return array('status'=>false,'data'=>array('message'=>'请先填写完相关信息','entry'=>''));
        }
    }


}