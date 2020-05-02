<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Profile extends Base
{
  use HasTranslations;

  protected $table = 'profile';

	public $translatable = [
		'title',
		'description',
	];

	protected $fillable = [
		'title',
		'description',
		'publish',
  ];

	public function images()
	{
		return $this->hasMany('App\Models\ProfileImage', 'profile_id', 'id');
	}
}
