<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Contact extends Base {

  use HasTranslations;
  
  protected $table = 'contact';

  public $translatable = [
		'address',
    'contacts',
    'info',
    'imprint'
	];

	protected $fillable = [
		'address',
    'google_maps_url',
    'contacts',
    'info',
    'imprint',
  ];
}
