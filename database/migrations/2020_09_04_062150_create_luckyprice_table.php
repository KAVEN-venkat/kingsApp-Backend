<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLuckypriceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('luckyprice', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('digit');
            $table->float('price',10,2);
            $table->float('bonus',8,2);
            $table->float('stbonus',8,2);
            $table->integer('item_id');
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('luckyprice');
    }
}
