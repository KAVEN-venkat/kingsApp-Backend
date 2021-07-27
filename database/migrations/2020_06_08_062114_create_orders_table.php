<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('sale_date');
            $table->integer('user_id')->nullable();
            $table->integer('item_id');
            $table->float('item_price',10,2);
            $table->float('extra_price',10,2);
            $table->string('item_digits');
            $table->string('item_from');
            $table->string('item_to');
            $table->integer('item_qty');
            $table->integer('total_items');
            $table->float('total_price',10,2);
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
        Schema::dropIfExists('orders');
    }
}
