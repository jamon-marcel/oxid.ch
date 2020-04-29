<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class News extends Base
{
	use HasTranslations;

	public $translatable = [
		'title',
		'subtitle',
		'text',
	];

	protected $fillable = [
		'subtitle',
		'title',
		'text',
		'date_end',
		'sticky',
		'order',
		'publish',
	];

	/**
	 * Mutator 'date_due'
	 */

	public function setDateEndAttribute($value)
	{
		$this->attributes['date_end'] = $value ? \Carbon\Carbon::parse($value)->format('Y.m.d') : null;
	}
}
