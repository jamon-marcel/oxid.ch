<?php

namespace Tests\Feature;

use App\Http\Controllers\ImageController;
use App\Support\Glide;
use App\Support\ImageSupport;
use Tests\TestCase;

class ImageRenditionTest extends TestCase
{
  protected string $name;

  protected function setUp(): void
  {
    parent::setUp();

    $this->name = 'phpunit-' . uniqid() . '.jpg';
    $image = imagecreatetruecolor(400, 300);
    imagefill($image, 0, 0, imagecolorallocate($image, 200, 100, 50));
    imagejpeg($image, storage_path('app/public/uploads/' . $this->name));
  }

  protected function tearDown(): void
  {
    Glide::forget($this->name);
    @unlink(storage_path('app/public/uploads/' . $this->name));

    parent::tearDown();
  }

  protected function cachedFiles(): array
  {
    return glob(storage_path('app/.glide-cache/uploads/' . $this->name . '/*')) ?: [];
  }

  public function test_a_broken_cached_rendition_is_rendered_again(): void
  {
    $format = ImageSupport::modernFormats()[0] ?? 'jpg';
    $url = Glide::url($this->name, 200, null, $format);

    $this->get($url)->assertOk();
    [$cached] = $this->cachedFiles();
    file_put_contents($cached, hex2bin('0000001066747970' . '0000000000000000'));

    $response = $this->get($url)->assertOk()->assertHeader('Content-Type', 'image/' . ($format === 'jpg' ? 'jpeg' : $format));
    $this->assertSame([200, 150], array_slice(getimagesizefromstring($response->getContent()), 0, 2));
    $this->assertSame($response->getContent(), file_get_contents($this->cachedFiles()[0]));
  }

  public function test_a_format_the_encoder_keeps_breaking_falls_back_to_the_upload_format(): void
  {
    $format = ImageSupport::modernFormats()[0] ?? null;
    if (! $format) {
      $this->markTestSkipped('No modern format on this server');
    }

    $this->app->bind(ImageController::class, fn () => new class extends ImageController {
      protected function make(string $filename, array $params): string
      {
        $path = parent::make($filename, $params);
        if ($params['fm'] !== 'jpg') {
          $this->server->getCache()->write($path, hex2bin('0000001066747970' . '0000000000000000'));
        }

        return $path;
      }
    });

    $response = $this->get(Glide::url($this->name, 200, null, $format))
      ->assertOk()
      ->assertHeader('Content-Type', 'image/jpeg')
      ->assertHeader('Cache-Control', 'max-age=300, public');
    $this->assertSame(IMAGETYPE_JPEG, getimagesizefromstring($response->getContent())[2]);
  }
}
