<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Tarif extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('tarifs', function (Blueprint $table) {
            $table->float('lift')->after('service');
            $table->float('rubbish')->after('lift');
            $table->float('parkingCleaning')->after('rubbish');
            $table->float('parkingLightening')->after('parkingCleaning');

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
    }
}
