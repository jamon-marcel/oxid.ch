<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Team extends Model
{
  use HasTranslations;

	protected $table = 'team';

	public $translatable = [
		'role',
		'position',
	];

	protected $fillable = [
		'firstname',
		'name',
		'role',
    'position',
		'email',
    'category',
		'order',
		'publish',
  ];

	public function documents()
	{
		return $this->hasMany('App\Models\TeamDocument', 'team_id', 'id');
	}
}
