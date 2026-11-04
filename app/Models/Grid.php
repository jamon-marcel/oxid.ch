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

		$columns = collect(self::equalColumns($columns));

		return [
			'columns' => $columns->all(),
			'ratio' => $columns->sum('ratio'),
			'gaps' => max(0, $columns->count() - 1) - $columns->sum(fn ($column) => $column['ratio'] * $column['gaps']),
		];
	}

	/**
	 * The one width every row on a page shares, as a CSS expression: the
	 * widest at which each row still fits the window height (--H), never
	 * wider than the container. Row i is r·H + c·gap wide at full height,
	 * so the narrowest of those wins; each row's height then follows from
	 * its own ratio (see _project.scss).
	 */
	public static function sharedWidth(iterable $compositions): string
	{
		$terms = collect($compositions)
			->filter(fn ($composition) => $composition['columns'])
			->map(fn ($composition) => sprintf('%s * var(--H) + %s * var(--g)', round($composition['ratio'], 4), round($composition['gaps'], 4)))
			->unique()
			->values();

		return 'min('.$terms->prepend('100cqw')->implode(', ').')';
	}

	/**
	 * Mixed rows (`1-2`, `2-1`: a single image beside a stack) split 50:50,
	 * so their column line sits where it does in a `1-1` row. The stack
	 * keeps its exact fit and sets the height; the single image takes the
	 * stack's width and height and is cropped by `object-fit: cover`
	 * (a 3:4 beside two 16:10 loses about 3% top and bottom).
	 */
	protected static function equalColumns(array $columns): array
	{
		if (count($columns) !== 2 || $columns[0]['gaps'] === $columns[1]['gaps']) {
			return $columns;
		}

		$stack = $columns[0]['gaps'] > $columns[1]['gaps'] ? 0 : 1;
		$single = 1 - $stack;
		$columns[$single]['ratio'] = $columns[$stack]['ratio'];
		$columns[$single]['gaps'] = $columns[$stack]['gaps'];

		return $columns;
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
