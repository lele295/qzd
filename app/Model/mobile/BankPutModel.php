<?php

namespace App\Model\mobile;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class BankPutModel extends Model
{
    protected $table = 'sync_bankput_info';

    public static function getBpByBn($bank_no)
    {
        return Arr::get(static::where('BANKNO', $bank_no)
            ->first(), 'BANKNAME', '');
    }
}
