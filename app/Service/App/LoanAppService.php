<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/4/6
 * Time: 13:38
 */

namespace App\Service\App;


use App\Log\Facades\Logger;
use App\Service\api\CaApiService;
use App\Service\api\LoanApiService;
use App\Service\base\LoanService;
use App\Service\base\UserService;
use App\Service\mobile\Service;
use App\Util\AppRule;
use App\Util\Curl;
use App\Util\FileReader;
use App\Util\FileWrite;
use App\Util\Loan;
use Illuminate\Support\Facades\Log;

class LoanAppService extends Service
{
    public function get_loan($user_id)
    {
        $loanApiService = new LoanApiService();
        $info = $loanApiService->get_fillout_loan_info($user_id);
        if($info['status']){
            return array('status'=>100,'data'=>array('message'=>'获取产品成功','product'=>$info['message']['data'],'default'=>$info['message']['default']));
        }else{
            return array('status'=>200,'data'=>array('message'=>$info['message']['data']));
        }
    }

    public function post_loan($info){
        $info['city'] = '440300';  //办单门店
        $info['issure'] = '1';  //是否投保
        $loanApiService = new LoanApiService();
        $info = $loanApiService->post_fillout_loan_info($info);
        if($info['status']){
            return array('status'=>100,'data'=>array('message'=>'试算成功'));
        }else{
            return array('status'=>200,'data'=>array('message'=>$info['message']['data']));
        }
    }

    public function get_person_info($user_id){
        $loanApiService = new LoanApiService();
        $info = $loanApiService->get_person_info($user_id);
        if($info['status']){
            return array('status'=>100,'data'=>array('message'=>'获取个人资料成功','entry'=>$info['message']['data']));
        }else{
            return array('status'=>200,'data'=>array('message'=>$info['message']['data'],'person'=>''));
        }
    }

    public function post_person_info($info){
        $userService = new UserService();
        $user_message = $userService->get_user_message_by_user_id($info['user_id']);
        if($user_message){
            $loanApiService = new LoanApiService();
            $info = array_add($info,'mobileno',$user_message->mobile);
            $result = $loanApiService->post_person_info($info);
            if($result['status']){
                return array('status'=>100,'data'=>array('message'=>'更新成功'));
            }else{
                return array('status'=>200,'data'=>array('message'=>$result['message']['data']));
            }
        }else{
            return array('status'=>200,'data'=>array('message'=>'没有该用户信息'));
        }
    }

    public function get_firm_info($user_id){
        $loanApiService = new LoanApiService();
        $info = $loanApiService->get_firm_info($user_id);
        if($info['status']){
            return array('status'=>100,'data'=>array('message'=>'获取单位资料成功','entry'=>$info['message']['data']));
        }else{
            return array('status'=>200,'data'=>array('message'=>$info['message']['data'],'firm'=>''));
        }
    }

    public function post_firm_info($info){
        $loanApiService = new LoanApiService();
        $result = $loanApiService->post_firm_info($info);
        if($result['status']){
            return array('status'=>100,'data'=>array('message'=>'单位信息更新成功'));
        }else{
            return array('status'=>200,'data'=>array('message'=>$result['message']['data']));
        }
    }

    public function get_customer_pic($user_id){
        $loanApiService = new LoanApiService();
        $info = $loanApiService->get_customer_pic($user_id);
        if($info['status']){
            return array('status'=>100,'data'=>array('message'=>'获取订单照片信息成功','entry'=>$info['message']['entry']));
        }else{
            return array('status'=>200,'data'=>array('message'=>$info['message']['data']));
        }
    }

    public function post_customer_pic($info){
        $rule_status = AppRule::file_picture_filter($info);
        if($rule_status['status']){
            $path = '/uploads/wechat/' . date("Y-m-d", time()).'/';
            $cert_face_pic_name = 'cert_face_pic_name'.time().$info['user_id'].'.jpg';
            $cert_opposite_pic_name = 'cert_opposite_pic_name'.time().$info['user_id'].'.jpg';
            $custom_pic_name = 'custom_pic_name'.time().$info['user_id'].'.jpg';
            FileWrite::write_storage_file($path,$cert_face_pic_name,base64_decode($info['cert_face_pic']));
            FileWrite::write_storage_file($path,$cert_opposite_pic_name,base64_decode($info['cert_opposite_pic']));
            FileWrite::write_storage_file($path,$custom_pic_name,base64_decode($info['custom_pic']));
            $array = array(
                'cert_face_pic'=>$path.$cert_face_pic_name,
                'cert_opposite_pic'=>$path.$cert_opposite_pic_name,
                'custom_pic'=>$path.$custom_pic_name,
                'user_id'=>$info['user_id']
            );
            $loanApiService = new LoanApiService();
            $result = $loanApiService->post_firm_pic_info($array);
            if($result['status']){
                return array('status'=>100,'data'=>array('message'=>'图片上传成功'));
            }else{
                return array('status'=>200,'data'=>array('message'=>$result['message']['data']));
            }
        }else{
            return array('status'=>200,'data'=>array('message'=>$rule_status['data']['message']));
        }
    }

