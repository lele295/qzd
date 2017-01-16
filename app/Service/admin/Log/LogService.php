<?php
namespace App\Service\admin\Log;
use App\Log\Facades\Logger;
use App\Model\Admin\ApiRecordLogModel;
use App\Service\admin\Service;
use App\Util\FileReader;
use Illuminate\Support\Facades\Config;

class LogService extends Service
{
    protected $title='';
    protected $action_at = '';

    public function get_api_record_log_file(){
        Logger::info('--------开始进行日志分析记录--------');
        $date = date('Y-m-d',strtotime('-1 days',time()));
        $path = '/logs/'.$date.'/'.'laravel-apache2handler-record-info-'.$date.'.log';
        $info = FileReader::read_storage_log_file($path);
        $array = array();
        foreach($info as $val){
            $record = $this->get_log_is_want_to_record($val);
            if($record){
                $array[$record['key']] = $this->get_key_default_val($array,$record['key']) + 1;
            }else{
                continue;
            }
        }
        $this->insert_api_log_record($array,$date);
        Logger::info('--------结束进行日志分析记录--------');
    }

    public function insert_api_log_record($array,$action_at){
        try {
            $apiRecordLog = new ApiRecordLogModel();
            $api_log = Config::get('logkeyword');
            $date = date('Y-m-d H:i:s', time());
            foreach ($array as $key => $val) {
                $info = array('title' => $key, 'remark' => $api_log[$key], 'number' => $val, 'created_at' => $date, 'updated_at' => $date, 'action_at' => $action_at);
                $apiRecordLog->insert_api_record_log($info);
            }
        }catch(\Exception $e){
            Logger::info('记录日志出现异常');
            throw $e;
        }
    }

    public function get_key_default_val($array,$key){
        if(array_key_exists($key,$array)){
            return $array[$key];
        }else{
            return 0;
        }
    }

    public function get_log_is_want_to_record($content){
        $info = Config::get('logkeyword');
        foreach($info as $key=>$val){
            if(strpos($content,$val)){
                return array('key'=>$key,'val'=>$val);
            }
            continue;
        }
        return '';

    }



}