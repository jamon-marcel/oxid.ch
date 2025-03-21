<?php

namespace App\Helpers;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class ImageHelper
{
  static function largeImage($image, $caption = NULL)
  {
    $coords = '';
    if ($image->coords_w && $image->coords_h)
    {
      $coords = floor($image->coords_w) . ',' .  floor($image->coords_h) . ',' .  floor($image->coords_x) . ',' .  floor($image->coords_y);
      return '<img srcset="/img/crop/'.$image->name.'/1200/750/'.$coords.' 900w, /img/crop/'.$image->name.'/2400/1500/'.$coords.' 2400w" src="/img/crop/'.$image->name.'/900/562/'.$coords.'" width="1600" height="1000" alt="'. $caption.'">';
    }
    
    return '<img srcset="/img/crop/'.$image->name.'/1200/750 900w, /img/crop/'.$image->name.'/2400/1500 2400w" src="/img/crop/'.$image->name.'/900/562" width="1600" height="1000" alt="'. $caption.'">';
  }

  static function previewImage($image, $caption = NULL)
  {
    $coords = '';
    if ($image->coords_w && $image->coords_h)
    {
      $coords = floor($image->coords_w) . ',' .  floor($image->coords_h) . ',' .  floor($image->coords_x) . ',' .  floor($image->coords_y);
      return '<img src="/img/crop/'.$image->name.'/900/562/'.$coords.'" width="1600" height="1000" alt="'. $caption.'">';
    }
    
    return '<img src="/img/crop/'.$image->name.'/900/562" width="1600" height="1000" alt="'. $caption.'">';
  }

  static function teaserImage($image, $caption = NULL)
  {
    $coords = '';
    if ($image->coords_w && $image->coords_h)
    {
      $coords = floor($image->coords_w) . ',' .  floor($image->coords_h) . ',' .  floor($image->coords_x) . ',' .  floor($image->coords_y);
      return '<img src="/img/crop/'.$image->name.'/1600/1000/'.$coords.'" width="1000" height="1600" alt="'. $caption.'">';
    }
    
    return '<img src="/img/crop/'.$image->name.'/1600/1000" width="1000" height="1600" alt="'. $caption.'">';
  }

  static function openGraphImage($image)
  {
    return "/img/crop/".$image->name."/1600/1000";
  }
}