<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInternalChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('internal_chats', function (Blueprint $table) {
            $table->id();

            $table->integer('user_id');
            $table->integer('from');
            $table->integer('to');
            $table->text('message');
            $table->enum('sender',['consul','secret'])->default('consul');
            $table->string('date');
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
        Schema::dropIfExists('internal_chats');
    }
}
