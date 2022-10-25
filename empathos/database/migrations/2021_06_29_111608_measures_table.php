<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MeasuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('measures', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('experienceResultID');
            $table->float('bpm', 10, 4);
            $table->float('ibi', 10, 4);
            $table->float('sdnn', 10, 4);
            $table->float('sdsd', 10, 4);
            $table->float('rmssd', 10, 4);
            $table->float('pnn20', 10, 4);
            $table->float('pnn50', 10, 4);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('measures');
    }
}
