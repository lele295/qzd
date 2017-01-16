<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLivingCompareResultTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        \Illuminate\Support\Facades\DB::statement("
            create table `living_compare_result`(
            `id` int unsigned not null auto_increment comment '主键',
            `loan_id` varchar(255) not null default '' comment '订单号',
            `pic_1` varchar(255) not null default '' comment '从安硕获取到的正面照片',
            `pic_2` varchar(255) not null default '' comment '从安硕获取到的反面照片',
            `pic_3` varchar(255) not null default '' comment '从安硕获取到的id5照片',
            `result` text not null default '' comment '比对结果，为json格式',
            `create_time` int comment 'unix时间戳',
            PRIMARY KEY (`id`)
            )ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='获取安硕图片和活体结果记录表';
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
