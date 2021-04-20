<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Model;

class Grid extends Base
{
	protected $fillable = [
		'order',
    'publish',
    'project_id',
    'layout_id',
  ];
  
	public function layout()
	{
		return $this->hasOne('App\Models\GridLayout', 'id', 'layout_id');
	}

	public function elements()
	{
		return $this->hasMany('App\Models\GridElement', 'grid_id', 'id')->orderBy('position');
	}

	/**
	 * Scope a query to only grids by a project.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $query
	 * @return \Illuminate\Database\Eloquent\Builder
	 */

	public function scopeByProject($query, $project_id)
	{
		return $query->where('project_id', '=', $project_id);
	}  
}
