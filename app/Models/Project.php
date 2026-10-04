<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Laravel\Scout\Searchable;

class Project extends Base
{
	use HasTranslations;
	use Searchable;

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
		'year_works',
		'client_works',
		'principal_works',
		'author_works',
		'program',
		'state',
		'author',
		'is_filter_wood',
		'is_filter_reuse',
		'is_filter_area',
		'has_detail',
		'is_highlight',
		'order',
		'publish',
	];

  public function searchableAs()
	{
    return 'projects';
	}

	public function toSearchableArray()
	{
		// Plain text: strip the editor's HTML so tags and entities don't match
		return array_map(
			fn ($value) => trim(html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8')),
			[
				'title' => $this->title,
				'title_short' => $this->title_short,
				'location' => $this->location,
				'year' => $this->year,
				'year_works' => $this->year_works,
				'description' => $this->description,
				'info' => $this->info,
			]
		);
	}

	public function images()
	{
		return $this->hasMany('App\Models\ProjectImage', 'project_id', 'id');
	}

	public function documents()
	{
		return $this->hasMany('App\Models\ProjectDocument', 'project_id', 'id');
	}

	public function publishedDocuments()
	{
		return $this->hasMany('App\Models\ProjectDocument', 'project_id', 'id')->where('publish', '=', 1);
	}

	public function teaserImage()
	{
		return $this->hasOne('App\Models\ProjectImage', 'project_id', 'id')->where('is_preview_navigation', '=', 1);
	}

	public function workImage()
	{
		return $this->hasOne('App\Models\ProjectImage', 'project_id', 'id')->where('is_preview_works', '=', 1);
	}

	public function grids()
	{
		return $this->hasMany('App\Models\Grid', 'project_id', 'id');
	}

	public function scopeHighlight($query)
	{
		return $query->where('publish', '=', '1')->where('is_highlight', '=', '1');
	}

	public function scopeHasDetail($query)
	{
		return $query->where('publish', '=', '1')->where('has_detail', '=', '1');
	}

}
