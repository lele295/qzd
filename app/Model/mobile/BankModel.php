<?php

namespace App\Model\mobile;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class BankModel extends Model
{
    protected $table = 'sync_code_library';

    public static function getBankByCode($code)
    {
        return Arr::get(static::where('codeno', "BankCode")
            ->where('isinuse', 1)
            ->where('itemno', $code)
            ->first(), 'ITEMNAME','');
    }
}
