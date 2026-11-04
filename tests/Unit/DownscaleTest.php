<?php

namespace Tests\Unit;

use App\Support\Glide;
use App\Support\ImageSupport;
use Imagick;
use Tests\TestCase;

class DownscaleTest extends TestCase
{
  protected string $dir;

  protected function setUp(): void
  {
    parent::setUp();

    if (Glide::driver() !== 'imagick') {
      $this->markTestSkipped('Downscaling needs Imagick');
    }

    $this->dir = sys_get_temp_dir() . '/oxid-downscale-' . uniqid();
    mkdir($this->dir);
    config(['images.upload_max' => 1000]);
  }

  protected function tearDown(): void
  {
    if (isset($this->dir)) {
      array_map('unlink', glob($this->dir . '/*') ?: []);
      @rmdir($this->dir);
    }

    parent::tearDown();
  }

  protected function make(string $name, int $width, int $height, string $format, ?int $orientation = null): string
  {
    $image = new Imagick();
    $image->newImage($width, $height, $format === 'png' ? 'transparent' : 'orange');
    $image->setImageFormat($format);
    $image->writeImage($path = $this->dir . '/' . $name);

    if ($orientation) {
      // An EXIF block with only the Orientation tag, as a camera writes it,
      // right after the JPEG's start marker
      $tiff = 'II*' . "\0" . pack('V', 8) . pack('v', 1) . pack('vvVvv', 0x0112, 3, 1, $orientation, 0) . pack('V', 0);
      $exif = "Exif\0\0" . $tiff;
      $jpeg = file_get_contents($path);
      file_put_contents($path, substr($jpeg, 0, 2) . "\xFF\xE1" . pack('n', strlen($exif) + 2) . $exif . substr($jpeg, 2));
    }

    return $path;
  }

  public function test_a_large_jpeg_is_scaled_to_the_limit(): void
  {
    $path = $this->make('photo.jpg', 3000, 2000, 'jpg');

    $this->assertTrue(ImageSupport::downscale($path));
    $this->assertSame([1000, 667], ImageSupport::dimensions($path));
    $this->assertSame(IMAGETYPE_JPEG, getimagesize($path)[2]);
  }

  public function test_exif_rotation_is_applied_once(): void
  {
    // Stored landscape, displayed portrait (rotate 90°)
    $path = $this->make('rotated.jpg', 3000, 2000, 'jpg', Imagick::ORIENTATION_RIGHTTOP);
    $this->assertSame([2000, 3000], ImageSupport::dimensions($path));

    $this->assertTrue(ImageSupport::downscale($path));
    $this->assertSame([667, 1000], ImageSupport::dimensions($path));
    $this->assertSame([667, 1000], array_slice(getimagesize($path), 0, 2));
  }

  public function test_a_png_stays_a_png_with_transparency(): void
  {
    $path = $this->make('plan.png', 2000, 4000, 'png');

    $this->assertTrue(ImageSupport::downscale($path));
    $this->assertSame([500, 1000], ImageSupport::dimensions($path));
    $this->assertSame(IMAGETYPE_PNG, getimagesize($path)[2]);
    $this->assertTrue((new Imagick($path))->getImageAlphaChannel());
  }

  public function test_an_image_within_the_limit_is_left_alone(): void
  {
    $path = $this->make('small.jpg', 1000, 800, 'jpg');
    $before = md5_file($path);

    $this->assertFalse(ImageSupport::downscale($path));
    $this->assertSame($before, md5_file($path));
  }

  public function test_no_limit_leaves_every_upload_alone(): void
  {
    config(['images.upload_max' => null]);
    $path = $this->make('photo.jpg', 3000, 2000, 'jpg');

    $this->assertFalse(ImageSupport::downscale($path));
    $this->assertSame([3000, 2000], ImageSupport::dimensions($path));
  }
}
