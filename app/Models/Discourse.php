<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use App\Services\Search\SearchService;

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
	
	protected static function booted()
	{
		// The search index is cached; any change to a record invalidates it
		static::saved(fn () => SearchService::flush());
		static::deleted(fn () => SearchService::flush());
	}

	public function images()
	{
		return $this->hasMany('App\Models\DiscourseImage', 'discourse_id', 'id')->orderBy('order');
	}

	public function previewImage()
	{
		return $this->hasOne('App\Models\DiscourseImage', 'discourse_id', 'id')->where('publish', '=', 1)->where('is_preview', '=', 1);
	}

	public function publishedImages()
	{
		return $this->hasMany('App\Models\DiscourseImage', 'discourse_id', 'id')->where('publish', '=', 1)->orderBy('order');
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
