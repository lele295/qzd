<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/2/2
 * Time: 15:19
 */

namespace App\Util;


use App\Log\Facades\Logger;
use App\Model\Base\SyncBankputInfoModel;
use App\Model\Base\SyncCodeLibraryModel;
use App\Model\Base\SyncModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class CodeLibrary {

    static public function init_marriage(){
        $info = SyncCodeLibraryModel::marriage();
        $array = array();
        $array_origin = array();
        foreach($info as $val){
            $array_origin = array_add($array_origin,$val->ITEMNO,$val->ITEMNAME);
            array_push($array,array('name'=>$val->ITEMNAME, 'id'=>$val->ITEMNO));
        }
        $array_target = array();
        $array_target['display'] = $array;
        $array_target['origin'] = $array_origin;
        $expire = Carbon::now()->addMinute(15);
        Cache::put('marriage',$array_target,$expire);
    }

    /**
     * 婚姻状况
     * @return string
     */
    static public function marriage(){
        if(!Cache::has('marriage')){
            self::init_marriage();
        }
        $info = Cache::get('marriage');
        return $info['display'];
    }


    static public function get_marriage_name_by_code($code){
        if(!Cache::has('marriage')){
            self::init_marriage();
        }
        $info = Cache::get('marriage');
        $origin = $info['origin'];
        if(!isset($origin[$code])){
            return "请选择";
        }
        return $origin[$code];
    }

    /**
     * 亲属关系
     */
    static public function init_family_relative(){
        $info = SyncCodeLibraryModel::familyRelative();
        $array = array();
        $array_origin = array();
        foreach($info as $val){
            $array_origin = array_add($array_origin,$val->ITEMNO,$val->ITEMNAME);
            array_push($array,array('name'=>$val->ITEMNAME, 'id'=>$val->ITEMNO));
        }
        $array_target = array();
        $array_target['display'] = $array;
        $array_target['origin'] = $array_origin;
        $expire = Carbon::now()->addMinute(15);
        Cache::put('family_relative',$array_target,$expire);
    }

    /*
     * 亲属关系
     */
    static public function family_relative(){
 //       Cache::forget('family_relative');
        if(!Cache::has('family_relative')){
            self::init_family_relative();
        }
        $info = Cache::get('family_relative');
        return $info['display'];
    }

    static public function get_family_relative_name_by_code($code){
        if(!Cache::has('family_relative')){
            self::init_family_relative();
        }
        $info = Cache::get('family_relative');
        $origin = $info['origin'];
        if(array_key_exists($code,$origin)){
            return $origin[$code];
        }else{
            return '';
        }
    }

    static public function init_yes_no_for_address(){
        $array = array();
        $array_display = array();
        $array = array_add($array,'1','同户籍地址');
        $array = array_add($array,'2','非同户籍地址');
        array_push($array_display,array('name'=>'同户籍地址', 'id'=>'1'));
        array_push($array_display,array('name'=>'非同户籍地址', 'id'=>'2'));
        $array_target = array();
        $array_target['display'] = $array_display;
        $array_target['origin'] = $array;
        $expire = Carbon::now()->addMinute(15);
        Cache::put('yes_no_for_address',$array_target,$expire);
    }

    /*
     * 现居住地址选项
     * @return string
     */
    static public function yes_no_for_address(){
        if(!Cache::has('yes_no_for_address')){
            self::init_yes_no_for_address();
        }
        $info = Cache::get('yes_no_for_address');
        return $info['display'];
    }

    static public function get_yes_no_for_address_name($key){
        if(!Cache::has('yes_no_for_address')){
            self::init_yes_no_for_address();
        }
        $info = Cache::get('yes_no_for_address');
        $target = $info['origin'];
        if(array_key_exists($key,$target)){
            return $target[$key];
        }else{
            return '';
        }
    }

    
    static public function init_bank_code(){
        $info = SyncCodeLibraryModel::bankCode();
        $array = array();
        $array_origin = array();
        foreach($info as $val){
            $array_origin = array_add($array_origin,$val->ITEMNO,$val->ITEMNAME);
            array_push($array,array('id'=>$val->ITEMNO,'name'=>$val->ITEMNAME));
        }
        $array_target['display'] = $array;
        $array_target['origin'] = $array_origin;
        $expire = Carbon::now()->addMinute(15);
        Cache::put('bank_code',$array_target,$expire);
    }

    /*
     * 代扣/放款账号开户行
     * @return string
     */
    static public function bank_code(){
        if(!Cache::has('bank_code')){
            self::init_bank_code();
        }
        $info = Cache::get('bank_code');
        return $info['display'];
    }

    static public function get_bank_name_by_code($code){
        if(!Cache::has('bank_code')){
            self::init_bank_code();
        }
        $info = Cache::get('bank_code');
        $target = $info['origin'];
        if(!array_key_exists($code,$target)){
            return "";
        }
        return $target[$code];
    }


    /**
     * 获取支行信息
     * @data-param $bankCode 银行代码
     * @data-param $city 城市代码
     */
    static public function get_bank_branch($bank_code,$city){
        $info = SyncCodeLibraryModel::bankBranch($city,$bank_code);
        $array = array();
        foreach($info as $val){
            $array = array_add($array,$val->ITEMNO,$val->ITEMNAME);
        }
        return json_encode($array);
    }

    static public function get_bank_put_info(){
        $bank_validate = self::bank_code();
        $bank_array = array();
        foreach($bank_validate as $item){
            array_push($bank_array,$item['id']);
        }
        $bank_put_info = array();
        SyncBankputInfoModel::whereIn('BANACODE',$bank_array)->whereNotNull('CITY')->chunk(500,function($info)use(&$bank_put_info){
            foreach($info as $value){
                $val_array = array('BANKNO'=>$value->BANKNO,'BANKNAME'=>$value->BANKNAME);
                $key = $value->CITY.'-'.$value->BANACODE;
                if(array_key_exists($key,$bank_put_info)){
                    $array = $bank_put_info[$key];
                    array_push($array,$val_array);
                    $bank_put_info[$key] = $array;
                }else{
                    $bank_put_info = array_add($bank_put_info,$key,$val_array);
                }
            }
        });
        return $bank_put_info;
    }


    static public function init_add_no(){
        $info = SyncCodeLibraryModel::addNo();
        $array = array();
        $array_origin = array();
        foreach($info as $val){
            array_push($array, array('name'=>$val->ITEMNAME, 'id'=>$val->ITEMNO));
            $array_origin = array_add($array_origin,$val->ITEMNO,$val->ITEMNAME);
        }
        $array_target = array();
        $array_target['origin'] = $array_origin;
        $array_target['display'] = $array;
        $expire = Carbon::now()->addMinute(15);
        Cache::put('add_no',$array_target,$expire);
    }

    /*
     *获取邮寄地址选项列表内容
     * @return mixed
     */
    static public function add_no(){
    //    Cache::forget('add_no');
        if(!Cache::has('add_no')){
            self::init_add_no();
        }
        $info = Cache::get('add_no');
        return $info['display'];
    }

    /*
     * 根据邮寄地址获取相关的名称描述
     */
    static public function get_add_name_by_add_no($add_no){
        if(!Cache::has('add_no')){
            self::init_add_no();
        }
        $info = Cache::get('add_no');
        $origin = $info['origin'];

        if(!array_key_exists($add_no,$origin)){
            return "";
        }
        return $origin[$add_no];
    }


    /**
     * 初始化贷款目地
     */
    static public function init_cash_purpose(){
        $info = SyncCodeLibraryModel::cashPurpose();
        $array_display = array();
        $array_target = array();
        foreach($info as $val){
            array_push($array_display,array('name'=>$val->ITEMNAME, 'id'=>$val->ITEMNO));
            $array_target = array_add($array_target,$val->ITEMNO,$val->ITEMNAME);
        }
        $array = array();
        $array['display'] = $array_display;
        $array['origin'] = $array_target;
        $expire = Carbon::now()->addMinute(15);
        Cache::put('cash_purpose',$array,$expire);
    }

    /**
     * 获取货款目地列表
     * @return mixed
     */
    static public function cash_purpose(){
        if(!Cache::has('cash_purpose')){
            self::init_cash_purpose();
        }
        $info = Cache::get('cash_purpose');
        return $info['display'];
    }

    /**
     * 根据key来获取货款目的的描述
     * @param $code
     * @return string
     */
    static public function get_cash_purpose_name_code($code){
         if(!Cache::has('cash_purpose')){
             self::init_cash_purpose();
         }
        $info = Cache::get('cash_purpose');
        $origin = $info['origin'];
        if(!array_key_exists($code,$origin)){
            return '';
        }
        return $origin[$code];
    }

    static public function init_house(){
        $info = SyncCodeLibraryModel::familyStatus();
        $array = array();
        $array_display = array();
        $array_target = array();
        foreach($info as $val){
            array_push($array_display,array('name'=>$val->ITEMNAME, 'id'=>$val->ITEMNO));
            $array_target = array_add($array_target,$val->ITEMNO,$val->ITEMNAME);
        }
        $array['display'] = $array_display;
        $array['origin'] = $array_target;
        $expire = Carbon::now()->addMinute(15);
        Cache::put('house_array',$array,$expire);
    }

    /**
     * 初始化住房信息
     * @return mixed
     */
    static public function house(){
        if(!Cache::has('house_array')){
          self::init_house();
        }
        $info = Cache::get('house_array');
        return $info['display'];
    }

    static public function get_house_by_code($code){
        if(!Cache::has('house_array')){
            self::init_house();
        }
        $info = Cache::get('house_array');
        $origin = $info['origin'];
        if(!array_key_exists($code,$origin)){
            return '';
        }
        return $origin[$code];
    }

    static public function init_edu_experience(){
        $info = SyncCodeLibraryModel::educationExperience();
        self::get_array($info,'init_edu_experience');
    }

    static public function get_edu_experience(){
        if(!Cache::has('init_edu_experience')){
            self::init_edu_experience();
        }
        $info = Cache::get('init_edu_experience');
        return $info['display'];
    }

    static public function get_edu_experience_name_by_code($code){
        if(!Cache::has('init_edu_experience')){
            self::init_edu_experience();
        }
        $info = Cache::get('init_edu_experience');
        $target = $info['origin'];
        if(!array_key_exists($code,$target)){
            return '';
        }
        return $target[$code];
    }

    static public function get_array($info,$name){
        $array = array();
        $array_display = array();
        $array_target = array();
        foreach($info as $val){
            array_push($array_display,array('name'=>$val->ITEMNAME, 'id'=>$val->ITEMNO));
            $array_target = array_add($array_target,$val->ITEMNO,$val->ITEMNAME);
        }
        $array['display'] = $array_display;
        $array['origin'] = $array_target;
        $expire = Carbon::now()->addMinute(15);
        Cache::put($name,$array,$expire);
    }

    static public function init_job_time(){
        $info = SyncCodeLibraryModel::workDate();
        self::get_array($info,'init_job_time');
    }

    static public function get_job_time(){
        if(! Cache::has('init_job_time')){
            self::init_job_time();
        }
        $info = Cache::get('init_job_time');
        return $info['display'];
    }

    static public function get_job_time_name_by_code($code){
        if(!Cache::get('init_job_time')){
            self::init_job_time();
        }
        $info = Cache::get('init_job_time');
        $target = $info['origin'];
        if(!array_key_exists($code,$target)){
            return '';
        }
        return $target[$code];
    }

    static public function init_job_total(){
        $info = SyncCodeLibraryModel::workExperence();
        self::get_array($info,'init_job_total');
    }

    static public function get_job_total(){
        if(!Cache::has('init_job_total')){
            self::init_job_total();
        }
        $info = Cache::get('init_job_total');
        return $info['display'];
    }

    static public function get_job_total_name_by_code($code){
        if(!Cache::has('init_job_total')){
            self::init_job_total();
        }
        $info = Cache::get('init_job_total');
        $target = $info['origin'];
        if(!array_key_exists($code,$target)){
            return '';
        }
        return $target[$code];
    }

    static public function init_yes_no(){
        $array = array();
        $array_display = array();
        $array = array_add($array,'1','是');
        $array = array_add($array,'2','否');
        array_push($array_display,array('name'=>'是', 'id'=>'1'));
        array_push($array_display,array('name'=>'否', 'id'=>'2'));
        $array_target = array();
        $array_target['display'] = $array_display;
        $array_target['origin'] = $array;
        $expire = Carbon::now()->addMinute(60);
        Cache::put('yes_no_flag',$array_target,$expire);
    }

    static public function get_yes_no(){
        if(!Cache::has('yes_no_flag')){
            self::init_yes_no();
        }
        $info = Cache::get('yes_no_flag');
        return $info['display'];
    }

    static public function get_yes_no_name_by_code($code){
        if(!Cache::has('yes_no_flag')){
            self::init_yes_no();
        }
        $info = Cache::get('yes_no_flag');
        $target = $info['origin'];
        if(!array_key_exists($code,$target)){
            return '';
        }
        return $target[$code];
    }

    static public function init_contact_relation(){
        $info = SyncCodeLibraryModel::relativeAccountOther();
        self::get_array($info,'init_contact_relation');
    }

    static public function get_contact_relation(){
        $cache = self::get_cache_by_key('init_contact_relation','init_contact_relation');
        return $cache['display'];
    }

    static public function get_contact_relation_name_by_code($code){
        $val = self::get_cache_detail_val_by_key_code('init_contact_relation','init_contact_relation',$code);
        return $val;
    }

    static public function init_cert_type(){
        $info = SyncCodeLibraryModel::certType();
        self::get_array($info,'init_cert_type_key');
    }

    static public function get_cert_type(){
        $cache = self::get_cache_by_key('init_cert_type_key','init_cert_type');
        return $cache['display'];
    }

    static public function get_cert_type_name_by_code($code){
        $val = self::get_cache_detail_val_by_key_code('init_cert_type_key','init_cert_type',$code);
        return $val;
    }

    static public function get_cache_by_key($key,$function){
        if(!Cache::has($key)){
            self::call_function($function);
        }
        $info = Cache::get($key);
        return $info;
    }

    static public function get_cache_detail_val_by_key_code($cache_key,$function,$code){
        if(!Cache::has($cache_key)){
            self::call_function($function);
        }
        $info = Cache::get($cache_key);
        $target = $info['origin'];
        if(!array_key_exists($code,$target)){
            return '';
        }
        return $target[$code];
    }

     static public function call_function($function,$code=NULL){
        if(!$code){
            call_user_func(array('App\Util\CodeLibrary',$function));
        }else{
            if(!is_array($code)){
               throw new \Exception;
            }
            call_user_func_array(array('App\Util\CodeLibrary',$function),$code);
        }
    }

    static public function init_head_ship()
    {
        $info = SyncCodeLibraryModel::duties();
        self::get_array($info, 'init_head_ship_key');
    }

    static public function get_head_ship(){
       $cache = self::get_cache_by_key('init_head_ship_key','init_head_ship');
       return $cache['display'];
    }

    static public function get_head_ship_name_by_code($code){
        $val = self::get_cache_detail_val_by_key_code('init_head_ship_key','init_head_ship',$code);
        return $val;
    }

    /**
     * 单位性质
     */
    static public function init_cell_property(){
        $info = SyncCodeLibraryModel::orgAttribute();
        self::get_array($info,'init_cell_property_key');
    }

    static public function get_cell_property(){
        $info = self::get_cache_by_key('init_cell_property_key','init_cell_property');
        return $info['display'];
    }

    static public function get_cell_property_name_by_code($code){
        $val = self::get_cache_detail_val_by_key_code('init_cell_property_key','init_cell_property',$code);
        return $val;
    }

    static public function init_unit_kind(){
        $info = SyncCodeLibraryModel::unitKind();
        self::get_array($info,'init_unit_kind_key');
    }

    static public function get_unit_kind(){
        $cache = self::get_cache_by_key('init_unit_kind_key','init_unit_kind');
        return $cache['display'];
    }

    static public function get_unit_kind_name_by_code($code){
        $val = self::get_cache_detail_val_by_key_code('init_unit_kind_key','init_unit_kind',$code);
        return $val;
    }

    static public function get_bank_branch_name_by_code($code){
        $syncBankputInfoModel = new SyncBankputInfoModel();
        $info = $syncBankputInfoModel->get_bank_branch_name_by_code($code);
        if($info){
            return $info->BANKNAME;
        }else{
            return '';
        }
    }

    static public function init_city(){
        $info = SyncCodeLibraryModel::areaCode();
        self::get_array($info,'init_city_key');
    }

    static public function get_city_name_by_code($code){
        $val = self::get_cache_detail_val_by_key_code('init_city_key','init_city',$code);
        return $val;
    }

    /**
     * 初始化取消原因
     */
    static public function init_cancel_reason(){
        $info = SyncCodeLibraryModel::cancelReason();
        $array_origin = array();
        foreach($info as $value){
            $array_origin = array_add($array_origin,$value->ITEMNO,$value->ITEMNAME);
        }
        $expire = Carbon::now()->addMinute(15);
        Cache::put('cancelReason',$array_origin,$expire);
    }

    /**
     * 根据码值获取原因
     * @param $code
     * @return mixed
     */
    static public function get_cancel_reason_by_code($code){
        if(!Cache::has('cancelReason')){
            self::init_cancel_reason();
        }
        $info = Cache::get('cancelReason');
        return isset($info[$code]) ? $info[$code] : '';
    }
}