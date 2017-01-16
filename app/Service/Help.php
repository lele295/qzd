<?php

namespace App\Service;

use Illuminate\Support\Facades\DB;
use Log;
use Config;
use Cache;
use Mail;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Help
 *
 * @author Administrator
 */
class Help {

    /**
     * 产生随机数
     */
    public static function randNum($length = 10) {
        $chars = '0123456789';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 获取真实IP
     * @return type
     */
    public static function realIp() {
        $realip = '';
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $realip = $_SERVER['REMOTE_ADDR'];
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            } else if (getenv('HTTP_CLIENT_IP')) {
                $realip = getenv('HTTP_CLIENT_IP');
            } else {
                $realip = getenv('REMOTE_ADDR');
            }
        }
        return $realip;
    }

    /**
     * 发送短信
     */
    public static function sendMsg($mobile, $appCode = '120012') {
        //检验获取手机号码       
        if (!$mobile) {
            return false;
        }

        $cacheKey = $mobile . "_msg";
        $count = Cache::get($cacheKey, 0);

        //半个小时内多余5条
        if ($count > 5) {
            return false;
        }

        $rand_key = Help::randNum(6);
        $content = "验证码是：{$rand_key}，有效期30分钟.";
        $content = iconv('UTF-8', 'gb2312', $content); //先转化ucs-2编码再转化为16进制
        $content = urlencode($content);
        $url = "http://q.hl95.com:8061/?username=新业务&password=xinyewu123&message={$content}&phone={$mobile}&epid={$appCode}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec($ch);
        curl_close($ch);
        if ($output == "00") {
            Log::info("短信发送成功：{$mobile}");
            session(['rand_key' => md5(md5($rand_key)), 'mobile' => $mobile, 'yzm_time' => time()]);
            session()->save();
            Cache::put($cacheKey, ++$count, 30);
            return true;
        }
        return false;
    }

    /**
     * 发送邮件
     * @return [type] [description]
     */
    public static function sendEmail($email, $data,$template='testmail') {
        ini_set('xdebug.max_nesting_level', 300);
        $data['email'] = $email;
        return Mail::send($template, $data, function($message) use($data) {
            $message->from(config('mail.username'), '员动力'); 
            $message->to($data['email'])->subject('佰仟员动力邮箱认证');
        });
    }

    /**
     * 检查手机验证码是否有效
     * @param type $mobile
     * @param type $code
     * @return boolean
     */
    public static function checkYzm($mobile, $code) {
        $res = true;
        Log::info(session('mobile'));
        if ($mobile != session('mobile')) {
            $res = false;
            Log::info("mobile");
        } elseif (md5(md5($code)) != session('rand_key')) {
            $res = false;
            Log::info("rand_key");
        } elseif (time() - session('yzm_time') > 300) {
            $res = false;
            Log::info("yzm_time");
        }
        return $res;
    }

    /**
     * 根据php的$_SERVER['HTTP_USER_AGENT'] 中各种浏览器访问时所包含各个浏览器特定的字符串来判断是属于PC还是移动端
     * @return  string
     */
    public static function deviceType() {
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : ''; //返回手机系统、型号信息
        if (stristr($user_agent, 'android')) {//返回值中是否有Android这个关键字
            return 'Android';
        } else if (stristr($user_agent, 'iphone')) {
            return 'iPhone';
        } else if (stristr($user_agent, 'windows phone')) {
            return 'Windows Phone';
        } else {
            return 'Others';
        }
    }

    /**
     * 根据日期计算年龄
     * @param type $birthday
     * @return boolean|int
     */
    public static function birthday($birthday) {
        $age = strtotime($birthday);
        if ($age === false) {
            return false;
        }
        list($y1, $m1, $d1) = explode("-", date("Y-m-d", $age));
        $now = strtotime("now");
        list($y2, $m2, $d2) = explode("-", date("Y-m-d", $now));
        $age = $y2 - $y1;
        if (intval($m2 . $d2) < intval($m1 . $d1)) {
            $age -= 1;
        }
        return $age;
    }

    /**
     * 等额本息计算
     * @param type $total
     * @param type $rate
     * @param type $periods
     */
    public static function averageCapitalPlusInterest($total, $rate, $periods = 12) {
        $month_rate = self::getMonthRateByYearRate($rate);
        $result = $total * $month_rate * pow(1 + $month_rate, $periods) / (pow(1 + $month_rate, $periods) - 1);
        return round($result, 2);
    }

    /**
     * 根据年率获取月利率
     * @param type $rate
     * @return type
     */
    public static function getMonthRateByYearRate($rate) {
        return $rate / 12 / 100;
    }

    /**
     * 生成带校验的url
     */
    public static function genUrl($baseUrl, $params = array()) {
        if (empty($params)) {
            return $baseUrl;
        }
        $tmpParam = $params;
        $token = Config::get('wx.token');
        sort($tmpParam, SORT_STRING);
        $tmpStr = sha1(implode($tmpParam) . $token);
        $params['signature'] = $tmpStr;
        return trim($baseUrl, '?') . '?' . http_build_query($params);
    }

    /**
     * 校验数据完整性
     */
    public static function urlCheck($url) {
        $tmpArr = parse_url($url);
        $args = self::convertUrlQuery($tmpArr['query']);
        if (!isset($args['signature'])) {
            return true;
        }
        $signature = $args['signature'];
        unset($args['signature']);
        $token = Config::get('wx.token');
        sort($args, SORT_STRING);
        $tmpStr = sha1(implode($args) . $token);
        if ($tmpStr == $signature) {
            return true;
        }
        return false;
    }

    public static function convertUrlQuery($query) {
        $queryParts = explode('&', $query);
        $params = array();
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = $item[1];
        }
        return $params;
    }

    //每月还款额处理(如果小数点后第二位数大于0，则小数点后第一位数字加1，小数点第二位改为0)
    public static function numRound($num){
        $arr = explode('.',$num);

        if(count($arr) == 2 && ($arr[1] > 0)){
            $lastNum2 = substr($num,-2,1)+1;
            $lastNum = 0;

            $dotBehindNum = $lastNum2.$lastNum;
            $arr2 = [$arr[0],$dotBehindNum];
            $num = implode('.',$arr2);

        }
        return $num;
    }

    //将YYYYMMDD格式转换为YYYY/MM/DD格式
    public static function dateChange($str = '19700101'){

        $arr = str_split($str);

        $year = $arr[0].$arr[1].$arr[2].$arr[3];
        $month = $arr[4].$arr[5];
        $date = $arr[6].$arr[7];

        $newStr = $year."/".$month."/".$date;

        return $newStr;
    }

    //qq邮箱转换为qq
    public static function qqEmail2qq($qq_email){
        return substr($qq_email,0,strpos($qq_email,'@'));
    }

    //通过城市编码查询出城市名称
    public static function findCity($itemno){
        $cityInfo = DB::table('sync_code_library')->where(['CODENO' => 'AreaCode','isinuse'=>1,'ITEMNO'=>$itemno])->select('ITEMNO','ITEMNAME')->first();
        if(is_object($cityInfo)){
            return $cityInfo->ITEMNAME;
        }else{
            return '';
        }
    }

    /**
     * 将二维数组内的某个值作为一维数组的键
     * @param unknown $array
     * @param unknown $key
     * @return unknown|array
     */
    public static function fixArray($array, $key)
    {
        if (empty($array)) {
            return $array;
        }
        foreach ($array as $s_value) {
            $s_value = (array) $s_value;
            $array_sell[$s_value[$key]] = $s_value;
        }
        return $array_sell;
    }
    
    /**
     * 将数组内某个方法的值作为数组的键
     * @param unknown $object
     * @param unknown $key
     */
    public static function fixObject($object, $key)
    {
        if (empty($object)) {
            return $object;
        }
        foreach ($object as $s_value) {
            $object_sell[$s_value->$key] = $s_value;
        }
        return $object_sell;
    }
    
    /**
     * 返回标准的json格式
     * @param unknown $status
     * @param string $massage
     * @param unknown $data
     */
    public static function json($status,$massage='',$data='')
    {
        return [
            'status'=>$status,
            'massage'=>$massage,
            'data'=>$data
        ];
    }
    
}
