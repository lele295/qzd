<?php
namespace App\Service\ymrt;
/**
 * 亿美软通的类
 * Class Sms
 * @package App\Service\ymrt
 */
class Sms{
    private $_gwUrl = 'http://sdk999ws.eucp.b2m.cn:8080/sdk/SDKService';
    private $_serialNumber = '9SDK-EMY-0999-RDUSP';
    private $_password = '301334';
    /**
     * 登录后所持有的SESSION KEY，即可通过login方法时创建
     */
    private $_sessionKey = '301334';

    /**
     * 连接超时时间，单位为秒
     */
    private  $_connectTimeOut = 2;

    /**
     * 远程信息读取超时时间，单位为秒
     */
    private  $_readTimeOut = 10;

    private $_client = null;
    public function __construct(){
        define('SCRIPT_ROOT',  app_path() .'/Service/ymrt/');
        require_once SCRIPT_ROOT . 'include/Client.php';

        $this->_client = new \Client($this->_gwUrl,$this->_serialNumber,$this->_password,$this->_sessionKey,false,false,false,false,$this->_connectTimeOut,$this->_readTimeOut);
    }

    /**
     * 发送语音验证码
     * @return string
     */
    function sendVoice($mobile, $content)
    {
        /**
         * 下面的代码将发送验证码123456给 159xxxxxxxx
         */
        $statusCode = $this->_client->sendVoice(array($mobile),$content);
        return $statusCode;
    }
}