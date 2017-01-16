<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Hash;
use App\Org\Verify;
use Redirect;
use DB;
use App\Model\Backend\BackendUser;

/**
 * Description of PassportController
 *
 * @author lenovo
 */
class LoginController extends Controller {

    public function getIndex() {
        $backUser = json_decode(session('back_user'), true);
        if (!empty($backUser)) {
            //return redirect('/backend/main');
        }
        return view('backend.login');
    }

    public function postIndex(Request $request) {
        $data = $request->except(['_token']);
        //dd($data);
        $verify = new Verify();
        if (!$verify->check($data['code'])) {
            return redirect('backend/login')->with(['msg' => '验证码错误'])->withInput();
        }
        $user =  BackendUser::findByUsername(trim($data['username']));
        //dd($user);
        //if (empty($user) || !Hash::check($data['password'], $user['password'])) {
        if (is_numeric($data['username']) && strlen($data['username'])){
            //dd(1);
            if (empty($user) || !(md5($data['password']) == $user->PASSWORD)){
                return redirect('backend/login')->with(['msg' => '用户名或密码错误'])->withInput();
            }
        } else {
            //dd(2);
            if (empty($user) || !(md5($data['password']) == $user['password'])){
                return redirect('backend/login')->with(['msg' => '用户名或密码错误'])->withInput();
            }
        }
        //dd($user);
        if (isset($user->password)){
            unset($user->password, $user->session_id);
        } else {
            unset($user->PASSWORD, $user->session_id);
        }
        session(['back_user'=>json_encode($user)]);
        session()->save();
        //$this->successLogin($user, $request);
        return redirect('backend/main');
    }

    protected function successLogin($user, $request) {
        $data['last_login_time'] = time();
        $data['last_login_ip'] = $request->ip();
        $data['login_count'] = $user['login_count'] + 1;
        BackendUser::where('id', $user['id'])->update($data);
    }

    /**
     * 登出
     * @param Request $request
     * @return type
     */
    public function getLogout(Request $request) {
        $request->session()->forget('key');
        $request->session()->flush();
        return redirect('backend/login');
    }

    public function getVerify() {
        $verify = new Verify();
        $verify->imageL = 220; // 验证码图片长
        $verify->entry();
    }
    /**
     * 添加
     *
     */
    public function getAdduser(){
        return view('backend.page.adduser');
    }
     /**
     * 添加管理员
     *
     */
    public function postEdituser(request $request){
       // $data = $request->except(['_token']);
       // print_r($data);die();
        $pdata = $request->all();
        if ( isset($pdata) ){
            $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
            if (! preg_match($pattern,$pdata["email"]) ){
               echo '您输入的邮箱有误，请重新输入'.'<br />';
            }else if (! preg_match("/1[3458]{1}\d{9}$/",$pdata["phone"])){
               echo "您输入的手机号码有误，请重新输入"; 
            }else if (preg_match('/^\d+$/i',$pdata["real_name"])){
               echo "您输入的帐号不能全部是数字，请重新输入"; 
            }else{
               
                $data = array(
                    "username" => $pdata["username"],
                    "password" => bcrypt($pdata["password"]),
                    "real_name" => $pdata["real_name"],
                    "email" => $pdata["email"],
                    "phone" => $pdata["phone"],
                    "created_at"=>$pdata['created_at']    
                );
                $user=new BackendUser();
                $users=$user->AddUsers($data);
                if ($users) {
                  return Redirect('backend/login/logout')->with(['msg', '添加成功'])->withInput();

                } else {

                    return Redirect::to('backend/login/adduser')->with('msg', '添加失败,请重新添加')->withInput();
                }
            }
        }   
    }
    
   
}
