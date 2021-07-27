<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOrdersTableColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('orders', 'item_number'))
        {
            Schema::table('orders', function (Blueprint $table) {
                //$table->dropColumn('item_from');
                //$table->dropColumn('item_to');
                $table->string('item_number')->after('item_digits')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*Schema::table('orders', function (Blueprint $table) {
            
        });*/
        if (Schema::hasColumn('orders', 'item_from'))
        {
            Schema::table('orders', function (Blueprint $table)
            {
                $table->dropColumn('item_from');
            });
        }
        if (Schema::hasColumn('orders', 'item_to'))
        {
            Schema::table('orders', function (Blueprint $table)
            {
                $table->dropColumn('item_to');
            });
        }
    }
}
