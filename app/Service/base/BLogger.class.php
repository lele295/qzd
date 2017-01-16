<?php

namespace App\Service\base;

use Illuminate\Support\Facades\Log;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Illuminate\Log\Writer;

class BLogger
{
    // 所有的LOG都要求在这里注册
    const LOG_ERROR = 'error';
    const LOG_INFO = 'info';
    const LOG_SHOPPING = 'shopping';
    const LOG_WECHAT = 'wechat';
    const LOG_ASAPI = 'saapi';
    const LOG_MALIPAY = "m_alipyorder";

    private static $loggers = array();

    // 获取一个实例
    public static function getLogger($type = self::LOG_ERROR, $day = 30)
    {
        if (empty(self::$loggers[$type])) {
            $dirpath = storage_path().'/logs/'.date('Y-m-d',time());
            if (!file_exists($dirpath)) {
                $old_mask = umask(0);
                if (!mkdir($dirpath, 02770, true)) {
                    Log::info("创建".date('Y-m-d',time())."日志目录失败");
                    return FALSE;
                }
                umask($old_mask);
            }
            self::$loggers[$type] = new Writer(new Logger($type));
            self::$loggers[$type]->useDailyFiles($dirpath.'/'. $type."-".php_sapi_name().'.log', $day);
        }

        $log = self::$loggers[$type];
        return $log;
    }
    
    public static function getYunlog($path,$filename,$info){ 
    	 $time1 = date("Y-m-d",time());
    	 $time2 = date("Y-m-d H:s:i",time());
    	 $res = error_log($time2." :".$info."\r\n",3, $path.$time1."_".$filename);
    	 if($res){
    	 	return true;
    	 }
    	 return false;
    	
    }
}