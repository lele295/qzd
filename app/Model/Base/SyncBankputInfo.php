<?php

namespace App\Model\Base;

use Illuminate\Database\Eloquent\Model;

class SyncBankputInfo extends Model {
    
    protected $table = 'sync_bankput_info';
    
    /**
     * 通过分行代码查询分行名
     * @param unknown $bank_no
     */
    public static function getBankName($bank_no) {
       return self::where('BANKNO',$bank_no)->first();
    }
}