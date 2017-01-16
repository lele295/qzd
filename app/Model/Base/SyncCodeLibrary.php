<?php

namespace App\Model\Base;

use Illuminate\Database\Eloquent\Model;
use Cache;

class SyncCodeLibrary extends Model {

    protected $table = 'sync_code_library';

    /**
     * 选择过滤
     */
    private static function selectedFilter($whereStr, $orderStr = '1,2') {
        return self::whereRaw($whereStr)->orderByRaw($orderStr)->get();
    }

    /**
     * 是否
     */
    public static function yesNo() {
        return self::whereRaw("codeno='YesNo'")->orderByRaw('1,2')->get();
    }

    /**
     * 还款方式
     */
    public static function repaymentWay() {
        return self::whereRaw("codeno='RepaymentWay' and ITEMNO='1'")->orderByRaw('1,2')->get();
    }

    /**
     * 代扣/放款账号开户行
     */
    public static function bankCode() {
        return self::whereRaw("CodeNo = 'BankCode' and IsInUse = '1'")->orderByRaw('1,2')->get();
    }

    /**
     * 运作模式, 易百分系统只有普通非ALDI
     */
    public static function operatorModeApply() {
        return self::whereRaw("codeno = 'OperatorModeApply' and ITEMNO='03'")->orderByRaw('1,2')->get();
    }

    /**
     * 产品类型,易佰分系统只有学生消费贷
     */
    public static function subProductType() {
        return self::whereRaw("codeno like 'SubProductType' and ITEMNO='7'")->orderByRaw('1,2')->get();
    }

    /**
     * 参考代码
     */
    public static function interiorCode() {
        return self::whereRaw("codeno like 'InteriorCode'")->orderByRaw('1,2')->get();
    }

    /**
     * 性别
     */
    public static function sex() {
        return self::whereRaw("codeno='Sex' and ITEMNO in (1,2)")->orderByRaw('1,2')->get();
    }

    /**
     * 证件类型
     */
    public static function certType() {
        return self::whereRaw("codeno like 'CertType' and isinuse ='1'")->orderByRaw('1,2')->get();
    }

    /**
     * 与申请人关系
     */
    public static function familyRelative() {
        return self::whereRaw("codeno like 'FamilyRelativeMore2'")->orderByRaw('1,2')->get();
    }

    /**
     * 学习形式
     */
    public static function schoolLearning() {
        return self::whereRaw("codeno like 'school_learning' and ITEMNO in ('普通全日制','其他')")->orderByRaw('1,2')->get();
    }

    /**
     * 学籍状态
     */
    public static function schoolStatusStudent() {
        return self::whereRaw("codeno = 'school_status_student' and ITEMNO in ('注册学籍')")->orderByRaw('1,2')->get();
    }

    /**
     * 学历类别
     */
    public static function schoolDegreeCategory() {
        return self::whereRaw("codeno = 'school_Degree_category' and ITEMNO in ('普通','普通专升本','其他')")->orderByRaw('1,2')->get();
    }

    /**
     * 学制
     */
    public static function schoolLength() {
        return self::whereRaw("codeno like 'school_length' and ITEMNO in ('2年','3年','4年','5年','其他')")->orderByRaw('1,2')->get();
    }

    /**
     * 层次
     */
    public static function educationExperience() {
        return self::whereRaw("codeno = 'EducationExperience' and ITEMNO in (5,6,7)")->orderByRaw('1,2')->get();
    }

    /**
     * 每月收入来源
     */
    public static function sourceIncome() {
        return self::whereRaw("codeno = 'Source_income'")->orderByRaw('1,2')->get();
    }

    /**
     * 与其他联系人关系
     */
    public static function relationshipOther() {
        return self::whereRaw("codeno like 'RelationshipOther'")->orderByRaw('1,2')->get();
    }

    /**
     * 民族
     */
    public static function nationality() {
        return self::whereRaw("codeno like 'Nationality'")->orderByRaw('1,2')->get();
    }

    /**
     * 邮寄地址
     */
    public static function addNo() {
        return self::whereRaw("codeno = 'AddNo'")->orderByRaw('1,2')->get();
    }

    /**
     * 到市为止的省市地址编码
     * 这个使用过于频繁，对于数据库查询压力比较大，加入缓存
     */
    public static function areaCodeToCity() {

        $cacheKey = '__pro_city_cache_key__';
        $res = Cache::get($cacheKey);
        if (empty($res)) {
            $res = self::whereRaw("CodeNo='AreaCode' and IsInuse='1' and (length(SortNo)!=6 and substr(SortNo,1,2) not in ('11','12','31','50') or SortNo in ('11','12','31','50'))")->orderBy('ITEMNO', 'asc')->get();
            Cache::put($cacheKey, $res, 1440); //缓存一天
        }
        return $res;
    }

