<?php
namespace App\Service\App;
use App\Model\Base\UserModel;
use App\Service\base\AuthService;
use App\Service\base\UserService;
use App\Service\mobile\Service;
use App\Service\mobile\UsersService;
use App\Util\AppRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class UserAppService extends Service
{
    /**
     * status = 100成功    status = 200 失败
     * @param $info
     * @return array
     */
    /*public function register($info){
        $rule = AppRule::register_rule($info);
        if($rule['status']){
                Cache::forget($info["mobile"]);
                $userm = new UserModel();
                $userinfo = $userm->check_mobile($info["mobile"]);
                if ($userinfo) {
                    return array('status'=>200,'data'=>array('message'=>'该帐号已存在'));
                } else {
                    $password = Hash::make($info['password']);
                    if($info['openid']){
                        $info['source'] = 2;
                    }else{
                        $info['source'] = 4;
                    }
                    $reg_arr = array('mobile' => $info["mobile"], 'source' => $info['source'], 'password'=>$password,'openid'=>$info['openid']);
                    UserModel::create($reg_arr);
                    return array('status'=>100,'data'=>array('message'=>'注册成功'));
                }
        }else{
            return array('status'=>200,'data'=>array('message'=>$rule['data']['message']));
        }
    }*/

    /**
     * 注册
     * @param $info
     * @return array
     * status = 100成功    status = 200 失败
     */
    public function register($info){
        $rule = AppRule::register_rule($info);
        if($rule['status']){
            if(Cache::has($info['mobile']) && Cache::get($info['mobile']) == $info['mobile_code']){
                Cache::forget($info['mobile']);
                $userModel = new UserModel();
                $userInfo = $userModel->check_mobile($info['mobile']);
                if ($userInfo) {
                    return array('status'=>200,'data'=>array('message'=>'该帐号已存在'));
                } else {
                    $password = Hash::make($info['password']);
                    $reg_arr = array('mobile' => $info['mobile'], 'source' => 1, 'password'=>$password);
                    UserModel::create($reg_arr);
                    return array('status'=>100,'data'=>array('message'=>'注册成功'));
                }
            }else{
                return array('status'=>200,'data'=>array('message'=>'手机验证码错误'));
            }
        }else{
            return array('status'=>200,'data'=>array('message'=>$rule['data']['message']));
        }
    }

    /**
     * 发送手机验证码
     * @param $mobile
     * @param bool $is_check
     * @param int $source
     * @return array
     * status = 100成功    status = 200 失败
     */
    public function send_mobile_code($mobile, $is_check = true, $source = 1){
        if(!$mobile){
            return array('status'=>200,'data'=>array('message'=>'手机号码错误或者为空'));
        }
        $userService = new UsersService();
        $res_arr = $userService->check_mobile_time();
        if($res_arr['status']){
            $result = $userService->check_send_mobile($mobile, $is_check, $source);
            if($result['status']){
                return array('status'=>100,'data'=>array('message'=>'手机验证码发送成功'));
            }else{
                return array('status'=>200,'data'=>array('message'=>$result['msg']));
            }
        }else{
            return array('status'=>200,'data'=>array('message'=>$res_arr['msg']));
        }
    }

    /**
     * 登录
     * status = 100成功 status = 200 失败
     * @param $info
     * @return array
     */
    public function login($info){
        $rule = AppRule::login_rule($info);
        if($rule['status']){
            if (Auth::attempt(array(
                'mobile' => $info['mobile'],
                'password' => $info['password']))){
                $userService = new UserService();
                $user = $userService->get_user_message_by_mobile($info['mobile']);
                return array('status'=>100,'data'=>array('message'=>'登录成功','user'=>(array)$user));
            }else{
                return array('status'=>200,'data'=>array('message'=>'账号或密码错误'));
            }
        }else{
            return array('status'=>200,'data'=>array('message'=>$rule['data']['message']));
        }
    }

    /**
     * 忘记密码
     * @param $info
     * @return array
     * status = 100成功    status = 200 失败
     */
    public function resetPwd($info){
        $rule = AppRule::forgetPwd_rule($info);
        if($rule['status']){
            if(Cache::has($info['mobile']) && Cache::get($info['mobile']) == $info["mobile_code"]){
                Cache::forget($info['mobile']);
                $userModel = new UserModel();
                $userInfo = $userModel->check_mobile($info['mobile']);
                if($userInfo){
                    try {
                        $password = Hash::make($info['password']);
                        $userService = new UserService();
                        $userService->update_user_message_by_mobile($info['mobile'], array('password' => $password));
                        return array('status'=>100,'data'=>array('message'=>'重置密码成功'));
                    }catch(\Exception $e){
                        return array('status'=>200,'data'=>array('message'=>'重置密码失败'));
                    }
                }else{
                    return array('status'=>200,'data'=>array('message'=>'未找到该手机号用户'));;
                }
            }else{
                return array('status'=>200,'data'=>array('message'=>'手机验证码错误'));
            }
        }else{
            return array('status'=>200,'data'=>array('message'=>$rule['data']['message']));
        }
    }

    /**
     * 修改密码
     * @param $info
     * @return array
     * status = 100成功    status = 200 失败
     */
    public function updatePwd($user_id,$info){
        $rule = AppRule::updatePwd_rule($info);
        if($rule['status']){
            $userService = new UserService();
            $userInfo = $userService->get_user_message_by_user_id($user_id);
            if(Hash::check($info['old_password'],$userInfo->password)){
                try {
                    $password = Hash::make($info['password']);
                    $userService->user_message_by_user_id($user_id,array('password' => $password));
                    return array('status'=>100,'data'=>array('message'=>'修改密码成功'));
                }catch(\Exception $e){
                    return array('status'=>200,'data'=>array('message'=>'修改密码失败'));
                }
            }else{
                return array('status'=>200,'data'=>array('message'=>'原始密码不正确'));
            }
        }else{
            return array('status'=>200,'data'=>array('message'=>$rule['data']['message']));
        }
    }

    public function get_user_message($user_id){
        $userService = new UserService();
        $user = $userService->get_user_message_by_user_id($user_id);
        if($user){
            return array('status'=>100,'data'=>array('message'=>'获取用户成功','user'=>(array)$user));
        }else{
            return array('status'=>100,'data'=>array('message'=>'获取用户成功','user'=>''));
        }
    }

    public function get_user_by_open_id($open_id){
        $userService = new UserService();
        $user = $userService->get_user_message_by_open_id($open_id);
        if($user){
            $user_id = $user->id;
            Auth::loginUsingId($user_id);
            return array('status'=>100,'data'=>array('message'=>'登录成功','user'=>(array)$user));
        }else{
            return array('status'=>200,'data'=>array('message'=>'登录失败'));
        }
    }

    public function get_user_by_mobile($mobile){
        $userService = new UserService();
        $user = $userService->get_user_message_by_mobile($mobile);
        if($user){
            $user_id = $user->id;
            Auth::loginUsingId($user_id);
            return array('status'=>100,'data'=>array('message'=>'登录成功','user'=>(array)$user));
        }else{
            return array('status'=>200,'data'=>array('message'=>'登录失败'));
        }
    }

    public function get_user_real_name_cert_id_login($mobile,$open_id,$real_name,$id_card){
        $userService = new UserService();
        $user = $userService->get_user_message_by_mobile($mobile);
        if($user){
            $user_id = $user->id;
        }else{
            $user_id = $userService->insert_user_message(array('openid'=>$open_id,'mobile'=>$mobile,'source'=>3));
        }
        Auth::loginUsingId($user_id);
        $info['user_id'] = $user_id;
        $info['id_card']  =$id_card;
        $info['real_name'] = $real_name;
        $authAppService = new AuthAppService();
        $auth_result = $authAppService->auth($info);
        $authService = new AuthService();
        $authService->get_auth_loan_message_by_user_id($user_id);
        if($auth_result['status'] === 100){
            return array('status'=>100,'data'=>array('message'=>'可以进行贷款','user_id'=>$user_id));
        }else{
            return array('status'=>200,'data'=>array('message'=>$auth_result['data']['message']));
        }
    }
}