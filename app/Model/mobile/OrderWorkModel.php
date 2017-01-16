<?php

namespace App\Model\mobile;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderWorkModel extends Model
{
    protected $table = 'orders_work';

    /**非学生个人信息
     * @var array
     */
    public static $infoLabels = [
        'work_unit' => '工作单位',
        'work_unit_mobile' => '单位电话',
        'work_edu' => '最高学历',
        'work_email' => '电子邮箱',
        'work_family_name' => '直属亲属姓名',
        'work_family_mobile' => '联系方式',
        /*'work_other_name' => '其他联系人姓名',
        'work_other_mobile' => '其他联系人手机',*/
    ];
    /**非学生账户信息
     * @var array
     */
    public static $bankLabel = [
        'work_deposit_bank' => '代扣开户银行',
        'work_bank_branch_name' => '银行支行',
        'work_repayment_account' => '代扣还款账户',
        'work_credit_card' => '信用卡账号',
    ];

    //获取用户工作信息
    public function get_user_work_info($id)
    {
        $data = DB::table($this->table)->where('id',$id)
            ->leftjoin('sync_bankput_info as a','a.BANKNO','=','work_bank_branch_no')
            ->select('orders_work.*','a.BANKNAME')
            //->toSql();
            ->first();

        return $data;
    }
}
