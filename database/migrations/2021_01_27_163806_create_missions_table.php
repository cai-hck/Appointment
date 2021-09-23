<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('missions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('consulant_id')->default(0);
            $table->string('number_of_users',10)->default('3');
            $table->float('cost_per_user')->default(70.0);
            $table->string('description')->default('');
            $table->string('cover_image')->default('');
            $table->boolean('status')->default(true);
            $table->string('active_date')->default('');
            $table->string('expire_date')->default('');
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
        Schema::dropIfExists('missions');
    }
}
