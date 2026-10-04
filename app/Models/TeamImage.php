<?php
namespace App\Models;
use App\Models\Base;
use App\Models\Concerns\IsImage;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class TeamImage extends Base
{
	use HasTranslations, IsImage;

	public $translatable = [
		'caption'
	];

	protected $fillable = [
		'name',
		'caption',
		'coords_w',
    'coords_h',
    'coords_x',
		'coords_y',
		'order',
		'publish',
	];
}
