<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discourses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->json('heading');
            $table->json('date');
            $table->json('title');
            $table->json('description_short')->nullable();
            $table->json('description')->nullable();
            $table->json('info')->nullable();
            $table->tinyInteger('category');
            $table->tinyInteger('order')->default(-1);
            $table->tinyInteger('publish')->default(0);
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
        Schema::dropIfExists('discourses');
    }
}
