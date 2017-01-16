<?php
namespace App\Util;


use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;

/**
 * Class Kits
 * @package App\Util
 * @author zhuyan
 */
class Kits{
    /**
     * 输入的时间字符串必须大于等于今天，返回Y-m-d格式，否则返回空字符串
     * @param $dateStr 时间字符串
     * @return string
     */
    static public function dateBTToday($dateStr){
        $date = strtotime($dateStr);
        if($date > time()){
            return date('Y-m-d',$date);
        }else{
            return '';
        }
    }

    /**
     * 输出.js后缀文件 <script></srcipt>
     * 输出.css后缀文件
     * @param $val
     * @return string
     */
    static public function echoFile($val){
        $res = pathinfo($val);
        $result = $val;
        switch($res['extension']){
            case 'css':
                $result = '<link rel="stylesheet" type="text/css" href="'.$val.'?version='.Config::get('extension.version').'">';
                break;
            case 'js':
                $result = '<script src="'.$val.'?version='.Config::get('extension.version').'"></script>';
                break;
            default:
                break;
        }
        return $result;
    }

    /**
     * 获取毫秒级别的时间戳
     */
    public static function getMillisecond()
    {
        //获取毫秒的时间戳
        $time = explode ( " ", microtime () );
        $time = $time[1] . ($time[0] * 1000);
        $time2 = explode( ".", $time );
        $time = $time2[0];
        return $time;
    }

    /**
     * 把code参数替换掉
     */
    static public function removeCodeAndRequestAgain(){
        $paramArr = array_except($_GET,['code']);
        $url = Request::URL();
        if($paramArr){
            $url .= '?' . http_build_query($paramArr);
        }
        header('Location:' . $url);
        exit;
    }

    /**
     * 返回404
     */
    static public function return404(){
        //报404错误
        header("HTTP/1.1 404 Not Found");
        header("Status: 404 Not Found");
        exit;
    }

    /**
     * 是否分期购
     * @return bool
     */
    static public function isFqg(){
        if(env('FQG_HOST') == $_SERVER['HTTP_HOST']){
            return true;
        }else{
            return false;
        }
    }

    static public function signJsUrl(){
        if(env('FQG_HOST') == $_SERVER['HTTP_HOST']){
            return env('FQG_HOST');
        }else{
            return env('JS_URL');
        }
    }

    /**
     * 地址拼接变成详细地址
     * @param $county
     * @param $street
     * @param $community
     * @param $cell
     * @return string
     */
    static public function addressPact($county,$street,$community,$cell){
        return str_replace('*', '',$county . $street . $community . $cell);
    }

    /**
     * 取地址字符串
     * @param $address
     * @param $index
     * @return string
     */
    static public function explodeAddressPact($address,$index){
        $pieceLen = 15;
        $pieceCount = mb_strlen($address)/15;
        if(mb_strlen($address)%15){
            $pieceCount++;
        }
        if($pieceCount > 4){
            $pieceCount = 4;
        }
        if($index > $pieceCount){
            return '*';
        }
        return mb_substr($address,($index - 1) * $pieceLen,$pieceLen);
    }

    /**
     * 计算百分比
     * @param $numerator 分子
     * @param $denominator 分母
     * @return float
     * 结果保留两位小数，向下取整
     */
    static public function mathRate($numerator ,$denominator){
        if($denominator == 0){
            return 0;
        }
        return $numerator/$denominator;
    }


    /**
     * 根据身份证号判断性别, 男性用M标识，女性用F标识
     * @param $idcard  18位或者15位身份证号码
     * @return string
     */
    public static function judgeGender($idcard){
        $length = strlen($idcard);
        if(15 === $length){
            $characterNumber = (int)substr($idcard, -1, 1);
        }elseif (18 === $length){
            $characterNumber = (int)substr($idcard, -2, 1);
        }else{
            return '';
        }
        return (($characterNumber & 1) === 1) ? 'M' : 'F';
    }
}