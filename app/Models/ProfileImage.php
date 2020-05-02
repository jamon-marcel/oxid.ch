<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ProfileImage extends Base
{
	use HasTranslations;

	public $translatable = [
		'caption'
	];

	protected $fillable = [
		'name',
		'caption',
		'publish',
	];
}
