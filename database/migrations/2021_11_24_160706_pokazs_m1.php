<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PokazsM1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('pokazs', function (Blueprint $table) {
            $table->integer('warm')->nullable()->default(null)->after('water');
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
        Schema::table('pokazs', function (Blueprint $table) {
            $table->dropColumn('warm');
        });
    }
}
