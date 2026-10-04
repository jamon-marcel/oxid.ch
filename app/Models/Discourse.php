<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Laravel\Scout\Searchable;

class Discourse extends Base
{
	use HasTranslations;
	use Searchable;

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
	
	public function searchableAs()
	{
    return 'discourse';
	}

	public function toSearchableArray()
	{
		// Plain text: strip the editor's HTML so tags and entities don't match
		return array_map(
			fn ($value) => trim(html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8')),
			[
				'heading' => $this->heading,
				'date' => $this->date,
				'title' => $this->title,
				'description_short' => $this->description_short,
				'description' => $this->description,
				'info' => $this->info,
			]
		);
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
