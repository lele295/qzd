<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/9/25
 * Time: 14:30
 */

namespace App\Service\base;


use App\Model\Base\SyncStoreInfoModel;
use App\Service\mobile\Service;

class StoreService extends Service
{
    //获取可用的门店
    public function get_userable_store_info($city){
        $syncStoreInfoModel = new SyncStoreInfoModel();
        $cityInfo = $syncStoreInfoModel->get_store_info_by_city($city);
        return $cityInfo;
    }
}