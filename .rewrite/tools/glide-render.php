<?php
// Render every URL in map.txt through the new ImageController, with crops at
// image-cache's legacy size (longer side 2400), into out-<driver>/.
// usage: php glide-render.php <driver>   (run from the scratch dir with map.txt)
$root = '/Users/marceli.to/Jamon.digital/Webroot/oxid.ch';
require $root . '/vendor/autoload.php';
$app = require $root . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$driver = $argv[1] ?? 'gd';
$out = getcwd() . "/out-$driver";
@mkdir($out);

$controller = new class($driver) extends App\Http\Controllers\ImageController {
  public function __construct(string $driver)
  {
    $this->server = League\Glide\ServerFactory::create([
      'source' => storage_path('app/public'),
      'cache' => getcwd() . "/glide-cache-$driver",
      'driver' => $driver,
    ]);
  }

  // Legacy: every crop came out with its longer side at 2400
  protected function size(?string $maxWidth, ?string $maxHeight): array
  {
    return [2400, 2400];
  }
};

$request = Illuminate\Http\Request::create('/');
foreach (file(getcwd() . '/map.txt', FILE_IGNORE_NEW_LINES) as $line) {
  [$file, $kind, , , $url] = preg_split('/\s+/', $line);
  $parts = explode('/', $url); // ['', 'img', kind, name, ...]
  $name = $parts[3];
  $response = match ($kind) {
    'crop' => $controller->crop($request, $name, $parts[4] ?? null, $parts[5] ?? null, $parts[6] ?? null),
    'home' => $controller->home($request, $name),
    'large' => $controller->large($request, $name),
    'thumbnail' => $controller->thumbnail($request, $name),
  };
  file_put_contents("$out/$file", $response->getContent());
}
echo "done $driver\n";
