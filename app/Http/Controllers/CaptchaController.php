<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/9/29
 * Time: 11:21
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Support\Facades\Session;

class CaptchaController extends Controller
{
    //获取图片验证码
    public function getIndex($tmp=1){
        ob_clean();
        //生成验证码图片的Builder对象，配置相应属性
        $builder = new CaptchaBuilder;
        $font = public_path()."/uploads/"."STHUPO.TTF";
        //可以设置图片宽高及字体
        $builder->build($width=200, $height=80, $font = $font);
        //获取验证码的内容
        $phrase = $builder->getPhrase();

        //把内容存入session
        Session::put('milkcaptcha', $phrase);
        //生成图片
        header("Cache-Control: no-cache, must-revalidate");
        header('Content-Type: image/jpeg');
        $builder->output();
    }
}