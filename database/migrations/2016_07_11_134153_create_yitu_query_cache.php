<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYituQueryCache extends Migration
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
	    CREATE TABLE `yitu_query_cache`(
`id` int unsigned not null auto_increment comment '主键',
`type` TINYINT(2) NOT NULL COMMENT '依图接口类型 1:OCR接口, 2:人脸比对接口',
`key_1` VARCHAR(255) COMMENT '第一张图片的路径',
`key_2` VARCHAR(255) COMMENT '第二张图片的路径',
`result` VARCHAR(255) COMMENT '接口返回的结果',
`createtime` INT COMMENT '生成此条记录的unix时间戳',
PRIMARY KEY (`id`)
)ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='依图接口返回值缓存数据表';
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
