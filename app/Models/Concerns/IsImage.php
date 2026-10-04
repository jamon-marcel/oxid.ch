<?php

namespace App\Models\Concerns;

use App\Support\Glide;
use App\Support\ImageSupport;

/**
 * An upload in storage/app/public/uploads with an optional crop
 * (coords_w/h/x/y) and its size after EXIF orientation (width/height).
 *
 * A size is a longer side: the image is scaled down to fit inside
 * size x size, never up.
 */
trait IsImage
{
  public static function bootIsImage(): void
  {
    static::saving(function ($image) {
      if ($image->isDirty('name') || ! $image->width || ! $image->height) {
        [$image->width, $image->height] = ImageSupport::dimensions($image->path()) ?? [null, null];
      }
    });
  }

  public function path(): string
  {
    return storage_path('app/public/uploads/' . $this->name);
  }

  /**
   * [w, h, x, y] as ints, or null when the image is not cropped.
   */
  public function crop(): ?array
  {
    if (! $this->coords_w || ! $this->coords_h) {
      return null;
    }

    return array_map(
      fn ($v) => max(0, (int) floor((float) $v)),
      [$this->coords_w, $this->coords_h, $this->coords_x, $this->coords_y]
    );
  }

  /**
   * [width, height] after the crop, or null when the upload is unreadable.
   * Like Glide, a crop reaching past the edges is cut at them.
   */
  public function displaySize(): ?array
  {
    $size = $this->width && $this->height
      ? [$this->width, $this->height]
      : ImageSupport::dimensions($this->path());

    if (! $crop = $this->crop()) {
      return $size;
    }

    [$width, $height, $x, $y] = $crop;

    return $size ? [min($width, $size[0] - $x), min($height, $size[1] - $y)] : [$width, $height];
  }

  public function url(int $size, ?string $format = null): string
  {
    return Glide::url($this->name, $size, $this->crop(), $format);
  }

  /**
   * One candidate per size, described by its real width. Sizes beyond the
   * image's own collapse into one candidate.
   */
  public function srcset(array $sizes, ?string $format = null): string
  {
    $candidates = [];
    foreach ($sizes as $size) {
      $width = $this->scaledWidth($size);
      $candidates[$width] ??= $this->url($size, $format) . ' ' . $width . 'w';
    }

    return implode(', ', $candidates);
  }

  protected function scaledWidth(int $size): int
  {
    [$width, $height] = $this->displaySize() ?? [$size, $size];

    return (int) round($width * min(1, $size / max($width, $height)));
  }
}
