<?php

namespace App\Service;

use App\Log\Facades\Logger;

class Curl {

    /**
     * 为安硕的application/x-www-form-urlencoded请求单独构造的post请求
     */
    static public function curlPostForAs($url, $postData) {
        $header = array(
            'Content-Type: application/x-www-form-urlencoded',
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);


        $SSL = substr($url, 0, 8) == "https://" ? true : false;
        if ($SSL) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
        }

        //设置请求为post类型
        curl_setopt($ch, CURLOPT_POST, TRUE);
        //添加post类型
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        //执行请求，获得回复
        $r = curl_exec($ch);

        return $r;
    }

    /*
     * curl发送post请求接收返回的数据但不输出
     * 第一个形参变量是请求地址url,其类型为字符串
     * 第二个形参变量是通过post方式提交的数据,其类型为数组
     * 返回请求处理的结果,其类型为对象数组
     * @param encode 把编码装换成utf8
     */

    static public function curlPost($url, $postDate, $type = false, $code = 'origin', $encode = false) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        $SSL = substr($url, 0, 8) == "https://" ? true : false;
        if ($SSL) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
        }

        //设置请求为post类型
        curl_setopt($ch, CURLOPT_POST, TRUE);
        //添加post类型
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postDate);
        //执行请求，获得回复
        $r = curl_exec($ch);
        if ($encode) {
            $check = mb_detect_encoding($r, array('ASCII', 'GB2312', 'GBK'));
            $r = iconv($check, 'UTF-8', $r);
        }
        curl_close($ch);
        switch ($code) {
            case 'json' :
                if ($type) {
                    return json_decode($r, true);
                }
                return json_decode($r);
                break;
            case 'origin' :
                return $r;
                break;
        }
        return null;
    }

    /*
     * curl发送post请求接收返回的数据但不输出
     * param url
     * param array('key1'=>'value1','key2'=>'value2',...)
     * @param bool $encode 编码装换成utf8
     */

    static public function curlGet($url, $getDate = '', $encode = false) {
        $ch = curl_init();
        if (!empty($getDate) && is_array($getDate)) {
            $i = 0;
            foreach ($getDate as $k => $v) {
                ++$i;
                if ($i == 1) {
                    $url = ($url . '?' . $k . '=' . $v);
                } else {
                    $url .= ('&' . $k . '=' . $v);
                }
            }
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); //超时

        $SSL = substr($url, 0, 8) == "https://" ? true : false;
        if ($SSL) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
        }
        //执行请求，获得回复
        $r = curl_exec($ch);
        if ($encode) {
            $check = mb_detect_encoding($r, array('ASCII', 'GB2312', 'GBK', 'UTF-8'));
            $r = iconv($check, 'UTF-8', $r);
        }
        curl_close($ch);
        return $r;
    }

    //curl get获取消息 ,简单，但无编码转换，如果出现乱码，则不要用这个
    public static function curl_get($url = "") {
        if (!$url) {
            return false;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); //设定为不验证证书和host
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = json_decode(curl_exec($ch));
        curl_close($ch);
        return $output;
    }

    //curl post获取消息,简单，但无编码转换，如果出现乱码，则不要用这个
    public static function curl_post($url = "", $data = "") {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); //设定为不验证证书和host
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $SSL = substr($url, 0, 8) == "https://" ? true : false;
        if ($SSL) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
        }

        $res = curl_exec($ch);

        if($res === false) {
            Logger::info('Curl error: ' . curl_error($ch),'curl');
        } else {
            //Logger::info('操作完成没有任何错误','curl');
        }
        return $res;
    }



}
