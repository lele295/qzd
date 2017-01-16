<?php

namespace App\Service;

use App\Model\mobile\WechatModel;
use App\Log\Facades\Logger;
use Illuminate\Support\Facades\Auth;
use App\Util\FileWrite;
use App\Util\FileReader;
use Illuminate\Support\Facades\Session;


class Wechat {
    private $_appid;
    private $_secret;
    public function __construct()
    {
        $this->_appid = config('wx.appid');
        $this->_secret = config('wx.secret');
    }

    //获取微信access_token有效期2小时
    public function get_access_token() {

        $weModel = new WechatModel();
        $data = $weModel->getInfo();

        if(empty($data->access_token) || (time()-$data->access_time) >= 7000){
            //Logger::info($this->_appid.'---'.$this->_secret,'appid-secret');
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $this->_appid . "&secret=" . $this->_secret;
            $output = $this->curl_get($url);
            //认为过期，重新更新access_token
            if($output->access_token){
                $time = time();
                $weModel->update_access_token($output->access_token,$time);
                return $output->access_token;
            }

        }else{

            return $data->access_token;
        }
    }


    //jssdk所需数据
    public function getSignPackage() {
        $jsapiTicket = $this->getJsApiTicket();

        //注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);
        $signPackage = array(
            "appId" => $this->_appid,
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }

    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }


    private function getJsApiTicket() {

        $weModel = new WechatModel();
        $data = $weModel->getInfo();

        if(empty($data->ticket) || (time()-$data->ticket_time) >= 7000){
            $accessToken = $this->get_access_token();
            //如果是企业号用以下 URL 获取 ticket
            //$url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $res = $this->curl_get($url);

            if($res->ticket){
                $time = time();
                $weModel->update_ticket($accessToken,$res->ticket,$time);
                return $res->ticket;
            }

        }else{
            return $data->ticket;
        }
    }

    //下载微信服务器图片
    public function downloadWeixinFile($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOBODY, 0);    //只取body头
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $package = curl_exec($ch);
        $httpinfo = curl_getinfo($ch);
        curl_close($ch);
        //获得图片数组包
        $imageAll = array_merge(array('header' => $httpinfo), array('body' => $package));

        $response = json_decode($package,true);
        if(isset($response['errcode'])){
            Logger::info('图片上传imageDownLoadErr:' . $package);
            return array('status'=>false,'msg'=>'服务端获取图片异常');
        }
        if(!isset($imageAll["body"]) || strlen($imageAll["body"])<10000){
            return array("status"=>false, "msg"=>"上传失败或图片太小");
        }
        $user_id = Session::get('user_id');
        $path = '/uploads/wechat/' . date("Y-m-d", time()).'/';
        $filename = time()."_".$user_id.".jpg";
        $img_path = FileWrite::write_storage_file($path,$filename,$imageAll["body"]);
        if($img_path){
            $arr = array("status"=>true, "path"=>$img_path,'msg'=>FileReader::read_storage_image_resize_file($img_path));
        }
        else{
            $arr = array("status"=>false, "msg"=>"上传失败");
        }
        return $arr;
    }


    //获取微信code
    public function get_wechat_code($url) {
        $returnurl = urlencode($url);
        $wxcodeurl = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $this->_appid . '&redirect_uri=';
        $wxcodeurl .= $returnurl . '&response_type=code&scope=snsapi_base&state=1#wechat_redirect';
        header("Location: " . $wxcodeurl);
        exit;
    }


    //根据CODE获取OPENID
    public function get_wechat_openidbycode($code) {
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $this->_appid . "&secret=" . config('wx.secret') . "&code=" . $code . "&grant_type=authorization_code";
        $wxres = $this->curl_get($url);
        //如果返回错误信息，则记录日志。返回'';
        if (property_exists($wxres, 'openid')) {
            return $wxres->openid;
        } else {
            $error = 'WeChat Call Error --> {"errcode":%s,"errmsg":"%s"}';
            $error = sprintf($error, $wxres->errcode, $wxres->errmsg);;
        }
        return '';
    }

    //通过openid获取用户的基本你信息
    public function get_user_info_by_openid($openid){
        $access_token = $this->get_access_token();
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $data = $this->curl_get($url);
        return $data;
    }

    //curl get获取消息
    public function curl_get($url="")
    {
        if(!$url){ return false; }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);//设定为不验证证书和host
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = json_decode(curl_exec($ch));
        curl_close($ch);
        return $output;
    }

    //curl post获取消息
    public function curl_post($url="", $data="")
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);//设定为不验证证书和host
        curl_setopt ( $ch, CURLOPT_URL, $url);
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
        $res = json_decode(curl_exec($ch));
        return $res;
    }

}
