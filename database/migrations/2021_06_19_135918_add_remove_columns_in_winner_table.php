<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRemoveColumnsInWinnerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('winner', function (Blueprint $table) {
            $table->dropColumn('item_id');
            $table->dropColumn('total_digits');
            $table->dropColumn('winning_item');
            $table->string("winner_a")->after('result_time');
            $table->string("winner_b")->after('winner_a');
            $table->string("winner_c")->after('winner_b');
            $table->string("winner_d")->after('winner_c');
            $table->string("winner_e")->after('winner_d');
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
            //
        });
    }
}
