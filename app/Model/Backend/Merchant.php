<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Model\Backend;

use Illuminate\Database\Eloquent\Model;
use DB;

class Merchant extends Model
{

    protected $table = 'sync_retail_info';

    public $timestamps = true;

    protected $guarded = [];
    
    public static function getMerchantDefail($serial_no){
        $res=self::select(
            'RNO',//商户代码
            'RNAME',//商户名称
            'RTYPE',//商户类型
            'CITY',//商户所在城市
            'LAWPERSON',//法人
            'LAWPERSONCARDNO',//法人身份证
            'LINKNAME',//主要联系人
            'LINKTEL',//主要联系人号码
            'LINKEMAIL',//主要联系人邮箱
            'FINANCIALNAME',//财务姓名
            'FINANCIALTEL',//财务号码
            'FINANCIALEMAIL',//财务邮箱
            'ACCOUNTBANKCITY',//开户行所在省市
            'ACCOUNTBANK',//开户行
            'ACCOUNTNAME',//开户账号名
            'BRANCHCODE',//开户支行
            'ACCOUNT',//开户账号
            'ADDRESS',//商戶地址
            'STORENUM'//分店数量
            //商户服务费
            )->where('SERIALNO',$serial_no)->first();
        return $res;
    }

}