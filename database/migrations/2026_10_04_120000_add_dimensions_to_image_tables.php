<?php

use App\Support\ImageSupport;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The upload's size after EXIF orientation, so the markup knows the aspect
 * ratio without reading files (see App\Models\Concerns\IsImage).
 */
return new class extends Migration
{
  protected array $tables = ['home_images', 'project_images', 'discourse_images', 'job_images', 'team_images', 'profile_images'];

  public function up(): void
  {
    foreach ($this->tables as $table) {
      Schema::table($table, function (Blueprint $table) {
        $table->unsignedInteger('width')->nullable()->after('name');
        $table->unsignedInteger('height')->nullable()->after('width');
      });

      DB::table($table)->select('id', 'name')->orderBy('id')->each(function ($row) use ($table) {
        if ($size = ImageSupport::dimensions(storage_path('app/public/uploads/' . $row->name))) {
          DB::table($table)->where('id', $row->id)->update(['width' => $size[0], 'height' => $size[1]]);
        }
      });
    }
  }

  public function down(): void
  {
    foreach ($this->tables as $table) {
      Schema::table($table, function (Blueprint $table) {
        $table->dropColumn(['width', 'height']);
      });
    }
  }
};
