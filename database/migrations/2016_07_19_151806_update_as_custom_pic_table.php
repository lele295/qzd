<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAsCustomPicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement("alter table as_custom_pic add body_pic varchar(200) NOT NULL DEFAULT '' COMMENT '活体照片';");
        \Illuminate\Support\Facades\DB::statement("alter table as_custom_pic add yitu_score varchar(50) NOT NULL DEFAULT '' COMMENT '依图参考评分';");
        \Illuminate\Support\Facades\DB::statement("alter table as_custom_pic add body_pic1 varchar(200) NOT NULL DEFAULT '' COMMENT '活体照片2';");
        \Illuminate\Support\Facades\DB::statement("alter table as_custom_pic add body_pic2 varchar(200) NOT NULL DEFAULT '' COMMENT '活体照片3';");

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
