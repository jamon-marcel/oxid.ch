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
	 * Which elements (by position) stack in which column, left to right.
	 */
	protected const COLUMNS = [
		'1' => [[0]],
		'1-1' => [[0], [1]],
		'1-2' => [[0], [1, 2]],
		'2-1' => [[0, 1], [2]],
		'2-2' => [[0, 2], [1, 3]],
	];

	/**
	 * The row as one composition: columns of stacked images that share the
	 * row's height, each image at its own (cropped) aspect ratio.
	 *
	 * Per column: `ratio` is width / (height without its inner gaps) and
	 * `gaps` the gaps inside it. For the row, width = ratio * height +
	 * gaps * gap, so the CSS can fit it into the window (see _project.scss).
	 */
	public function composition(): array
	{
		$elements = $this->elements->filter(fn ($element) => $element->image)->values();
		$columns = [];

		foreach (self::COLUMNS[$this->layout?->key] ?? [array_keys($elements->all())] as $positions) {
			$images = collect($positions)->map(fn ($i) => $elements[$i]->image ?? null)->filter()->values();
			if ($images->isEmpty()) {
				continue;
			}

			$ratios = $images->map(fn ($image) => self::ratio($image));
			// Each image's share of the column's height (flex-grow; these sum
			// to 1, a smaller sum would leave part of the height empty)
			$inverse = $ratios->sum(fn ($ratio) => 1 / $ratio);
			$columns[] = [
				'images' => $images->map(fn ($image, $i) => ['image' => $image, 'share' => 1 / $ratios[$i] / $inverse])->all(),
				'ratio' => 1 / $inverse,
				'gaps' => $images->count() - 1,
			];
		}

		$columns = collect($columns);

		return [
			'columns' => $columns->all(),
			'ratio' => $columns->sum('ratio'),
			'gaps' => max(0, $columns->count() - 1) - $columns->sum(fn ($column) => $column['ratio'] * $column['gaps']),
		];
	}

	protected static function ratio($image): float
	{
		[$width, $height] = $image->displaySize() ?? [16, 10];

		return $width && $height ? $width / $height : 1.6;
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
