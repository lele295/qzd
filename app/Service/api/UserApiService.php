<?php
namespace App\Service\api;

use App\Commands\WechatMessage;
use App\Http\Service\queue\WechatQueue;
use App\Model\Base\AuthModel;
use App\Model\Base\UniqueCodeModel;
use App\Model\Base\UserModel;
use App\Service\base\UserService;
use App\Service\mobile\Service;
use App\Service\mobile\UsersService;
use App\Service\mobile\WeChatService;
use App\Util\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class UserApiService extends Service{

    /**
     * 根据用户名跟密码登录
     * @param $mobile
     * @param $password
     */
    public function login_with_mobile($mobile,$password){
        if(Auth::attempt(array('mobile' => $mobile,'password' => $password))){
            return array('status'=>true,'date'=>array('message'=>'登录成功'));
        }else{
            return array('status'=>false,'date'=>array('message'=>'登录失败'));
        }
    }

    /**
     * 通过wechat进行登录操作
     * 根据用户微信的openid进行登录
     * @param $openid
     * @return array
     */
    public function login_with_openid($openid){
        $userModel = new UserModel();
        $user_info = $userModel->sel_openid_user($openid);
        if($user_info){
            Auth::loginUsingId($user_info->id);
            Session::put('openid',$openid);
            $userModel->update_user_info_by_id($user_info->id, array('updated_at'=>date('Y-m-d H:i:s',time())));
            return array('status'=>true,'data'=>array('message'=>'登录成功'));
        }else{
            return array('status'=>false,'date'=>array('message'=>'登录失败'));
        }
    }


    /**
     * 进行用户注册
     * @param $mobile
     * @param $mobile_code
     * @param $opt 为可选参数
     * @return array
     *
     */
    public function register($mobile,$mobile_code,$opt = array()){
        $unique = new UniqueCodeModel();
        $check_code = $unique->check_yzm($mobile,$mobile_code);
        if($check_code){
            $userModel = new UserModel();
            $userinfo = $userModel->check_mobile($mobile);
            if($userinfo){
                return array('status'=>true,'date'=>array('message'=>'检验通过'));
            }else{
                return array('status'=>false,'data'=>array('message'=>'该手机号码已存在','code'=>'mobile_error','info'=>$userinfo));
            }
        }else{
            return array('status'=>false,'data'=>array('message'=>'验证码错误','code'=>'mobile_code_error'));
        }
    }

    /**
     * 微信端注册
     * @param $mobile
     * @param $mobile_code
     * @param $access_token
     * @param $openid
     * @param int $source
     * @param array $opt
     * @return array
     */
    public function we_chat_register($mobile,$mobile_code,$access_token,$openid,$source = 1,$opt = array()){
        $info = $this->register($mobile,$mobile_code);
        $userModel = new UserModel();
        if($info['status']){
            $array = array('mobile'=>$mobile,'openid'=>$openid,'source' => $source);
            $user_res = UserModel::create($array);
            if($user_res){
                Bus::dispatch(new WechatMessage($access_token,$openid));
                return array('status'=>true,'data'=>array('message'=>'注册成功','next_url'=>'/users/set-password/'.$openid));
            }else{
                return array('status'=>false,'data'=>array('message'=>'系统繁忙，请重试'));
            }
        }else{
            $data = $info['data'];
            if($data['code'] == 'mobile_error'){
                $update = array('openid'=>$openid);
                $userModel->update_user_info_by_id($data['info']->id,$update);
                Auth::loginUsingId($data['info']->id);
                Bus::dispatch(new WechatMessage($access_token,$openid));
            }else{
                return $info;
            }
        }
    }

    /**
     * 为手机客户端用户注册逻辑
     * @param $mobile
     * @param $mobile_code
     * @param int $source
     * @return array
     */
    public function phone_register($mobile,$mobile_code,$source=2){
        $info = $this->register($mobile,$mobile_code);
        if($info['status']){
            $array = array('mobile'=>$mobile,'source' => $source);
            $user_res = UserModel::create($array);
            if($user_res){
                return array('status'=>true,'data'=>array('message'=>'注册成功'));
            }else{
                return array('status'=>false,'data'=>array('message'=>'系统繁忙，请重试'));
            }
        }else{
            $data = $info['data'];
            if($data['code'] == 'mobile_error'){
                Auth::loginUsingId($data['info']->id);
            }else{
                return $info;
            }
        }
    }

    /**
     * 为用户设置密码
     * @param UserModel $userModel
     * @param $user_id 用户id
     * @param $password 新密码
     * @return bool返回结果
     * 返回ture表示设置成功，返回false表示设置失败
     */
    public function set_password(UserModel $userModel,$user_id,$password){
        if(!($userModel instanceof UserModel)){
            return false;
        }
        $password = Hash::make($password);
        $info = $userModel->update_user_info_by_id($user_id,array('password'=>$password));
        if($info){
            return true;
        }else{
            return false;
        }
    }


    /**
     * 微信端设置密码
     * @param $open_id
     * @param $password
     * @return array
     */
    public function we_chat_set_password($open_id,$password){
        $userModel = new UserModel();
        $user_info = $userModel->sel_openid_user($open_id);
        if($user_info){
            $info = $this->set_password($userModel,$user_info->id,$password);
            if($info){
                Auth::loginUsingId($user_info->id);
                return array('status'=>true,'data'=>array('message'=>'密码设置成功'));
            }else{
                return array('status'=>false,'data'=>array('message'=>'密码设置失败，请重试'));
            }
        }else{
            return array('status'=>false,'data'=>array('message'=>'密码设置失败，请重试','reason'=>'该用户OPENID不存在'));
        }
    }

    /**
     * 为手机客户端设置密码
     * @param $user_id
     * @param $password
     * @return array
     */
    public function phone_set_password($user_id,$password){
        $userModel = new UserModel();
        $info = $this->set_password($userModel,$user_id,$password);
        if($info){
            return array('status'=>true,'data'=>array('message'=>'密码设置成功'));
        }else{
            return array('status'=>false,'data'=>array('message'=>'密码设置失败，请重试'));
        }
    }

    /**
     * PC重置密码
     * @param $user_id
     * @param $input
     * @return array
     */
    public function pc_set_password($user_id,$input){
        $info = Rule::pc_set_forget_password($input);
        if($info['status']){
            $userModel = new UserModel();
            $user = $userModel->get_user_message_by_id($user_id);
            $hash_password = Hash::make($input['password']);
            if(!$user->password || Hash::check($input['oldPassword'],$user->password)){
                $userModel->update_user_info_by_id($user_id,array('password'=>$hash_password));
                return array('status'=>true,'data'=>array('message'=>"重置密码成功"));
            }else{
                return array('status'=>false,'data'=>array('message'=>'原始密码不正确'));
            }
        }else{
            return array('status'=>false,'data'=>array('message'=>$info['data']['message']));
        }
    }

}