<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTeamImagesAddCropCoords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('team_images', function (Blueprint $table) {
            $table->double('coords_w', 16, 12)->after('caption')->nullable();
            $table->double('coords_h', 16, 12)->after('coords_w')->nullable();
            $table->double('coords_x', 16, 12)->after('coords_h')->nullable();
            $table->double('coords_y', 16, 12)->after('coords_x')->nullable();
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
