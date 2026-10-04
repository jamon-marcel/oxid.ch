<?php

namespace Tests\Unit;

use App\Models\ProjectImage;
use App\Support\Glide;
use Tests\TestCase;

class ImageTest extends TestCase
{
  protected function image(array $attributes): ProjectImage
  {
    return (new ProjectImage)->forceFill($attributes + ['name' => 'test.jpg']);
  }

  public function test_crop_is_floored_and_needs_a_width_and_height(): void
  {
    $this->assertSame([1333, 1775, 0, 90], $this->image(['coords_w' => '1333.9', 'coords_h' => 1775.2, 'coords_x' => null, 'coords_y' => '90.7'])->crop());
    $this->assertNull($this->image(['coords_w' => 0, 'coords_h' => 1775])->crop());
    $this->assertNull($this->image([])->crop());
  }

  public function test_display_size_is_the_crop_cut_at_the_edges(): void
  {
    $this->assertSame([3000, 2000], $this->image(['width' => 3000, 'height' => 2000])->displaySize());
    $this->assertSame([1000, 800], $this->image(['width' => 3000, 'height' => 2000, 'coords_w' => 1000, 'coords_h' => 800, 'coords_x' => 10, 'coords_y' => 10])->displaySize());
    $this->assertSame([500, 400], $this->image(['width' => 3000, 'height' => 2000, 'coords_w' => 1000, 'coords_h' => 800, 'coords_x' => 2500, 'coords_y' => 1600])->displaySize());
  }

  public function test_srcset_describes_real_widths_and_collapses_upscales(): void
  {
    $portrait = $this->image(['width' => 2000, 'height' => 3000]);
    $this->assertSame(
      [$portrait->url(900) . ' 600w', $portrait->url(2400) . ' 1600w'],
      explode(', ', $portrait->srcset([900, 2400]))
    );

    $small = $this->image(['width' => 1000, 'height' => 800]);
    $this->assertSame([$small->url(900) . ' 900w', $small->url(1200) . ' 1000w'], explode(', ', $small->srcset([900, 1200, 2400])));
  }

  public function test_urls_are_signed_over_every_parameter(): void
  {
    $url = $this->image(['coords_w' => 100, 'coords_h' => 50, 'coords_x' => 1, 'coords_y' => 2])->url(900, 'avif');
    parse_str(parse_url($url, PHP_URL_QUERY), $params);

    $this->assertStringStartsWith('/img/test.jpg?', $url);
    $this->assertSame(['w' => '900', 'h' => '900', 'fit' => 'max', 'crop' => '100,50,1,2', 'fm' => 'avif'], array_diff_key($params, ['s' => 1]));

    Glide::signature()->validateRequest('img/test.jpg', $params);
    $this->expectException(\League\Glide\Signatures\SignatureException::class);
    Glide::signature()->validateRequest('img/test.jpg', ['w' => '901'] + $params);
  }
}
