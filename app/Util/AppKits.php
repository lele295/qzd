<?php
namespace App\Util;
use App\Crypt3Des\Facades\Crypt3Des;
use App\Model\mobile\WechatModel;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class AppKits{
    /**
     * js桥接跳转的判断,会把当前请求地址
     * 用法如果 返回值!==true,那么把返回值return回去
     * @param null $param
     * @return bool|string
     */
    static public function bridge($param = null){
        if(!WechatModel::getWxversion()){
            $agent = isset($_SERVER['HTTP_USER_AGENT'])?strtolower($_SERVER['HTTP_USER_AGENT']):'';
            $iphone = (strpos($agent, 'iphone')) ? true : false;
            $ipad = (strpos($agent, 'ipad')) ? true : false;
            $android = (strpos($agent, 'android')) ? true : false;
            if($param === null){
                $param = base64_encode(Input::get('loan_id'));
            }
            if($iphone || $ipad){
                return sprintf('<script>window.webkit.messageHandlers.AppModel.postMessage({loan_id: %s});</script>',"'$param'");
            }
            if($android){
                return sprintf('<script>window.location.href="javascript:faceIndentify.start2faceIndentify(%s)";</script>',"'$param'");
            }
            return true;
        }else{
            return true;
        }
    }

    static public function bridgeCheck(){
        if(!WechatModel::getWxversion()){
            $agent = isset($_SERVER['HTTP_USER_AGENT'])?strtolower($_SERVER['HTTP_USER_AGENT']):'';
            $iphone = (strpos($agent, 'iphone')) ? true : false;
            $ipad = (strpos($agent, 'ipad')) ? true : false;
            $android = (strpos($agent, 'android')) ? true : false;
            if($iphone || $ipad || $android){
                return false;
            }
            return true;
        }else{
            return true;
        }
    }


    /**
     * @param $attributes
     * @param $rules
     * @return bool
     */
    static public function validates($attributes,$rules){
        $validator = Validator::make($attributes,$rules);
        if($validator->passes()){
            return true;
        }else{
            /**
             * 获取第一条错误信息
             */
            $message = $validator->messages();
            echo Crypt3Des::encrypte(json_encode(array('status'=>false,'data'=>$message->first())));
            exit;
        }
    }
}