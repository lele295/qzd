<?php

namespace App\Model\mobile;

use Illuminate\Database\Eloquent\Model;

class OrderPicModel extends Model
{
    protected $table = 'orders_picture';
    /**
     * @var array
     */
    public static $picLabel = [
        'cert_face_pic' => '身份证正面照片地址',
        'cert_opposite_pic' => '身份证反面照片地址',
        'cert_hand_pic' => '手持身份证照片地址',
        'bank_card_pic' => '银行卡正面',
        'credit_auth_pic' => '征信授权书',
        'contract_pic' => '手术合同签名确认单',
        'work_pic' => '工牌或名片',
    ];

}
