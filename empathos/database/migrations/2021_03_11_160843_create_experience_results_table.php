<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExperienceResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('experience_results', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('userID');
            $table->integer('experienceID');
            $table->dateTime('startTime');
            $table->string('userEmotion');
            $table->integer('valence');
            $table->integer('arousal');
            $table->integer('dominance');
            $table->json('results')->nullable();
            $table->integer('frequency');
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('name')->nullable();
            $table->string('surname')->nullable();
            $table->dateTime('birthday')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('country')->nullable();
        });

        Schema::create('tests', function( Blueprint $table) {
           $table->id();
           $table->timestamps();
           $table->string('title');
           $table->string('description');
           $table->integer('duration');
           $table->integer('resultStart');
           $table->integer('resultDuration');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('experience_results');
        Schema::dropIfExists('users');
        Schema::dropIfExists('tests');
    }
}
