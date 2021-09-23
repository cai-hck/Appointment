<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeetingRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meeting_rooms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id');
            $table->integer('consultant_id');
            $table->integer('user_id'); //Consultant User ID
            $table->integer('mission_id');
            $table->integer('section_id');
            $table->string('schedule_date');
            $table->string('start_time');
            $table->string('end_time');
            $table->integer('booking_id');
            $table->string('meeting_url');
            $table->string('twilio_access_token');
            $table->string('twilio_channel_name');
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
        Schema::dropIfExists('meeting_rooms');
    }
}
