<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductBusinessTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement("
CREATE TABLE `sync_product_businesstype`(
`PRODUCTBUSTYPEID` varchar(32) DEFAULT NULL,
`PRODUCTSERIESID` varchar(32) DEFAULT NULL,
`BUSTYPEID` varchar(32) DEFAULT NULL,
`branch` int(11) NOT NULL DEFAULT '1'
)ENGINE=InnoDB DEFAULT CHARSET=utf8
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
