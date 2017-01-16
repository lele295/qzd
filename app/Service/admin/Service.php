<?php

namespace App\Service\admin;
use Illuminate\Support\Facades\DB;

class Service
{
    public function start_conn(){
        DB::beginTransaction();
    }

    //对数据库进行提交或是回滚
    public function end_conn($array= array()){
        if(in_array(false,$array) || in_array(0,$array)){
            DB::rollback();
            return false;
        }else{
            DB::commit();
            return true;
        }
    }

    /*
     * 解析进度状态
     */
    public function parse_step_status($step_status)
    {
        switch($step_status){
            case "101":
                $name = "填写贷款";
            break;
            case "102":
                $name = "个人资料";
            break;
            case "103":
                $name = "单位资料";
            break;
            case "104":
                $name = "上传图片";
            break;
            case "108":
                $name = "签署协议";
            break;
            case "109":
                $name = "Ca认证";
            break;
            case "201":
                $name = "已提单";
            break;
            case "200":
                $name = "重新填写贷款";
            break;
            default:
                $name = "未知状态";
            break;
        }
        return $name;
    }
}