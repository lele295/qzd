<?php
/**
 * Class AsKits
 * @desc 安硕工具类
 */
namespace App\Service\base;
class AsKits{
    /**
     * @param $idCard
     * @desc 根据身份证判断性别,偶数是女，基数是男
     */
    static public function sex($idCard){

    }

    /**
     * @desc 判断是否下单成功
     * @param $arr
     * @return bool
     */
    static public function submitModelOk($arr){
        if(isset($arr['RequestStatus']) && ($arr['RequestStatus']=='1')){
            return $arr;
        }
        return false;
    }

    static public function uploadImageOk($arr){
        if(isset($arr['data']) && ($arr['data'][0]['Status']=='Success')){
            return $arr;
        }
        return false;
    }
}