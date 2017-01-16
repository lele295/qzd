<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/1/20
 * Time: 14:49
 */

namespace App\Service\base;



use App\Log\Facades\Logger;
use App\Model\Base\SyncInsuranceCityInfoModel;
use App\Service\mobile\Service;

class InsuranceCityInfoService extends Service{
    static public function get_insurance_info_by_city($city){
        try {
            $syncInsuranceCityInfoModel = new SyncInsuranceCityInfoModel();
            $info = $syncInsuranceCityInfoModel->get_insurance_info_by_city_no($city, '1');
            return $info;
        }catch (\Exception $e){
            return '';
        }
    }
}