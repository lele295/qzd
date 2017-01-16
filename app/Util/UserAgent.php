<?php
namespace App\Util;
use App\Log\Facades\Logger;

/**
 * Class UserAgent
 * @package App\Util
 */
class UserAgent{
    const OS_IPHONE = 'iphone';
    const OS_ANDROID = 'android';
    private $_user_agent = '';
    private $_iphone_android_flag = false;
    private $_os_arr = [];
    public function __construct(){
        $this->_user_agent = $_SERVER['HTTP_USER_AGENT'];
        $str = $this->_user_agent;
        $match = [];
        preg_match('~Mozilla/5.0 \(([^\)]+)\)~',$str,$match);
        if(!isset($match[1])){
            return '';
        }
        $osStr = $match[1];
        /*用;分隔*/
        $this->_os_arr = $osArr = explode(';',$osStr);
        if(strpos($osStr,'iPhone') !== false){
            $this->_iphone_android_flag = self::OS_IPHONE;
        }elseif(strpos($osStr,'Android') !== false){
            $this->_iphone_android_flag = self::OS_ANDROID;
        }
    }

    /**
     * 获得设备类型
     * 比如iphone 6，华为mate s
     */
    public function deviceMode(){
        /*第一步找build参数*/
        if($this->_iphone_android_flag == self::OS_ANDROID){
            return isset($this->_os_arr[2])?$this->_os_arr[2]:'';
        }else{
            return isset($this->_os_arr[1])?$this->_os_arr[1]:'';
        }
    }

    /**
     * 获得手机操作系统
     */
    public function os(){
        /*正则匹配*/
//        $str = '浏览器信息：Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36';
//        $str = 'Mozilla/5.0 (Linux; Android 5.1.1; HUAWEI CRR-UL00 Build/HUAWEICRR-UL00) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/37.0.0.0 Mobile MQQBrowser/6.2 TBS/036215 Safari/537.36 MicroMessenger/6.3.16.49_r03ae324.780 NetType/WIFI Language/zh_TW';
        $str = $this->_user_agent;
        $match = [];
        preg_match('~Mozilla/5.0 \(([^\)]+)\)~',$str,$match);
        if(!isset($match[1])){
            return '';
        }
        $osStr = $match[1];
        /*用;分隔*/
        $osArr = explode(';',$osStr);
        /*分两条线走,出现iphone和出现android的*/
        if(strpos($osStr,'iPhone') !== false){
            if(isset($osArr[1])) return $osArr[1];
        }elseif(strpos($osStr,'Android') !== false){
            if(isset($osArr[1])) return $osArr[1];
        }else{
            return '';
        }

        return '';
    }

    /**
     * 判断是android，iphone或者其他
     */
    public function iphoneOrAndroid(){

    }

    //正值表达式比对解析$_SERVER['HTTP_USER_AGENT']中的字符串 获取访问用户的浏览器的信息
    function determinebrowser () {
        $Agent = $this->_user_agent;
        $browseragent="";   //浏览器
        $browserversion=""; //浏览器的版本
        if (ereg('MSIE ([0-9].[0-9]{1,2})',$Agent,$version)) {
            $browserversion=$version[1];
            $browseragent="Internet Explorer";
        } else if (ereg( 'Opera/([0-9]{1,2}.[0-9]{1,2})',$Agent,$version)) {
            $browserversion=$version[1];
            $browseragent="Opera";
        } else if (ereg( 'Firefox/([0-9.]{1,5})',$Agent,$version)) {
            $browserversion=$version[1];
            $browseragent="Firefox";
        }else if (ereg( 'Chrome/([0-9.]{1,3})',$Agent,$version)) {
            $browserversion=$version[1];
            $browseragent="Chrome";
        }
        else if (ereg( 'Safari/([0-9.]{1,3})',$Agent,$version)) {
            $browseragent="Safari";
            $browserversion="";
        }
        else {
            $browserversion="";
            $browseragent="Unknown";
        }
        return $browseragent." ".$browserversion;
    }
}