    public function get_application($user_id){
        $loanApiService = new LoanApiService();
        $info = $loanApiService->create_application($user_id);
        if($info['status']){
           $loan_status = Loan::get_order_entry($user_id);
            if($loan_status['status']){
                $loan_entry = $loan_status['entry'];
                $loan_pact = $loan_entry->pact_url;
                $view = base64_decode(FileReader::read_storage_text_file($loan_pact));
                return array('status'=>100,'data'=>array('message'=>'获取页面申请协议成功','entry'=>$view));
            }else{
                return array('status'=>200,'data'=>array('message'=>'获取页面申请协议失败','entry'=>''));
            }
        }else{
            return array('status'=>200,'data'=>array('message'=>'获取页面申请协议失败','entry'=>''));
        }
    }

    public function post_ca_auth($user_id){
        $loan = Loan::get_order_entry($user_id);
        if($loan['status']){
            $loan_id = $loan['data']['OrderId'];
            $caApiService = new CaApiService();
            $info = $caApiService->send_challenge_code_to_ca_by_loan_id($loan_id,$user_id);
            if($info['status']){
                return array('status'=>100,'data'=>array('message'=>'挑战码发送成功','entry'=>''));
            }else{
                return array('status'=>200,'data'=>array('message'=>$info['data']['message'],'entry'=>''));
            }
        }else{
            return array('status'=>200,'data'=>array('message'=>'没有需要发送CA挑战码的订单','entry'=>''));
        }
    }

    public function post_application($info){
        $loanApiService = new LoanApiService();
        $info = $loanApiService->commit_data_to_an_sys($info['user_id'],$info['mobile_code']);
        if($info['status']){
            return array('status'=>100,'data'=>array('message'=>'提交成功','entry'=>''));
        }else{
            return array('status'=>200,'data'=>array('message'=>$info['msg'],'entry'=>''));
        }
    }

    /**
     * 获取还款计划
     * @param $user_id
     * @return array
     */
    public function get_loan_schedules($user_id){
        $loanApiService = new LoanApiService();
        $info = $loanApiService->get_loan_schedules($user_id);
        if($info['status']){
            return array('status'=>100,'data'=>array('message'=>'获取还款计划成功','entry'=>$info['message']['schedule']));
        }else{
            return array('status'=>200,'data'=>array('message'=>$info['message']['data'],'schedule'=>''));
        }
    }

    public function get_user_loan($user_id){
        $loanApiService = new LoanApiService();
        $info = $loanApiService->get_user_loan($user_id);
        if($info['status']){
            $loan_array  =array();
            foreach($info['message']['loan'] as $item){
                $array = (array)$item;
                array_push($loan_array,$array);
            }
            return array('status'=>100,'data'=>array('message'=>'获取用户订单成功','entry'=>$loan_array));
        }else{
            return array('status'=>200,'data'=>array('message'=>'获取用户订单失败','entry'=>$info['message']['loan']));
        }
    }

    /**
     * 获取客户图片信息
     * @param $user_id
     * @return array
     */
    public function get_customer_picture($user_id){
        $loanService = new LoanService();
        $info = $loanService->get_customer_pic($user_id);
        $pic_array = array(
            'cert_face_pic'=>'',
            'cert_opposite_pic'=>'',
            'custom_pic'=>''
        );
        if($info['status']){
            $pic = $info['message']['data'];
            if($pic){
                if($pic->cert_face_pic){
                    $pic_array['cert_face_pic'] = Curl::getHttpServer(true).'/imagestorage/'.$pic->cert_face_pic;
                }
                if($pic->cert_opposite_pic){
                    $pic_array['cert_opposite_pic'] = Curl::getHttpServer(true).'/imagestorage/'.$pic->cert_opposite_pic;
                }
                if($pic->custom_pic){
                    $pic_array['custom_pic'] = Curl::getHttpServer(true).'/imagestorage/'.$pic->custom_pic;
                }
            }
            return array('status'=>100,'data'=>array('message'=>'获取订单照片信息成功','pictures'=>$pic_array));
        }else{
            return array('status'=>200,'data'=>array('message'=>$info['message']['data']));
        }
    }

    /**
     * 贷款申请 - 上传附件 - 提交图片信息
     * @param $user_id
     * @param $info
     * @return array
     * status = 100成功    200 失败
     */
    public function post_customer_picture($user_id,$info){
        $rule_status = AppRule::file_picture_filter($info);
        if($rule_status['status']){
            $path = '/uploads/app/attachment/' . date("Y-m-d", time()).'/';
            $cert_face_pic_name = 'cert_face_pic_name'.time().$user_id.'.jpg';
            $cert_opposite_pic_name = 'cert_opposite_pic_name'.time().$user_id.'.jpg';
            $custom_pic_name = 'custom_pic_name'.time().$user_id.'.jpg';
            FileWrite::write_storage_file($path,$cert_face_pic_name,base64_decode($info['cert_face_pic']));
            FileWrite::write_storage_file($path,$cert_opposite_pic_name,base64_decode($info['cert_opposite_pic']));
            FileWrite::write_storage_file($path,$custom_pic_name,base64_decode($info['custom_pic']));
            $array = array(
                'cert_face_pic'=>$path.$cert_face_pic_name,
                'cert_opposite_pic'=>$path.$cert_opposite_pic_name,
                'custom_pic'=>$path.$custom_pic_name,
                'user_id'=>$user_id
            );
            $loanApiService = new LoanApiService();
            $result = $loanApiService->post_firm_pic_info($array);
            if($result['status']){
                return array('status'=>100,'data'=>array('message'=>'图片上传成功'));
            }else{
                return array('status'=>200,'data'=>array('message'=>$result['message']['data']));
            }
        }else{
            return array('status'=>200,'data'=>array('message'=>$rule_status['data']['message']));
        }
    }
}