    /**
     * 到市为止的省市地址编码
     */
    public static function mapCodeToCity() {
        $cacheKey = '__pro_map_to_city_cache_key__';
        $list = Cache::get($cacheKey);
        if (empty($list)) {
            $list = self::whereRaw("CodeNo='AreaCode' and IsInuse='1' and (length(SortNo)!=6 and substr(SortNo,1,2) not in ('11','12','31','50') or SortNo in ('11','12','31','50'))")->orderBy('ITEMNO', 'asc')->lists('ITEMNAME', 'ITEMNO');
            Cache::put($cacheKey, $list, 14400); //缓存一天
        }
        return $list;
    }
    
    
    /**
     * 市、县区地址编码
     */
    public static function detailCodeToCity() {
        $cacheKey = '__pro_detail_to_city_cache_key__';
        $list = Cache::get($cacheKey);
        if (empty($list)) {
            $list = self::whereRaw("CodeNo='DetailAreaCode' and IsInuse='1'")->orderBy('ITEMNO', 'asc')->lists('ITEMNAME', 'ITEMNO');
            Cache::put($cacheKey, $list, 14400); //缓存一天
        }
        return $list;
    }
    
    /**
     * 省的信息
     */
    public static function provinceInfo() {
        return self::whereRaw("CodeNo = 'AreaCode' and IsInuse = '1' and ITEMNO like '%0000'")->select('SORTNO', 'ITEMNAME', 'ITEMNO')->get();
    }

    public static function where_areaCodeToCity($pro) {
        return self::whereRaw("CodeNo='AreaCode' and IsInuse='1' and (length(SortNo)!=6 and substr(SortNo,1,2) not in ('11','12','31','50') or SortNo in ('11','12','31','50')) and ITEMNO like '$pro%' ")->select('SORTNO', 'ITEMNAME', 'ITEMNO')->get();
    }

    /**
     *  根据no获取省
     */
    public static function detailProvinceInfo($item) {
        return self::whereRaw("CodeNo = 'AreaCode' and IsInuse = '1' and ITEMNO='" . $item . "'")->select('SORTNO', 'ITEMNAME', 'ITEMNO')->first();
    }

    /**
     *  根据no获取省的名字
     */
    public static function getProvinceName($item) {
        if (empty($item)) {
            return "";
        }
        $provice = self::whereRaw("CodeNo = 'AreaCode' and IsInuse = '1' and ITEMNO='" . $item . "'")->select('SORTNO', 'ITEMNAME', 'ITEMNO')->first();
        $name = $provice->ITEMNAME;
        $arr = array('省', '市', '区');
        $p_name = '';
        foreach ($arr as $v) {
            if (mb_strpos($name, $v, 0, "UTF-8") > 0 && mb_strpos($name, $v, 0, "UTF-8") != mb_strlen($name, "UTF-8") - 1) {
                $length = mb_strpos($name, $v, 0, "UTF-8");
                $p_name = mb_substr($name, 0, $length + 1, "UTF-8");
                break;
            }
        }
        return $p_name;
    }

    public static function detailCityInfo($item) {
        return self::whereRaw("CodeNo = 'DetailAreaCode' and IsInuse = '1' and ITEMNO='" . $item . "'")->select('SORTNO', 'ITEMNAME', 'ITEMNO')->first();
    }

    /**
     *  根据no获取银行名称
     */
    public static function bankNameInfo($item) {
        return self::whereRaw("CodeNo = 'BankPutCode' and IsInuse = '1' and ITEMNO='" . $item . "'")->select('SORTNO', 'ITEMNAME', 'ITEMNO')->first();
    }
    
    /**
     * 获取商户类型
     */
    public static function retailType(){
        return self::where('CodeNo','RetailType')->select('SORTNO', 'ITEMNAME', 'ITEMNO')->get();
    }

    /**
     * 城市分级
     */
    public static function classificationForCity() {
        $res = SyncCodeLibrary::areaCodeToCity();
        $firstLevel = array();
        $secondeLevel = array();
        foreach ($res as $item) {
            if (($item->ITEMNO % 10000) == 0) {
                array_push($firstLevel, array('name' => $item->ITEMNAME, 'value' => $item->ITEMNO, 'data' => array()));
            }
        }
    
        foreach ($res as $item) {
            if (($item->ITEMNO % 10000) == 0) {
                continue;
            }
            $parentValue = intval($item->ITEMNO / 10000) * 10000;
            foreach ($firstLevel as $key => $val) {
                if ($val['value'] == $parentValue) {
                    array_push($firstLevel[$key]['data'], array('name' => str_replace($val['name'], '', $item->ITEMNAME), 'val' => $item->ITEMNO));
                    break;
                }
            }
        }
        return $firstLevel;
    }
    
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
