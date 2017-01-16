<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/1/11
 * Time: 14:28
 */

namespace App\Service\base;


use App\Model\Admin\ConnectTimeModel;
use App\Service\mobile\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ConnectTimeService extends Service{

    public function init_connect_time($init = true){
        if(!$init){
            $connectTimeModel = new ConnectTimeModel();
            $info = $connectTimeModel->get_connect_time();
            $expiresAt = Carbon::now()->addHours(24);
            Cache::put('connect_time',$info,$expiresAt);
        }else{
            if(!Cache::has('connect_time')){
                $connectTimeModel = new ConnectTimeModel();
                $info = $connectTimeModel->get_connect_time();
                $expiresAt = Carbon::now()->addHours(24);
                Cache::put('connect_time',$info,$expiresAt);
            }
            $data = Cache::get('connect_time');
            return $data;
        }
    }


    public function get_connect_time(){
        $time = time();
        $now_time = str_replace(':','',date('H:i:s',time()));
        $info = $this->init_connect_time();
        $deal_date = '';
        if(empty($info)){
            return '';
        }
        foreach ($info as $val) {
            $val->start_time = str_replace(':','',$val->start_time);
            $val->end_time = str_replace(':','',$val->end_time);
            if($now_time >= $val->start_time && $now_time <= $val->end_time){
                $deal_date = $val;
                break;
            }else{
                continue;
            }
        }
        if(empty($deal_date)){
            return date('Y/m/d H:i:s',strtotime('+5 minutes',$time));
        }else{
            $date = date('Y-m-d',$time).' '.$deal_date->action_time;
            return date('Y/m/d H:i:s',strtotime('+'.$deal_date->deal_day.' days',strtotime($date)));
        }
    }

    public function get_connect_time_with_default(){
        $time = $this->get_connect_time();
        if(empty($time)){
            $now_time = $this->get_validate_date();
            return $now_time;
        }else{
            return $time;
        }
    }

    public function get_validate_date(){
        $time = time();
        $date = date('Y-m-d',$time);

        $year_start_time = strtotime('2016-02-06 22:30:00');
        $year_end_time = strtotime('2016-02-13 22:30:00');
        if($time >= $year_start_time && $time <= $year_end_time){
            return $this->get_new_year_date();
        }else{
            $normal_start_time = strtotime($date.' 09:00:00');
            $normal_end_time = strtotime($date.' 22:30:00');
            $eleven_time = strtotime($date.' 23:00:00');
            $zero_time = strtotime($date.' 23:59:59');
            $tomorr_time = strtotime($date.' 00:00:00');
            $five_time = strtotime($date.' 05:00:00');

            if($time >= $normal_start_time && $time < $normal_end_time){
                return date('Y/m/d H:i:s',strtotime('+5 minutes',$time));
            }
            if($time >= $normal_end_time && $time < $eleven_time){
                $send_time = $date.' 09:45:00';
                return date('Y/m/d H:i:s',strtotime('+1 days',strtotime($send_time)));
            }
            if($time >= $eleven_time && $time <= $zero_time){
                $send_time = $date.' 10:15:00';
                return date('Y/m/d H:i:s',strtotime('+1 days',strtotime($send_time)));
            }
            if($time >= $tomorr_time && $time < $five_time){
                $send_time = $date.' 14:01:00';
                return date('Y/m/d H:i:s',strtotime($send_time));
            }
            if($time >= $five_time && $time < $normal_start_time){
                $send_time = $date.' 14:30:00';
                return date('Y/m/d H:i:s',strtotime($send_time));
            }
        }
    }

    public function get_new_year_date()
    {
        $time = time();
        $date = date('Y-m-d',$time);
        $zero_time =  strtotime($date.' 00:00:00');
        $nice_time = strtotime($date.' 09:30:00');
        $nineteen_time = strtotime($date.' 19:30:00');
        $twenty_two = strtotime($date.' 22:59:59');
        $twenty_three = strtotime($date.' 23:59:59');
        if($time >= $zero_time && $time < $nice_time){
            $send_time = $date.' 14:30:00';
            return date('Y/m/d H:i:s',strtotime($send_time));
        }
        if($time >= $nice_time && $time < $nineteen_time){
            return date('Y/m/d H:i:s',strtotime('+5 minutes',$time));
        }

        if($time >= $nineteen_time && $time < $twenty_two){
            $send_time = $date.' 10:45:00';

            return date('Y/m/d H:i:s',strtotime('+1 days',strtotime($send_time)));
        }
        if($time >= $twenty_two && $time <= $twenty_three){
            $send_time = $date.' 14:15:00';
            return date('Y/m/d H:i:s',strtotime('+1 days',strtotime($send_time)));
        }
    }
}