<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Discourse extends Model
{
	use HasTranslations;

	public $translatable = [
		'heading',
		'date',
		'title',
    'description_short',
		'description',
		'info',
	];

	protected $fillable = [
		'heading',
		'date',
		'title',
    'description_short',
		'description',
    'info',
    'category',
		'order',
		'publish',
  ];
  
	public function images()
	{
		return $this->hasMany('App\Models\DiscourseImage', 'discourse_id', 'id');
	}

	public function documents()
	{
		return $this->hasMany('App\Models\DiscourseDocument', 'discourse_id', 'id');
	}
}
