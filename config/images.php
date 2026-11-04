<?php

/**
 * Image presets for <x-image>: the longer sides offered in the srcset,
 * smallest first. Each is scaled to fit inside size x size, never up.
 */
return [
  'presets' => [
    'large' => [900, 1200, 2400],
    'preview' => [900, 1600],
    'teaser' => [1600, 2400],
    'home' => [1200, 2000],
  ],

  /*
   * Uploaded JPEG/PNG images are scaled down to this longer side (px). The
   * site shows at most 2400; the rest leaves room for crops.
   */
  'upload_max' => 4000,

  /*
   * Largest AVIF (longer side, px) this server renders itself; larger cold
   * renders are served as WebP. Unset = no limit. Production: 800, its
   * ImageMagick writes empty AVIFs or crashes from about 1000 px.
   */
  'avif_max' => env('IMAGES_AVIF_MAX') ? (int) env('IMAGES_AVIF_MAX') : null,
];
