<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Discourse extends Base
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

	public function publishedImages()
	{
		return $this->hasMany('App\Models\DiscourseImage', 'discourse_id', 'id')->where('publish', '=', 1);
	}

	public function documents()
	{
		return $this->hasMany('App\Models\DiscourseDocument', 'discourse_id', 'id');
	}

	public function scopeResearch($query)
	{
		return $query->where('category', '=', '1')->where('publish', '=', '1');
	}

	public function scopeEvents($query)
	{
		return $query->where('category', '=', '2')->where('publish', '=', '1');
	}

	public function scopePublications($query)
	{
		return $query->where('category', '=', '3')->where('publish', '=', '1');
	}
}
