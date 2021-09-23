<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMissionSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mission_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('mission_id');
            $table->string('logo');
            $table->string('contact_no');
            $table->string('contact_email');
            $table->string('contact_address');
            $table->text('description_en');
            $table->text('description_ar');
            $table->string('email_subject');
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
        Schema::dropIfExists('mission_settings');
    }
}
