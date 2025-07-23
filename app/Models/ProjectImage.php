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
    'is_plan',
    'coords_w',
    'coords_h',
    'coords_x',
    'coords_y',
    'orientation',
    'publish',
    'project_id',
	];

  public function project()
  {
    return $this->belongsTo('App\Models\Project');
  }

  // Append the coords as coma separated string
  protected $appends = ['coords'];

  public function getCoordsAttribute() {
    $coords = [];
    $coords[] = $this->coords_w !== null ? floor(floatval($this->coords_w)) : 0;
    $coords[] = $this->coords_h !== null ? floor(floatval($this->coords_h)) : 0;
    $coords[] = $this->coords_x !== null ? floor(floatval($this->coords_x)) : 0;
    $coords[] = $this->coords_y !== null ? floor(floatval($this->coords_y)) : 0;
    return implode(',', $coords);
  }
}
