<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class JobDocument extends Base
{
  use HasTranslations;

	public $translatable = [
		'caption'
	];

	protected $fillable = [
		'name',
    'caption',
    'publish',
    'job_id',
	];

  public function job()
  {
    return $this->belongsTo('App\Models\Job');
  }
}