<?php
namespace App\Service\api;


use App\Commands\SetGroupWechat;
use App\Http\Requests\Request;
use App\Log\Facades\Logger;
use App\Model\Base\AuthModel;
use App\Model\Base\LoanModel;
use App\Model\Base\SyncModel;
use App\Model\mobile\WechatModel;
use App\Service\base\AuthService;
use App\Service\base\ConnectTimeService;
use App\Service\base\DocumentService;
use App\Service\base\LoanAfterService;
use App\Service\base\LoanBeforeService;
use App\Service\base\LoanService;
use App\Service\mobile\LoanScheduleService;
use App\Service\mobile\Service;
use App\Util\FileReader;
use App\Util\Loan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Queue;

class LoanApiService extends Service{


    public function __construct(){

    }

    /**
     * 获取试算进行试算的相关产品
     */
    public function get_fillout_loan_info($user_id){
        Logger::info($user_id.'-进入了试算调用');
        $loanService = new LoanService();
    //    $check = $loanService->get_user_is_can_loan_message($user_id);
        $check = $loanService->get_user_is_can_loan_message($user_id);
        if($check['status']){
            $default = $loanService->get_wain_sumbit_nextest_loan($user_id);
            $info = $loanService->get_loan_amount_product($user_id);
            Logger::info($user_id,'fillout',$info);
            if($info['status']){
                return array('status'=>true,'message'=>array('data'=>$info['message']['data'],'default'=>$default));
            }else{
                return array('status'=>false,'message'=>array('data'=>$info['message']['data'],'default'=>''));
            }
        }else{
            return array('status'=>false,'message'=>array('data'=>$check['message']['data'],'default'=>''));
        }
    }

    /**
     * 提交试算提交
     * @param $array
     * @return array
     * @throws \Exception
     */
    public function post_fillout_loan_info($array){
        Logger::info($array['user_id'].'-进行了试算提交调用，提交内容为：');
        Logger::info($array);
        $authModel = new AuthModel();
        if(!$array['remark_descript']){
            return array('status'=>false,'message'=>array('data'=>"请填写详细描述",'url'=>'','loan_id'=>''));
        }
//        $auth = $authModel->get_auth_info_by_user_id($array['user_id']);
//        if($auth->step_status == 109){
//            return array('status'=>false,'message'=>array('data'=>'请进行合同确认','url'=>'/loan/protocol-info','loan_id'=>''));
//        }
        $loanService = new LoanService();
        $info = $loanService->get_loan_message_by_api($array);
        if($info['status']){
     //       $loan_id = $info['message']['loan_id'];
     //       $loanBeforeService = new LoanBeforeService();
     //       $loanBeforeService->init_loan_before($array['user_id'],$loan_id);
     //       $loanAfterService = new LoanAfterService();
     //       $loanAfterService->init_loan_after($array['user_id'],$loan_id);
            return array('status'=>true,'message'=>array('data'=>$info['message']['data'],'url'=>'/loan/person-info/'.$info['message']['loan_id'],'loan_id'=>$info['message']['loan_id']));
        }else{
            return array('status'=>false,'message'=>array('data'=>$info['message']['data'],'url'=>'','loan_id'=>''));
        }
    }

    /**
     * 获用户的个人资料页面
     * @param $user_id
     * @param int $loan_id
     * @return array
     */
    public function get_person_info($user_id,$loan_id=0)
    {
        Logger::info($user_id.'-进入到了个人资料');
        $loanService = new LoanService();
        $info =$loanService->get_loan_person_info($user_id,$loan_id);
        if($info['status']){
            return array('status'=>true,'message'=>array('data'=>$info['message']['data']));
        }else{
            return array('status'=>false,'message'=>array('data'=>$info['message']['data']));
        }
    }

    /**
     * 对提交的个人资料进行更新操作
     * @param $array
     * @param int $loan_id
     * @return array
     * @throws \Exception
     */
    public function  post_person_info($array,$loan_id=0){
        Logger::info($array['user_id'].'-进行了个人资料的更新操作，更新内容为：');
        Logger::info($array);
        $loanService = new LoanService();
        $check_info = $loanService->check_loan_status_is_deal($array['user_id'],$loan_id);
        if($check_info['status']){
            $info = $loanService->update_person_info($array,$loan_id);
            if($info['status']){
                return array('status'=>true,'message'=>array('data'=>$info['message']['data']));
            }else{
                Logger::info('-------提交个人资料出现错误------');
                Logger::info($info);
                return array('status'=>false,'message'=>array('data'=>$info['message']['data']));
            }
        }else{
            Logger::info('-------提交个人资料出现错误------');
            Logger::info($check_info);
            return array('status'=>false,'message'=>array('data'=>'您已提交，请关闭'));
        }
    }

