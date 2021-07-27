<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTwoColumnsToWinnerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('winner', function (Blueprint $table) {
            $table->integer('item_id')->after('id');
            $table->date('winner_date')->after('item_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('winner', function (Blueprint $table) {
            $table->dropColumn('item_id');
            $table->dropColumn('winner_date');
        });
    }
}
