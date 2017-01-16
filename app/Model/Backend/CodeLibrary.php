<?php

namespace App\Model\Backend;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Service\Help;

/**
 * Description of BackUser
 *
 * @author lenovo
 */
class CodeLibrary extends Model {

    protected $table = 'sync_code_library';
    
    /**
     * 通过CODENO查询
     * @param unknown $code_no
     */
    public static function getItemInfoByCodeNo($code_no)
    {
        $list=self::where('CODENO',$code_no)->get();
        return $list;
    }

    /**
     * 通过CODENO查询
     * @param unknown $code_no
     */
    public static function getItemInfoByCodeNoList($code_no)
    {
        $list=self::where('CODENO',$code_no)->lists('ITEMNAME', 'ITEMNO');
        return $list;
    }
    
    /**
     * 通过$field_name作为表字段名进行查询
     * @param unknown $field_name
     * @param unknown $value
     * @return unknown
     */
    public static function findByField($field_name, $value)
    {
        $list=self::where($field_name,$value)->get();
        return $list;
    }
    
    /**
     * 获取门店状态
     */
    public static function getRetailStoreStatus() {
        $cacheKey = md5('__retail_store_status__');
        $list = Cache::get($cacheKey);
        if (empty($list)) {
            $list=self::getItemInfoByCodeNo('RetailStoreStatus');
            if (!empty($list)) {
                $list = Help::fixObject($list, 'ITEMNO');
                Cache::put($cacheKey, $list, 1440);
            }
        }
        return $list;
    }

    /**
     * 获取合同状态
     */
    public static function getContractStatus() {
        $cacheKey = md5('__contract_status__');
        $list = Cache::get($cacheKey);
        if (empty($list)) {
            $list=self::getItemInfoByCodeNoList('ContractStatus');
            if (!empty($list)) {
                Cache::put($cacheKey, $list, 1440);
            }
        }
        return $list;
    }
}