    /**
     * 为个人单位资料提供数据
     * @param $user_id
     * @param int $loan_id
     * @return array
     * @throws \Exception
     */
    public function get_firm_info($user_id,$loan_id=0){
        Logger::info($user_id.'-进行到个人单位资料功能');
        $loanService = new LoanService();
        $info = $loanService->get_firm_info($user_id,$loan_id);
        if($info['status']){
            return array('status'=>true,'message'=>array('data'=>$info['message']['data']));
        }else{
            return array('status'=>false,'message'=>array('data'=>$info['message']['data']));
        }
    }

    /**
     * 提交单位信息资料
     * @param $array
     * @param int $loan_id
     * @return array
     * @throws \Exception
     */
    public function post_firm_info($array,$loan_id=0){
        Logger::info($array['user_id'].'-进行单位信息资料的提交');
        Logger::info($array);
        $loanService = new LoanService();
        $check_info = $loanService->check_loan_status_is_deal($array['user_id'],$loan_id);
        if($check_info['status']){
            $loanService = new LoanService();
            $info = $loanService->update_firm_info_message($array,$loan_id);
            if($info['status']){
                return array('status'=>true,'message'=>array('data'=>$info['message']['data']));
            }else{
                Logger::info('-------提交单位信息资料出现错误------');
                Logger::info($info);
                return array('status'=>false,'message'=>array('data'=>$info['message']['data']));
            }
        }else{
            Logger::info('-------提交单位信息资料出现错误------');
            Logger::info($check_info);
            return array('status'=>false,'message'=>array('data'=>'没有需要进行单位信息提交的订单'));
        }

    }

    /**
     * 获取用户订单的相关照片信息
     * @param $user_id
     * @param int $loan_id
     * @return array
     * @throws \Exception
     */
    public function get_customer_pic($user_id,$loan_id=0){
        $loanService = new LoanService();
        $info = $loanService->get_customer_pic($user_id,$loan_id);
        $pic_array = array(
            'cert_face_pic'=>'',
            'cert_opposite_pic'=>'',
            'custom_pic'=>''
        );
        if($info['status']){
            $pic = $info['message']['data'];
            if($pic){
                if($pic->cert_face_pic){
                    $pic_array['cert_face_pic'] = FileReader::read_storage_image_resize_file($pic->cert_face_pic);
                }
                if($pic->cert_opposite_pic){
                    $pic_array['cert_opposite_pic'] = FileReader::read_storage_image_resize_file($pic->cert_opposite_pic);
                }
                if($pic->custom_pic){
                    $pic_array['custom_pic'] = FileReader::read_storage_image_resize_file($pic->custom_pic);
                }
            }
            return array('status'=>true,'message'=>array('data'=>'获取返回照片成功','entry'=>$pic_array));
        }else{
            return array('status'=>false,'message'=>array('data'=>$info['message']['data']));
        }
    }

    public function post_firm_pic_info($array,$loan_id=0){
        Logger::info($array['user_id'].'-进行了上传照片操作');
        $loanService = new LoanService();
        $info = $loanService->update_file_picture($array,$loan_id);
        if($info['status']){
            return array('status'=>true,'message'=>array('data'=>$info['message']['data']));
        }else{
            return array('status'=>false,'message'=>array('data'=>$info['message']['data']));
        }
    }





    /*
     *pc申请信息
     */
    public function pc_get_apply($user_id){
        $auth_m = new AuthModel();
        $auth = $auth_m->get_auth_info_by_user_id($user_id);
        $loan_m = new LoanModel();
        $loans = $loan_m->get_loan_by_user_id($user_id);

        $loan = $loans?$loans[0]:false;
        if($loan && $loan->status != 100 && $loan->status != 210 && $loan->status != '011'){
            $data['loans'] = $loans;
        }else{
            if($auth) {
                if($auth->step_status == 109){
                    header('Location:/pc/loan/ca-auth');die();
                }
                $data = $this->pc_trial_amount($user_id);
            }
            if($loan && $loan->status == '011'){
                $person_arr = $this->get_person_info($user_id);
                if($person_arr['status']){
                    $data['person_info'] = $person_arr['message']['data'];
                    $data['selectArray'] = array(
                        'Marriage'=>SyncModel::marriage(array('name'=>'Marriage', 'id'=>'Marriage','class'=>'select'),$data['person_info']['Marriage']),
                        'Flag2'=>SyncModel::yesNoForAddress(array('name'=>'Flag2', 'id'=>'Flag2','class'=>'select'),$data['person_info']['Flag2']),
                        'OpenBank'=>SyncModel::bankCode(array('name'=>'OpenBank','class'=>'select'),$data['person_info']['OpenBank']),
                        'familyRelative'=>SyncModel::familyRelative(array('name'=>'RelativeType','class'=>'select select_limit'),$data['person_info']['RelativeType'])
                    );
                }else{
                    $data['person_info'] = false;
                }

                $firm_arr = $this->get_firm_info($user_id);
                if($firm_arr['status']){
                    $data['firm_info'] = $firm_arr['message']['data'];
                    $data['selectArray']['Flag8'] = SyncModel::addNo(array('name'=>'Flag8','class'=>'select'),$data['firm_info']['Flag8']);
                }else{
                    $data['firm_info'] = false;
                }
            }
        }
        $data['auth'] = $auth;
        $data['loan'] = $loan;
        return $data;
    }


