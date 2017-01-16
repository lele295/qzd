<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/4/12
 * Time: 9:43
 * Update: 2016-05-31 16:40
 *          新增属性 $protocol, $interfaceURI
 *          重写方法 send_wechat_message()
 */

namespace App\Api\api;


use App\Util\Curl;
use Illuminate\Support\Facades\Config;
use App\Log\Facades\Logger;

/**
 * 分期购
 * Class WechatApi
 * @package App\Api\api
 */
class WechatApi
{
    private $ip ;

	// 协议
	private $protocol;

	//接口资源标识数组
	private $interfaceURI;

    public function __construct(){
        $this->ip = Config::get('myconfig.FQG_send_msg_ip');
	    // 2016-05-31增加
	    $this->interfaceURI = Config::get('myconfig.FQG_send_msg_interface');
	    $this->protocol = Config::get('myconfig.FQG_protocol');
    }

	/*
    public function send_wechat_message($data){
        $url = $this->ip."CheckCustomer";
        $response = Curl::curlGet($url ,$data);
        return $response;
    }
	*/

	//2016-05-31 16:28重写
	public function send_wechat_message($type, $arr){
		if(!in_array($type, ['type_1', 'type_2'], true)){
			return ['success' => false, 'msg' => '非法参数！'];
		}
		$interfaceType = $this->interfaceURI[$type];
		$url = $this->protocol . '://' . $this->ip . $interfaceType;
		Logger::info('接口' . $url . '发送的消息内容为：' . json_encode($arr, JSON_UNESCAPED_UNICODE), 'fqg-msg-interface');
		$response = Curl::curlPost($url, $arr);
		Logger::info('接口' . $url . '返回值为：'. $response, 'fqg-msg-interface');
		return $response;
	}


	//分期购 推送
	public function AppPush($id_cards,$messageId){
        $data = array('id_cards'=>json_encode($id_cards),'message_id'=>$messageId);

		$url = env('FQG_APP_MSG');

        $i = 0;
        $param = '';
        foreach ($data as $k => $v) {
            ++$i;
            if ($i == 1) {
                $param = ($param . '?' . $k . '=' . $v);
            } else {
                $param .= ('&' . $k . '=' . $v);
            }
        }


        Logger::info('访问路径' . $url . $param,'app_message_record');
		$response = Curl::curlGet($url, $data,false,'origin');
//		Logger::info('接口' . $url . '返回值为：'. $response, 'fqg-msg-interface');
        Logger::info('返回结果' . $response ,'app_message_record');
		return $response;
    }
}