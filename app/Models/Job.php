<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Job extends Base
{
  use HasTranslations;

	public $translatable = [
		'title',
		'description',
		'info',
	];

	protected $fillable = [
		'title',
		'description',
    'info',
		'order',
		'publish',
  ];
  
	public function documents()
	{
		return $this->hasMany('App\Models\JobDocument', 'job_id', 'id');
	}
}
