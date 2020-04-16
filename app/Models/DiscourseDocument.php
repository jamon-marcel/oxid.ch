<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class DiscourseDocument extends Model
{
  use HasTranslations;

	public $translatable = [
		'caption'
	];

	protected $fillable = [
		'name',
		'caption',
    'publish',
    'discourse_id',
	];

  public function discourse()
  {
    return $this->belongsTo('App\Models\Discourse');
  }
}
