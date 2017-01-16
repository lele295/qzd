<?php

namespace App\Http\Controllers;


use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use App\Service\Wechat;
use Illuminate\Support\Facades\Log;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function __construct(){

    }

    /**
     * Ajax返回
     * @param type $code
     * @param type $msg
     * @param type $data
     */
    protected function responseJson($code, $msg = '', $data = []) {
        exit(json_encode(['code' => $code, 'msg' => $msg, 'data' => $data]));
    }

    /**
     * 根据Code获取OpenID
     */
    protected function openId()
    {
        $openId = Session::get('openid', null);
        if (!$openId) {
            $info = \Wechat::getInfoInThisUrl();
            Session::put('openid', $info['openid']);
            Session::save();
            $openId =  $info['openid'];
        }
        return $openId;
    }

    /**
     * 根据url参数获取Openid
     * @return boolean
     */
    protected function getOpenIdByUrlArgs() {
        $opid = Request::get('openid', '');
        if ($opid && Help::urlCheck($this->currentUrl())) {
            session(['openid' => $opid]);
            Log::info("从url参数获取OPENID:{$opid}");
            return $opid;
        }
        return false;
    }

    /**
     * 获取code
     */
    protected function getCode() {
        $appid = config('wx.appid');
        $curUrl = $this->currentUrl();
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$curUrl}&response_type=code&scope=snsapi_base&state=STATE#wechat_redirec";
        //Log::info("获取CODE,设备类型:" . ",IP地址:" . Request::getClientIp() . ",当前url：{$curUrl}");
        header("Location:{$url}");
        exit;
    }

    protected function currentUrl() {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
        return $url;
    }
}
