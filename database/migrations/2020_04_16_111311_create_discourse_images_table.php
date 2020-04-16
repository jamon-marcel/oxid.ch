<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscourseImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discourse_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->json('caption')->nullable();
            $table->tinyInteger('publish')->default(0);
            $table->unsignedBigInteger('discourse_id');
            $table->foreign('discourse_id')->references('id')->on('discourses')->onDelete('cascade');
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
        Schema::dropIfExists('discourse_images');
    }
}
