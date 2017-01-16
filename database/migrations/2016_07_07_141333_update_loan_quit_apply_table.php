<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLoanQuitApplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement("alter table loan_quit_apply add source_type tinyint(4) NOT NULL DEFAULT '0' COMMENT '来源类型，1：微信端 2：PC端 3：分期购';");
        \Illuminate\Support\Facades\DB::statement("alter table loan_quit_apply add page tinyint(4) NOT NULL DEFAULT '0' COMMENT '退出的页面';");
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
