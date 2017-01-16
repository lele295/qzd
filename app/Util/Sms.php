<?php
/**
 * 发送短信验证码
 */
namespace App\Util;

use App\Log\Facades\Logger;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class Sms{

    private $url;
    public function __construct()
    {
        $this->url = Config::get('myconfig.sms_it_ip');
    }

    /*
	public function sendphonecode($mobile) {
      	$pool='0123456789';
     	$rand_key='';
      	for($i = 0;$i < 6;$i++){
       		$rand_key.=substr($pool, mt_rand(0,  strlen($pool)-1),1);
      	}

        $content = "您的验证码是：{$rand_key}，有效期30分钟.";
        $content =iconv('UTF-8', 'gb2312', $content);//先转化ucs-2编码再转化为16进制
        $content = urlencode($content);

        $ch = curl_init();
        $url = "http://q.hl95.com:8061/?username=qzd&password=Qzd217121&message=".$content."&phone=".$mobile."&epid=121712";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec($ch);
        Logger::info('hl95收发短信结果：'.$output,'pho-message');
        curl_close($ch);
      	if($output=="00")
       	{
            Session::put('rand_key', $rand_key);
            Session::save();
            return true;
       	}
        return false;
    }

    //挑战码发送
    public function send_tiaozhan_sms($content ,$mobile) {
        $pool='0123456789';
        $rand_key='';
        for($i = 0;$i < 6;$i++){
            $rand_key.=substr($pool, mt_rand(0,  strlen($pool)-1),1);
        }

        $content =iconv('UTF-8', 'gb2312', $content);//先转化ucs-2编码再转化为16进制
        $content = urlencode($content);

        $ch = curl_init();
        $url = "http://q.hl95.com:8061/?username=新业务&password=xinyewu123&message=".$content."&phone=".$mobile."&epid=120012";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec($ch);
        Logger::info('hl95收发短信结果（挑战码）：'.$output,'pho-message');
        curl_close($ch);
        if($output=="00")
        {
            Session::put('rand_key', $rand_key);
            Session::save();
            return true;
        }

        return false;
    }*/



    //短信平台
    /**/
    public function sendphonecode($mobile,$appcode = 'BQQZD'){

        $pool='0123456789';
        $rand_key='';
        for($i = 0;$i < 6;$i++){
            $rand_key.=substr($pool, mt_rand(0,  strlen($pool)-1),1);
        }

        $content = "您的验证码是：{$rand_key}，有效期30分钟.";
        //$content = iconv('UTF-8','GBk',$content);//原来的编码就是utf8，不需要转码
        $content = urlencode($content);
        $ch = curl_init();
        $url = $this->url."bqap_client/sendSms.do?appCode=".$appcode."&appType=2&phone=".$mobile."&message=".$content;
        Logger::info('短信平台收发短信地址：'.$url,'pho-message');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec($ch);
        Logger::info('短信平台收发短信结果：'.$output,'pho-message');
        curl_close($ch);
        $output = json_decode($output);

        if($output->code == "0")
        {
            Session::put('rand_key', $rand_key);
            return true;
        }

        return false;
    }

    public function send_tiaozhan_sms($content,$mobile,$appcode = 'BQQZD'){
        $pool='0123456789';
        $rand_key='';
        for($i = 0;$i < 6;$i++){
            $rand_key.=substr($pool, mt_rand(0,  strlen($pool)-1),1);
        }


        $content = urlencode($content);
        $ch = curl_init();
        $url = $this->url."bqap_client/sendSms.do?appCode=".$appcode."&appType=2&phone=".$mobile."&message=".$content;
        Logger::info('短信平台收发短信地址（挑战码）：'.$url,'pho-message');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec($ch);
        Logger::info('短信平台收发短信结果（挑战码）：'.$output,'pho-message');
        curl_close($ch);
        $output = json_decode($output);

        if($output->code == "0")
        {
            Session::put('rand_key', $rand_key);
            return true;
        }

        return false;
    }

}
