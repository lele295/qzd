<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateteLoanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement("
            alter table loan
              add `back_reason` tinyint(4) NOT NULL DEFAULT 0 COMMENT '退回原因 0:默认值表示没有填写,1:活体照模糊无法辨认,2:活体照反光或光线不足无法辨认,3:活体照五官遮挡,4:其他(详见back_remark字段)。',
              add `back_remark` varchar(255) NOT NULL DEFAULT '' COMMENT '退回备注。手动填写的退回原因。',
              add `back_time` varchar(25) NOT NULL DEFAULT '' COMMENT '退回时间';
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
