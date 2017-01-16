<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSmsManageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement("CREATE TABLE IF NOT EXISTS `sms_manage`(
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `mobile` varchar(11) NOT NULL COMMENT '手机号',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '发送状态，0：成功 1：失败',
  `sms_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '短信类型，1：文本 2：语音',
  `content` varchar(255) NOT NULL COMMENT '短信内容',
  `created_at` datetime NOT NULL COMMENT '创建日期',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8");
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
