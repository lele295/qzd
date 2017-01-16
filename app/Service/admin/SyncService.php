<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/11/16
 * Time: 14:02
 */

namespace App\Service\admin;

use App\Model\Base\AsUserAuthModel;


class SyncService extends Service
{

    public function get_as_auth_user_count(){
        $asUserAuthModel = new AsUserAuthModel();
        $asUserAuthCount = $asUserAuthModel->get_user_auth_count();
        return $asUserAuthModel;
    }

    public function get_sync_bankput_info(){

    }
}