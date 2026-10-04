<?php

namespace App\Support;

use League\Glide\Server;
use League\Glide\ServerFactory;

/**
 * The Glide server behind /img/... (ImageController).
 */
class Glide
{
	public static function server(): Server
	{
		return ServerFactory::create([
			'source' => storage_path('app/public'),
			'cache' => storage_path('app/.glide-cache'),
			'driver' => self::driver(),
		]);
	}

	/**
	 * Imagick when the extension is loaded, GD otherwise. The production
	 * driver is unconfirmed, so this is detected rather than configured.
	 */
	public static function driver(): string
	{
		return extension_loaded('imagick') ? 'imagick' : 'gd';
	}

	/**
	 * Remove every cached rendition of an upload.
	 */
	public static function forget(string $filename): void
	{
		self::server()->deleteCache('uploads/' . $filename);
	}
}