    /*
     * pc获取试算金额和期数
     */
    public function pc_trial_amount($user_id){
        $info = $this->get_fillout_loan_info($user_id);
        if($info['status']){
            $data['product']  = $info['message']['data'];

            $first_amounts = array_keys($data['product']);
            rsort($first_amounts);
            $data['first_amounts'] = $first_amounts;
            $data['purpose'] = SyncModel::cashPurpose();
        }else{
            $data['product']  = false;
            $data['fail_txt'] = $info['message']['data'];
        }
        return $data;
    }

    /**
     * 检查是否为汽车现金货的客户
     * @param $user_id
     */
    public function check_is_car_customer($user_id){
        $authService = new AuthService();
        $auth = $authService->get_auth_by_user_id($user_id);
        if($auth){
            if($auth->SubProductType == 3){
                return array('status'=>true);
            }else{
                return array('status'=>false);
            }
        }else{
            return array('status'=>false);
        }
    }

    public function create_application($user_id){
        $documentService = new DocumentService();
        $info = $documentService->get_new_loan_deal($user_id);
        Logger::info($info);
        if($info){
            return array('status'=>true,'message'=>array('data'=>$info));
        }else{
            return array('status'=>false);
        }
    }

    /**
     * 提交订单到安硕
     * @param $user_id
     * @param $code
     * @return array
     */
    public function commit_data_to_an_sys($user_id,$code){
        $loan_info = Loan::get_order_entry($user_id);
        if($loan_info['status']) {
            $loan_id = $loan_info['data']['OrderId'];
            $caApiService = new CaApiService();
            $info  = $caApiService->check_ca_challenge_code_from_user($loan_id,$user_id,$code);
            if($info['status']){
                $connectTimeService = new ConnectTimeService();
                $contact_time = $connectTimeService->get_connect_time_with_default();
                $loanModel = new LoanModel();
                $documentService = new DocumentService();
                //更新方便联系时间
                $loanModel->update_loan_by_id(array('contact_time'=>$contact_time),$loan_id);
                //触发提交订单
                $info = $documentService->user_aggressment_application($user_id, $loan_id);

                Queue::push(new SetGroupWechat(WechatModel::YET_APPLY, Auth::user()->openid));//分组

                return $info;
            }else{
                return array("status" => false, "msg" => $info['data']['message']);
            }
        }else{
            return array('status'=>false,'msg'=>'没有需要进行操作的货款信息');
        }
    }

    /**
     * 获取详细还款计划
     * @param $user_id
     * @return array
     */
    public function get_loan_schedules($user_id){
        $loanModel = new LoanModel();
        $loan_info = $loanModel->get_loan_newest($user_id);
        $status = array('050','160','110');
        if(in_array($loan_info->status,$status)){
            $loanScheduleService = new LoanScheduleService();
            $schedule = $loanScheduleService->deal_loan_schedules($loan_info->id);
            return array('status'=>true,'message'=>array('data'=>'获取还款计划成功','schedule'=>$schedule));
        }else{
            return array('status'=>false,'message'=>array('data'=>'暂时没有生成还款计划','schedule'=>''));
        }
    }

    public function get_user_loan($user_id){
        try {
            $loanModel = new LoanModel();
            $loan = $loanModel->get_loan_by_user_id($user_id);
            return array('status'=>true,'message'=>array('data'=>'获取订单成功','loan'=>(array)$loan));
        }catch(\Exception $e){
            return array('status'=>false,'message'=>array('data'=>'获取订单失败','loan'=>''));
        }
    }

    public function get_loan_message_by_order_id($order_id){
        $loanService = new LoanService();
        $info = $loanService->get_loan_by_loan_id($order_id);
        if($info['status']){
            return array('status'=>true,'message'=>array('data'=>'获取订单成功','loan'=>$info['message']['data']));
        }else{
            return array('status'=>false,'message'=>array('data'=>'获取订单失败','loan'=>''));
        }
    }

    public function get_loan_before_init($user_id,$loan_id,$array){
        $loanBeforeService = new LoanBeforeService();
        $loanBeforeService->init_loan_before($user_id,$loan_id);
        array_pull($array,'latitude');
        array_pull($array,'longitude');
        $network = $array['network'];
        array_push($array,'network');
        $loanBeforeService->update_pic_upload_by_loan_id($loan_id,$array);
        $loanAfterService = new LoanAfterService();
        $loanAfterService->init_loan_after($user_id,$loan_id);
        if($network){
            $loanAfterService->update_loan_after_by_loan_id($loan_id,array('network'=>$network));
        }
    }


}