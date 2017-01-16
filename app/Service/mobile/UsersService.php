<?php
namespace App\Service\mobile;

use App\Log\Facades\Logger;
use App\Model\Admin\SmsManageModel;
use App\Model\Base\AuthModel;
use App\Model\Base\UniqueCodeModel;
use App\Model\Base\UserModel;

use App\Model\Datamigrate\UsersModel;
use App\Model\mobile\WechatModel;
use App\Util\AppKits;
use App\Util\Kits;
use App\Util\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class UsersService extends Service{

    public function send_mobile_code($mobile, $is_check = true, $source=1){

        $res_arr = $this->check_mobile_time();
        if($res_arr['status']){
            $res_arr = $this->check_send_mobile($mobile, $is_check, $source);
        }
        return $res_arr;
    }
    /*
     * 检验上次发送验证码的session时间
     */
    public function check_mobile_time(){
        //Session::forget("mobile_yzm");

        if(Session::has("mobile_yzm")){
            $diff = time() - Session::get("mobile_yzm");
            Logger::info($diff);
            if($diff < 60){
                $res_arr = array('status'=>false, 'msg'=>'发送验证码太频繁，请60秒后再试');
            }else{
                Session::put("mobile_yzm", time());
                $res_arr = array('status'=>true);
            }
        }else{
            Session::put("mobile_yzm", time());
            $res_arr = array('status'=>true);
        }

        Logger::info(Session::get("mobile_yzm"));
        return $res_arr;
/*
        //Session::forget("mobile_yzm");
        $lifeTime = 1;
        session_set_cookie_params($lifeTime);
        session_start();

        if(isset($_SESSION["mobile_yzm"])){
            $diff = time() - $_SESSION["mobile_yzm"];
            Logger::info($diff);
            if($diff < 60){
                $res_arr = array('status'=>false, 'msg'=>'发送验证码太频繁，请稍后再试');
            }else{
                $_SESSION["mobile_yzm"] = time();
                $res_arr = array('status'=>true);
            }
        }else{
            $_SESSION["mobile_yzm"] = time();
            $res_arr = array('status'=>true);
        }

        Logger::info($_SESSION["mobile_yzm"]);
        return $res_arr;
*/
    }

    /*
     * 根据手机号码检验并发送验证码
     * $is_check 为true 需要检验手机
     */
    public function check_send_mobile($mobile, $is_check = true, $source=1) {

        $users = new UserModel();
        if($is_check === true){
            if ($users->check_mobile($mobile)) {
                return array('status' => false, "msg" => "该手机已被注册");
            }
        }
        $unique = new UniqueCodeModel($source);
        $rand_key = $unique->randcode($mobile);

        Cache::put($mobile, $rand_key, 20);
        $content = "尊敬的客户，您的验证码为：".$rand_key."（有效期20分钟）";

        $res = $unique->select_send_supply($content, $mobile);
        //保存短信
        $array = array();
        $array = array_add($array,'user_id','');
        $array = array_add($array,'mobile',$mobile);
        $array = array_add($array,'content',$content);
        $array = array_add($array,'sms_type',1);
        $status = $res['status'] == 1 ? 0 : 1;
        $array = array_add($array,'status',$status);
        $sms = new SmsManageModel();
        $sms->insert_sms_manage($array);
        return $res;
    }

    public function login_in($mobile,$password){
        if(Auth::attempt(array(
            'mobile' => $mobile,
            'password' => $password))){
            $userModel = new UserModel();
            $userModel->update_user_info_by_id(Auth::id(),array('session_id'=>Session::getId()));
            return true;
        }else{
            return false;
        }
    }

    /*
     * pc注册
     */
    public function  pc_do_register($post_data){
        $yzm_res = $this->verify_code($post_data);
        if($yzm_res['status']){
            if ($yzm_res['msg']) {
                $rearr = array("status" => false, "msg" => "您已注册过,请登录");
            } else {
                $password = Hash::make($post_data['password']);
                $reg_arr = array('mobile' => $post_data["mobile"], 'source' => 2, 'password'=>$password);
                $user_res = UserModel::create($reg_arr);
                if ($user_res) {
                    $rearr = array("status" => true);
                } else {
                    $rearr = array("status" => false, "msg" => "注册失败");
                }
            }
        }else{
            $rearr = $yzm_res;
        }
        return $rearr;
    }


    /*
     * pc忘记密码
     */
    public function pc_forget_pass($post_data){
        $yzm_res = $this->verify_code($post_data);
        if($yzm_res['status']){
            if ($yzm_res['msg']) {
                $password = Hash::make($post_data['password']);
                $user_m = new UserModel();
                $affect = $user_m->update_user_info_by_mobile($yzm_res['msg']->mobile, array("password"=>$password));
                if($affect){
                    $rearr = array("status" => true, "msg" => "更改密码成功");
                }else{
                    $rearr = array("status" => false, "msg" => "更改密码失败");
                }
            }else{
                $rearr = array("status" => false, "msg" => "未找到该手机号用户");
            }
        }else{
            $rearr = $yzm_res;
        }
        return $rearr;
    }

    /*
     * 检验验证码
     */
    public function verify_code($post_data){
        if (Cache::has($post_data["mobile"]) && Cache::get($post_data["mobile"]) == $post_data["mobile_code"]) {
            Cache::forget($post_data["mobile"]);
            $userm = new UserModel();
            $userinfo = $userm->check_mobile($post_data["mobile"]);
            $rearr = array("status" => true, 'msg'=>$userinfo);
        } else {
            $rearr = array("status"=>false, "msg"=>"验证码错误");
        }
        return $rearr;
    }

    /*
     * 微信检验并注册
     */
    public function check_register($post_data)
    {
        if (Cache::has($post_data["mobile"]) && Cache::get($post_data["mobile"]) == $post_data["mobile_code"]) {
            Cache::forget($post_data["mobile"]);
            $userm = new UserModel();
            //$wechatservice = new WeChatService();
            /*$wxinfo = $wechatservice->get_wx_userinfo($post_data['access_token'], $post_data['openid']);
            if(!$wxinfo){
                $rearr = array("status"=>0, "msg"=>"请重新访问，微信获取信息失败");
            }else {*/
                $userinfo = $userm->check_mobile($post_data["mobile"]);
                if ($userinfo) {
                    $userm->update_user_info_by_id($userinfo->id, array('openid' => $post_data["openid"]));
                    //$userm->update_user_info_by_id($userinfo->id, array('openid' => $post_data["openid"], 'nickname'=>$wxinfo->nickname, 'headimgurl'=>$wxinfo->headimgurl));
                    Auth::loginUsingId($userinfo->id);
                    $rearr = array("status" => 1, "url" => "/center");
                } else {
                    $reg_arr = array('mobile' => $post_data["mobile"], 'openid' => $post_data["openid"], 'source' => $post_data["source"]);
                    //$reg_arr = array('mobile' => $post_data["mobile"], 'openid' => $post_data["openid"], 'source' => $post_data["source"], 'nickname'=>$wxinfo->nickname, 'headimgurl'=>$wxinfo->headimgurl);
                    $user_res = UserModel::create($reg_arr);
                    if ($user_res) {
                        $rearr = array("status" => 1, "url" => "/users/set-password/" . $post_data["openid"]);
                    } else {
                        $rearr = array("status" => 0, "msg" => "注册失败");
                    }
                }
            //}
        } else {
            $rearr = array("status"=>0, "msg"=>"验证码错误");
        }
        return $rearr;
    }

    /*
     * 检验登录并获取认证表信息
     */
    public function check_auth_info(){
        $info['is_login'] = false;
        if(Auth::check()){
            $auth_m = new AuthModel();
            $info['is_login'] = true;
            $info['auth_info'] = $auth_m->get_auth_info_by_user_id(Auth::id());
        }
        return $info;
    }

    /**
     * 设置用户登录
     */
    public function setAuth(){
        //判断是否登录
        if(Auth::check()){
            return;
        }

        if(Session::get('global_openid')){
            $openid = Session::get('global_openid');
            //尝试去登录
            if(UserModel::setAuthByOpenid($openid)){
                return true;
            }else{
                header('Location:' . '/users/register1');
                exit;
            }
        }


        //判断平台
        /**
         * app此页面需要访问原生
         */
        if(true !== ($brige = AppKits::bridge('/m/index/login'))){
            header('Location:/m/index/login');
            exit;
        }

        //获得code
        $wechat = new WechatModel();
        $openid = $wechat->getOpenidInThisUrlDealWithError(function(){
            /*去掉CODE的参数*/
            Kits::removeCodeAndRequestAgain();
        });
        Session::set('global_openid',$openid);
        Request::session()->save();

        //尝试去登录
        if(UserModel::setAuthByOpenid($openid)){
            return true;
        }else{
            header('Location:' . '/users/register1');
            exit;
        }
    }
}