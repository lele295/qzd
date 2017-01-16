<?php

namespace App\Model\mobile;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderSchModel extends Model
{
    protected $table = 'orders_school';
    /**学生个人信息
     * @var array
     */
    public static $infoLabels = [
        'school_name' => '学校名称',
        'school_edu' => '最高学历',
        'school_address' => '学校地址',
        'school_major' => '所学专业',
        'school_family_address' => '家庭住址',
        'school_qq' => 'qq',
        'school_wechat' => '微信',
        'school_family_name' => '直系亲属姓名',
        'school_family_mobile' => '直系亲属电话',
    ];
    /**学生账户信息
     * @var array
     */
    public static $bankLabels = [
        'school_deposit_bank' => '代扣开户银行',
        'school_bank_branch' => '银行支行',
        'school_repayment_account' => '代扣还款账户',
    ];

    //获取用户学校相关信息
    public function get_user_sch_info($id)
    {
        $data = DB::table($this->table)->where('id',$id)->first();
        return $data;
    }
}
