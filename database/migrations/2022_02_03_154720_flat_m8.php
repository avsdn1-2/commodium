<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FlatM8 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('flats', function(Blueprint $table) {
            $table->float('square_total')->after('number');
            $table->renameColumn('square', 'square_warm');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('flats', function(Blueprint $table) {
            $table->dropColumn('square_total');
            $table->renameColumn('square_warm', 'square');
        });
    }
}
