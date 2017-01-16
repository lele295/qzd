<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/10/26
 * Time: 10:50
 */

namespace App\Service\admin;

use App\Model\Base\JieqianmeConfigModel;
use App\Model\Base\SyncCodeLibraryModel;
use App\Model\mobile\BankCodeLikeModel;
use App\Util\Common;
use Illuminate\Support\Facades\Log;

class ConfigService extends Service
{
    /*
     * 更改短信配置
     */
    public function amend_sms_config($sms_name="it_sms", $arr){
        $config_m = new JieqianmeConfigModel();
        $check = $config_m->value($sms_name, "sms");
        $this->start_conn();
        $data["config_value"] = json_encode($arr);
        $res1 = $config_m->update_type_config();
        if($check){
            $data['is_enabled'] = 1;
            $res2 = $config_m->update_config_name($sms_name, $data);
        }else{
            $res1 = true;
            $data["config_name"] = $sms_name;
            $data["type"] = "sms";
            $res2 = $config_m->add_config($data);
        }

        $res = $this->end_conn(array($res1, $res2));
        return $res;
    }

    /*
     *银行卡号校验添加
     * return array
     */
    public function add_bank_like_verify($content){
        $res_arr = array();
        $filter_arr = Common::str_filter_arr($content);
        foreach($filter_arr as $item){
            if(isset($item[2])){
                $data['bank_name']= trim($item[2]);
                $library = SyncCodeLibraryModel::like_bank_Code($data['bank_name']);

                if($library){
                    $data['bank_mark'] = $item[0];
                    if ($item[1] == '信用卡') {
                        $data['type'] = 2;
                    } else {
                        $data['type'] = 1;
                    }

                    $data['bank_code'] = $library[0]->ITEMNO;
                    $data['bank_name']= $library[0]->ITEMNAME;
                    $verify = BankCodeLikeModel::verify_bank_like($data['bank_code'], $data['bank_mark']);
                    if(!$verify){
                        $affect = BankCodeLikeModel::add_bank_code($data);
                        if(!$affect){
                            $item['status'] = "失败";
                        }else{
                            continue;
                        }
                    }else{
                        $item['status'] = "已存在";
                    }
                }else{
                    $item['status'] = "未找到";
                }
                array_push($res_arr, $item);
            }
        }
        return $res_arr;
    }
}