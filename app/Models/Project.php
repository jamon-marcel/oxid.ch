<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Project extends Model
{
	use HasTranslations;

	public $translatable = [
		'title',
		'title_short',
		'location',
		'description',
		'info',
	];

	protected $fillable = [
		'title',
		'title_short',
		'location',
		'year',
		'description',
		'info',
		'program',
		'state',
		'author',
		'is_filter_wood',
		'is_filter_reuse',
		'has_detail',
		'is_highlight',
		'order',
		'publish',
	];
}
