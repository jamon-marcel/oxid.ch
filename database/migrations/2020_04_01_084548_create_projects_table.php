<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->json('title');
            $table->json('title_short')->nullable();
            $table->json('location')->nullable();
            $table->json('description')->nullable();
            $table->json('info')->nullable();
            $table->string('year', 100);
            $table->tinyInteger('program');
            $table->tinyInteger('state');
            $table->tinyInteger('author');
            $table->tinyInteger('has_detail')->default(1);
            $table->tinyInteger('is_highlight')->default(0);
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
        Schema::dropIfExists('projects');
    }
}
