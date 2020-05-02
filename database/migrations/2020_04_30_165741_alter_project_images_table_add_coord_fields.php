<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProjectImagesTableAddCoordFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_images', function (Blueprint $table) {
            $table->dropColumn('coords');
            $table->double('coords_w', 24, 20)->nullable();
            $table->double('coords_h', 24, 20)->nullable();
            $table->double('coords_x', 24, 20)->nullable();
            $table->double('coords_y', 24, 20)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
