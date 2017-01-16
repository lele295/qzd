<?php


namespace App\Api\api;


use App\Log\Facades\Logger;
use App\Model\Base\CommErrorModel;
use App\Service\base\AECCBC;
use App\Service\base\BLogger;
use App\Util\Curl;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

/**
安硕接口API
 */
class CaApi
{
    private $ip ;
    public function __construct(){
          $this->ip = Config::get('myconfig.ca_ip');
    }

    /*
     * 发送CA信息
     */
    public function send_ca_info($post_data)
    {
        $url = $this->ip."AnysignApi/anysignApiController.do?getChallengeCode";
        $curl = new Curl();
        Logger::info($url,'ca-send');
        $res = $curl->curlPostForAs($url, $post_data);
        $res = iconv('GBK', 'UTF-8', $res);
        $res = json_decode($res, true);
        Logger::info($res,'ca-send');
        return $res;
    }

    /*
     * 验证CA信息
     */
    public function check_ca_info($post_data)
    {
        $url = $this->ip."AnysignApi/anysignApiController.do?checkChallengeCodeAndSign";
        $curl = new Curl();
        Logger::info('ca验证传入的数据：'.json_encode($post_data),'ca-check');
        $res = $curl->curlPostForAs($url, $post_data);
        $res = iconv('GBK', 'UTF-8', $res);
        Logger::info('ca验证结果：'.$res,'ca-check');
        $res = json_decode($res, true);
        return $res;
    }
}