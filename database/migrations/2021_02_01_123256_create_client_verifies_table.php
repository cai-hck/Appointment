<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientVerifiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_verifies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id')->default(0);
            $table->string('phone')->unique();
            $table->string('digits')->default('');
            $table->boolean('verified')->defautl(false);
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
        Schema::dropIfExists('client_verifies');
    }
}
