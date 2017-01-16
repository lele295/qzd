<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Org;

/**
 * Description of SelectMap
 *
 * @author lenovo
 */
class SelectMap {

    /**
     * 婚姻状况
     * @return type
     */
    public static function maritalStatus() {
        return [
            '未婚',
            '已婚',
            '离异',
            '丧偶'
        ];
    }

    /**
     * 学历
     * @return type
     */
    public static function eduLevel() {
        return [
            1 => '小学',
            2 => '初中',
            3 => '高中',
            4 => '中专',
            5 => '专科',
            6 => '本科',
            7 => '研究生及以上'
        ];
    }

    /**
     * 住房性质
     */
    public static function houseProperties() {
        return [
            '购买',
            '租赁',
            '自建',
            '继承',
            '父母房产',
            '福利房',
            '宿舍',
            '其他',
        ];
    }

    /**
     * 银行
     * @return type
     */
    public static function banks() {
        return [
            308 => '招商银行股份有限公司',
            102 => '中国工商银行股份有限公司',
            103 => '中国农业银行股份有限公司',
            104 => '中国银行股份有限公司',
            403 => '中国邮政储蓄银行股份有限公司',
        ];
    }

    /**
     * 工资发放方式
     * @return type
     */
    public static function salaryType() {
        return [
            1 => '现金',
            2 => '银行代发',
            3 => '转账',
            4 => '其他'
        ];
    }

    /**
     * 关系
     */
    public static function relationShip() {
        return [
            '父亲',
            '母亲',
            '兄弟',
            '姐妹',
            '儿女',
            '其他亲属',
        ];
    }
    
    
   /**
    * 借款期数
    * @return type
    */
    public static function periods() {
        return [
            6,
            12,
            18,
            24,
            30,
            36,
        ];
    }
    
    
    /**
    * 企业所属行业
    * @return type
    */
    public static function industry() {
        return [
            1=>'农、林、牧、渔业',
            2=>'采矿业',
            3=>'制造业',
            4=>'电力、燃气和水的生产和供应业',
            5=>'建筑业',
            6=>'交通运输、仓储和邮政业',
            7=>'信息传输、计算机服务和软件业',
            8=>'批发和零售业',
            9=>'住宿和餐饮业',
            10=>'金融业',
            11=>'房地产业',
            12=>'租赁与商业化服务',
            13=>'科学研究、技术服务和地质勘查业',
            14=>'水利、环境和公共设施管理业',
            15=>'居民服务和其他服务业',
            16=>'教育',
            17=>'卫生、社会保障和社会福利业',
            18=>'文化、体育和娱乐业',
            19=>'文化艺术业',
            20=>'公共管理和社会组织',
            21=>'其他',
        ];
    }
    
    
    /**
    * 企业性质
    * @return type
    */
    public static function companyProperty() {
        return [
            1=>'国家行政企业',
            2=>'公私合作企业',
            3=>'中外合资企业',
            4=>'社会组织机构',
            5=>'外资企业',
            6=>'私营企业',
            7=>'集体企业',
        ];
    }

}
