<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/2/22
 * Time: 11:10
 */

namespace App\Service\base;
use App\Log\Facades\Logger;
use App\Model\Base\UserModel;
use App\Service\mobile\Service;
use Illuminate\Support\Facades\DB;

/**
 * 一次性代码
 * Class OnceService
 * @package App\Service\base
 */

class OnceService extends Service{
    public function set_user_group(){
        UserModel::where('group','3')->chunk(1000,function($user){
            $this->chunk_user_is_exit($user);
        });
    }

    public function chunk_user_is_exit($user){
        $userModel = new UserModel();
        foreach($user as $val){
            $info = DB::connection('mysql-2')->table('IND_INFO')->select('CERTID','MOBILETELEPHONE','CUSTOMERNAME')->where('MOBILETELEPHONE','=',$val->mobile)->first();
            Logger::info('写入分组数据：');
            Logger::info((array)$info);
            if($info){
                $userModel->update_user_info_by_id($val->id,array('group'=>'2','mark'=>$info->CUSTOMERNAME.':'.$info->CERTID.','));
          //      UserModel::where('id','=',$val->id)->update(array('group'=>'2','mark'=>$info->CUSTOMERNAME.':'.$info->CERTID.','));
            }
        }
    }

}