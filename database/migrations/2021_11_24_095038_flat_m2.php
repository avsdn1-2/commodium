<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FlatM2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('flats', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->boolean('useLift')->after('warmCounter');
            $table->string('name')->after('privilege');
            $table->string('first_name')->after('name');
            $table->string('mid_name')->after('first_name');
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
        Schema::table('flats', function (Blueprint $table) {
            $table->string('number');
            $table->dropColumn('useLift');
            $table->dropColumn('name');
            $table->dropColumn('first_name');
            $table->dropColumn('mid_name');
        });
    }
}
