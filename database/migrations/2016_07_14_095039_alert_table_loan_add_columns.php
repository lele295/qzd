<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlertTableLoanAddColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement("alter table loan add submit_time int(20) NOT NULL DEFAULT '0' COMMENT '提交安硕时间';");
        \Illuminate\Support\Facades\DB::statement("alter table loan add vivo_time int(20) NOT NULL DEFAULT '0' COMMENT '提交活体验证结果时间';");
        \Illuminate\Support\Facades\DB::statement("alter table loan add vivo_res tinyint(4) NOT NULL DEFAULT '0' COMMENT '活体验证结果，0未验证 1验证通过 2验证不通过';");
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
