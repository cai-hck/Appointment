<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('mission_id');
            $table->integer('consultant_id');
            $table->string('date')->default('');
            $table->string('weekday')->default('');
            $table->string('slots');
            $table->boolean('isDefault')->default(true);
            $table->boolean('isHoliday')->default(false);
            $table->boolean('isReschedule')->default(false);            
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
        Schema::dropIfExists('schedules');
    }
}
