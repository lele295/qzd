<?php
namespace App\Service\Log;
use App\Log\Facades\Logger;
use App\Service\mobile\Service;
use App\Util\FileReader;

/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/4/26
 * Time: 15:10
 */
class LoggerService extends Service
{
    public function read_log_file(){
        $path = FileReader::get_log_path();
        $date = date('Y-m-d',time());
  //      $path = $path.'/'.$date.'/laravel-apache2handler-yunwei-error-'.$date.'.log';
        $path = $path.'/'.$date.'/laravel-apache2handler-info-'.$date.'.log';
        $count = 0;
        if(file_exists($path)){
            $handle = fopen($path,'r');
            if($handle){
                while(!feof($handle)){
                    $info = fgets($handle);
                    Logger::info($info,'test');
                    $count++;
                    if($count > 15){
                        break;
                    }
                }
            }
            fclose($handle);
        }else{
            return;
        }
    }

    public function log_content_analyze($content){

    }

    public function get_key_word(){

    }
}