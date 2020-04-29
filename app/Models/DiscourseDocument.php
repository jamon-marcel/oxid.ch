<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class DiscourseDocument extends Base
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
