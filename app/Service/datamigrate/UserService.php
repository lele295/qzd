<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/10/8
 * Time: 14:09
 */

namespace App\Service\datamigrate;


use App\Model\Datamigrate\UsersModel;
use App\Service\mobile\Service;
use Illuminate\Support\Facades\Log;

class UserService extends Service
{
    public function get_user_migrate(){
        $userModel = new UsersModel();
        $data = $userModel->get_user_message();
        foreach ($data as $val) {
            $array['id'] = $val->id;
            $array['openid'] = $val->wechat_id;
            $array['mobile'] = $val->mobile;
            $array['password'] = $val->password;
            $array['realname'] = $val->real_name;
            $array['created_at'] = $val->created_at;
            $array['updated_at'] = $val->updated_at;
            $info = $userModel->insert_user_to_database($array);
            if(!$info){
                Log::info('用户导入出现错误:'.$val->id);
                continue;
            }else{
                Log::info('导入成功：'.$val->id);
            }
        }
        Log::info('用户数据导入成功');
    }
}