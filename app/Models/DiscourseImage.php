<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class DiscourseImage extends Model
{
	use HasTranslations;

	public $translatable = [
		'caption'
	];

	protected $fillable = [
		'name',
    'caption',
    'is_preview',
    'publish',
    'discourse_id',
	];

  public function discourse()
  {
    return $this->belongsTo('App\Models\Discourse');
  }
}
