<?php

use App\Helpers\AppHelper;
use App\Helpers\ImageHelper;
use Illuminate\Support\Facades\Facade;

return [

    'name' => env('APP_NAME', 'Laravel'),

    'env' => env('APP_ENV', 'production'),

    'debug' => (bool) env('APP_DEBUG', false),

    'url' => env('APP_URL', 'http://localhost'),

    'timezone' => 'UTC',

    'locale' => 'de',

    'fallback_locale' => 'de',

    'faker_locale' => 'de_DE',

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'aliases' => Facade::defaultAliases()->merge([
        'AppHelper' => AppHelper::class,
        'ImageHelper' => ImageHelper::class,
    ])->toArray(),

];
