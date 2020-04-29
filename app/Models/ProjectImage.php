<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ProjectImage extends Base
{
	use HasTranslations;

	public $translatable = [
		'caption'
	];

	protected $fillable = [
		'name',
    'caption',
    'is_preview_navigation',
    'is_preview_works',
    'is_grid',
    'publish',
    'project_id',
	];

  public function project()
  {
    return $this->belongsTo('App\Models\Project');
  }
}
