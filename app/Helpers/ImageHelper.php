<?php

namespace App\Helpers;
use App\Support\ImageSupport;

/**
 * <picture> markup for /img/crop/... (see ImageController).
 *
 * Every size is a [maxWidth, maxHeight] pair from ImageController::CROP_SIZES:
 * landscape crops scale to the width, portrait ones to the height. The
 * largest candidate, 2400/2400, matches what image-cache served for every
 * size before (longer side 2400), so large screens lose nothing.
 */
class ImageHelper
{
  static function largeImage($image, $caption = NULL)
  {
    return self::picture($image, [[900, 562], [1200, 750], [2400, 2400]], [900, 562], 1600, 1000, $caption);
  }

  static function previewImage($image, $caption = NULL)
  {
    return self::picture($image, [[900, 562], [1600, 1000]], [900, 562], 1600, 1000, $caption);
  }

  static function teaserImage($image, $caption = NULL)
  {
    return self::picture($image, [[1600, 1000], [2400, 2400]], [1600, 1000], 1000, 1600, $caption);
  }

  /**
   * Home images are cropped from home_images, so the URL has no sizes.
   */
  static function homeImage($image)
  {
    $url = '/img/home/' . $image->name;
    $html = '<picture>';
    foreach (ImageSupport::modernFormats() as $format)
    {
      $html .= '<source type="image/' . $format . '" srcset="' . $url . '?fm=' . $format . '">';
    }

    return $html . '<img src="' . $url . '" height="400" width="800" alt=""></picture>';
  }

  static function openGraphImage($image)
  {
    return self::url($image, [1600, 1000]);
  }

  /**
   * One <source> per modern format the server can write, then the <img>
   * with the upload's own format as fallback.
   */
  protected static function picture($image, array $sizes, array $src, int $width, int $height, $caption)
  {
    $html = '<picture>';
    foreach (ImageSupport::modernFormats() as $format)
    {
      $html .= '<source type="image/' . $format . '" srcset="' . self::srcset($image, $sizes, $format) . '">';
    }
    $html .= '<img srcset="' . self::srcset($image, $sizes) . '" src="' . self::url($image, $src) . '" width="' . $width . '" height="' . $height . '" alt="' . e((string) $caption, false) . '">';

    return $html . '</picture>';
  }

  protected static function srcset($image, array $sizes, $format = NULL)
  {
    return implode(', ', array_map(fn ($size) => self::url($image, $size, $format) . ' ' . $size[0] . 'w', $sizes));
  }

  protected static function url($image, array $size, $format = NULL)
  {
    $url = '/img/crop/' . $image->name . '/' . $size[0] . '/' . $size[1];
    if ($image->coords_w && $image->coords_h)
    {
      $url .= '/' . implode(',', array_map(fn ($v) => (int) floor((float) $v), [$image->coords_w, $image->coords_h, $image->coords_x, $image->coords_y]));
    }

    return $format ? $url . '?fm=' . $format : $url;
  }
}
