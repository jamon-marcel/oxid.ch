<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class TeamDocument extends Base
{
  use HasTranslations;

	public $translatable = [
		'caption'
	];

	protected $fillable = [
		'name',
    'caption',
    'language',
    'publish',
    'team_id',
	];

  public function team()
  {
    return $this->belongsTo('App\Models\Team');
  }
}

