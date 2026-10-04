<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `order` was a TINYINT (up to 127): saving the order of a list with more
 * entries failed halfway, e.g. the 244 former team members.
 */
return new class extends Migration
{
  protected array $tables = ['projects', 'grids', 'discourses', 'discourse_images', 'news', 'jobs', 'job_images', 'team', 'team_images', 'profile_images'];

  public function up(): void
  {
    foreach ($this->tables as $table) {
      Schema::table($table, function (Blueprint $table) {
        $table->smallInteger('order')->default(-1)->change();
      });
    }
  }

  public function down(): void
  {
    foreach ($this->tables as $table) {
      Schema::table($table, function (Blueprint $table) {
        $table->tinyInteger('order')->default(-1)->change();
      });
    }
  }
};
