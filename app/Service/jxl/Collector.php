<?php
namespace App\Service\jxl;

use App\Log\Facades\Logger;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

/**
 * Class JxlCollecter
 * @package app\Services
 */
class Collector
{
    /**
     * @var string 机构账号
     */
    protected static $orgAccount = 'baiqianjinrong';
    private static $tokenKey = 'jxl_token';
    /**
     * @var string 数据源列表
     */
    protected static $source_url = 'https://www.juxinli.com/orgApi/rest/v3/orgs/%s/datasources';
    /**
     * @var string 获取回执和token
     */
    protected static $auth_url = 'https://www.juxinli.com/orgApi/rest/v3/applications/%s';
    /**
     * @var string提交申请
     */
    protected static $apply_url = 'https://www.juxinli.com/orgApi/rest/v2/messages/collect/req';

    static $ERROR_NETWORK = ['code' => 101, 'message' => '接口异常'];

    /**
     * @param Request $request
     */
    public static function collectData($user_info = [])
    {
        Logger::info('-------------------一条不华丽的分割线------------------------','jxl');
        Logger::info('调用聚信立接口user_info:' . json_encode($user_info,JSON_UNESCAPED_UNICODE),'jxl');
        //return ['code'=>10008,'message'=>'success'];
        $receipt_data = static::getAccessToken($user_info);
        Logger::info('聚信立接口getAccessToken:' . json_encode($receipt_data,JSON_UNESCAPED_UNICODE),'jxl');
        if ($receipt_data == null) {
            Session::forget(static::$tokenKey);
            Session::save();
            return static::$ERROR_NETWORK;
        }
        //return ['code' => 100010, 'message' => $user_info];
        if ($receipt_data['success']) {
            $token = $receipt_data['data']['token'];
            $website = $receipt_data['data']['datasource']['website'];
            $apply_result = static::submitApply($user_info, $token, $website);
            Logger::info('聚信立接口submitApply:' . json_encode($apply_result,JSON_UNESCAPED_UNICODE),'jxl');
            if ($apply_result == null) {
                Session::forget(static::$tokenKey);
                Session::save();
                return static::$ERROR_NETWORK;
            }
            if (Arr::get($apply_result, 'success', false)) {
                Logger::info('聚信立接口formatMessage:' . json_encode(static::formatMessage($apply_result),JSON_UNESCAPED_UNICODE),'jxl');
                return static::formatMessage($apply_result);
            } else {
                Session::forget(static::$tokenKey);
                Session::save();
                Logger::info('聚信立接口apply_result:' . json_encode($apply_result,JSON_UNESCAPED_UNICODE),'jxl-error');
                return ['code' => static::$ERROR_NETWORK['code'], 'message' => $apply_result['message']];
            }
        } else {
            Session::forget(static::$tokenKey);
            Session::save();
            unset($receipt_data['success']);
            Logger::info('聚信立接口receipt_data:' . json_encode($receipt_data,JSON_UNESCAPED_UNICODE),'jxl-error');
            return $receipt_data;
        }
    }

    /**
     * @param $data
     * @return array
     */
    public static function formatMessage($data)
    {
        $deep_data = $data['data'];
        if ($deep_data['process_code'] == 10008||$deep_data['process_code'] == 30000) {
            Session::forget(static::$tokenKey);
            Session::save();
        }
        return ['code' => $deep_data['process_code'], 'message' => $deep_data['content']];
    }


    /**回执
     * @param $user_info
     */
    public static function getAccessToken($user_info)
    {
        $token = Session::get(static::$tokenKey);
        if (empty($token)||
            $token['user_info']['full_name']!=$user_info['full_name']||
            $token['user_info']['id_card']!=$user_info['id_card']||
            $token['user_info']['phone_number']!=$user_info['phone_number']
        ) {
            $token = static::generateToken($user_info);
            if ($token['success']) {
                $token = [
                    'success' => true,
                    'data' => [
                        'token' => $token['data']['token'],
                        'datasource' => [
                            'website' => $token['data']['datasource']['website']
                        ],
                    ],
                    'user_info'=>$user_info
                ];
                Session::put(static::$tokenKey, $token);
                Session::save();
            }
        }
        return $token;
    }

    /**获取回执
     * @param $user_info 用户信息
     * @return mixed
     */
    public static function generateToken($user_info)
    {
        $log='entry:'.json_encode($user_info);
        $t_d = json_encode([
            'selected_website' => [],
            "skip_mobile" => false,
            'basic_info' => [
                'name' => Arr::get($user_info, 'full_name', ''),
                'id_card_num' => Arr::get($user_info, 'id_card', ''),
                'cell_phone_num' => Arr::get($user_info, 'phone_number', ''),
                "cell_phone_num2" => "",
                "home_addr" => "",
                "work_tel" => "",
                "work_addr" => "",
                "home_tel" => ""
            ],
            'contacts' => []
        ]);
        return static::curlPost(static::$auth_url, $t_d);
        $r= static::curlPost(static::$auth_url, $t_d);
        $log.=';output:'.json_encode($r);
        Logger::info($log);
        return $r;
    }

    /**提交采集申请
     * @param $user_info 用户信息
     * @param $token 上一步生产的token
     * @param $website 网站名称
     * @return mixed
     */
    public static function submitApply($user_info, $token, $website)
    {
        //$log='entry:'.json_encode($user_info).';token:'.$token;
        $data = json_encode([
            "token" => $token,
            "account" => Arr::get($user_info, 'phone_number', ''),
            "password" => Arr::get($user_info, 'password', ''),
            "queryPwd" => Arr::get($user_info, 'queryPwd', ''),
            "captcha" => Arr::get($user_info, 'captcha', ''),
            "type" => '',//Arr::get($user_info, 'type', 'SUBMIT_CAPTCHA'),
            "website" => $website,
        ]);
        $r=static::curlPost(static::$apply_url, $data);
        //$log.=';output:'.json_encode($r);
        //Logger::info($log);
        return $r;
    }

    /**
     * @param $url
     * @return string
     */
    public static function formatUrl($url)
    {
        return sprintf($url, static::$orgAccount);
    }

    /**post请求数据
     * @param $url
     * @param string $post_data
     * @return mixed
     */
    public static function curlPost($url, $post_data = '')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, static::formatUrl($url));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($post_data)
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }

}