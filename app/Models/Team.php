<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

class Team extends Base
{
  use HasFactory, HasTranslations;

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
		'phone',
    'category',
		'order',
		'publish',
  ];

	public function documents()
	{
		return $this->hasMany('App\Models\TeamDocument', 'team_id', 'id');
	}

	public function scopePartner($query)
	{
		return $query->where('category', '=', '1')->orderBy('order', 'ASC');
	}

	public function scopeEmployee($query)
	{
		return $query->where('category', '=', '2')->orderBy('order', 'ASC');
	}
	
	public function scopeAlumni($query)
	{
		return $query->where('category', '=', '3')->orderBy('name', 'ASC')->orderBy('order', 'ASC');
	}

  public function scopeAssociate($query)
  {
    return $query->where('category', '=', '4')->orderBy('order', 'ASC');
  }

  public function scopeSeniorStaff($query)
  {
    return $query->where('category', '=', '5')->orderBy('order', 'ASC');
  }
}
