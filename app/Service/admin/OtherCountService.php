<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/30
 * Time: 15:22
 */

namespace App\Service\admin;


use App\Model\Admin\OtherCountModel;

class OtherCountService extends Service{

    public function count_calculate(){
        $date = strtotime('-1 days',time());
        $date = '2015-12-28';
        $this->count_bank($date);
        $this->count_ca($date);
        $this->count_sign($date);
        $this->get_message_count($date);
    }

    public function count_bank($date){
        $file = fopen('D:\xampp\htdocs\jieqianme_v2\storage\logs\\'.$date.'\laravel-apache2handler-bank-info-record-'.$date.'.log','r') or die("Unable to open file!");
        $pass_count = 0;
        $fail_count = 0;
        while(!feof($file)) {
            $string = fgets($file);
            if(strpos($string,'银行卡认证不通过，需要更换银行卡')){
                $fail_count = $fail_count + 1;
            }else{
                $pass_count = $pass_count + 1;
            }
        }
        fclose($file);
        $otherCountModel = new OtherCountModel();
        $array = array(
            'count'=>$fail_count.'/'.$pass_count,
            'model'=>'bank',
            'text'=>'银行卡认证情况',
            'created_at'=>$date,
        );
        $otherCountModel->delete_other_count('bank',$date);
        $otherCountModel->insert_other_count($array);
    }
    //
    public function count_ca($date){
        $file = fopen('D:\xampp\htdocs\jieqianme_v2\storage\logs\\'.$date.'\laravel-apache2handler-ca-info-record-'.$date.'.log','r') or die("Unable to open file!");
        $success_count = 0;
        $error_count = 0;
        $ca_challenge_success_count = 0;
        $ca_challenge_error_count = 0;
        while(!feof($file)) {
            $string = fgets($file);
            if(strpos($string,'ca签名成功')){
                $success_count = $success_count + 1;
                continue;
            }
            if(strpos($string,'CA签名挑战码发送成功')){
                $ca_challenge_success_count = $ca_challenge_success_count +1;
                continue;
            }

        }
        fclose($file);
        $error_file = fopen('D:\xampp\htdocs\jieqianme_v2\storage\logs\\'.$date.'\laravel-apache2handler-ca-error-record-'.$date.'.log','r') or die("Unable to open file!");
        while(!feof($error_file)){
            $string = fgets($error_file);
            if(strpos($string,'ca签名失败')){
                $error_count = $error_count + 1;
                continue;
            }
            if(strpos($string,'CA签名挑战码发送失败')){
                $ca_challenge_error_count = $ca_challenge_error_count + 1;
                continue;
            }
        }
        fclose($error_file);
        $array = array(
            'count'=>$error_count.'/'.$success_count,
            'model'=>'ca',
            'text'=>'ca签名',
            'created_at'=>$date,
        );
        $ca_challenge_array = array(
            'count'=>$ca_challenge_error_count.'/'.$ca_challenge_success_count,
            'model'=>'ca_challenge',
            'text'=>'CA签名挑战码',
            'created_at'=>$date,
        );
        $otherCountModel = new OtherCountModel();
        $otherCountModel->delete_other_count('ca',$date);
        $otherCountModel->insert_other_count($array);
        $otherCountModel->delete_other_count('ca_challenge',$date);
        $otherCountModel->insert_other_count($ca_challenge_array);
    }
    //注册短信与挑战码短信统计
    public function count_sign($date){
        $file = fopen('D:\xampp\htdocs\jieqianme_v2\storage\logs\\'.$date.'\laravel-apache2handler-info-'.$date.'.log','r') or die("Unable to open file!");
        $sign_count = 0;
        $challenge_count = 0;
        while(!feof($file)){
            $string = fgets($file);
            if(strpos($string,'尊敬的客户，注册验证码为')){
                $sign_count = $sign_count + 1;
                continue;
            }
            if(strpos($string,'尊敬的客户，您的挑战码为')){
                $challenge_count = $challenge_count + 1;
                continue;
            }
        }
        fclose($file);
        $array = array(
            'count'=>$sign_count,
            'model'=>'sign_message',
            'text'=>'注册短信',
            'created_at'=>$date,
        );
        $array_two = array(
            'count'=>$challenge_count,
            'model'=>'challenge_message',
            'text'=>'挑战码短信',
            'created_at'=>$date,
        );
        $otherCountModel = new OtherCountModel();
        $otherCountModel->delete_other_count('sign_message',$date);
        $otherCountModel->insert_other_count($array);
        $otherCountModel->delete_other_count('challenge_message',$date);
        $otherCountModel->insert_other_count($array_two);
    }

    //发送审核结果通知短信
    public function get_message_count($date){
        $file = fopen('D:\xampp\htdocs\jieqianme_v2\storage\logs\\'.$date.'\laravel-cli-info-'.$date.'.log','r') or die("Unable to open file!");
        $count = 0;
        while(!feof($file)){
            $string = fgets($file);
            if(strpos($string,'发送信息为：尊敬的')){
                $count = $count +1;
            }
        }
        $otherCountModel = new OtherCountModel();
        $array = array(
            'count'=>$count,
            'model'=>'verify_message',
            'text'=>'审核短信',
            'created_at'=>$date,
        );
        $otherCountModel->delete_other_count('challenge_message',$date);
        $otherCountModel->insert_other_count($array);
    }

    public function get_count($path,$description){
        $file = fopen($path,'r') or die('Unable to open file!');
        $count = 0;
        while(!feof($file)){
            $string = fgets($file);
            if(strpos($string,$description)){
                $count = $count + 1;
            }
        }
        fclose($file);
        return $count;
    }

}