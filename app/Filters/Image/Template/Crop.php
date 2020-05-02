<?php
namespace App\Filters\Image\Template;
use Intervention\Image\Image;
use Intervention\Image\Filters\FilterInterface;
use App\Models\ProjectImage;

class Crop implements FilterInterface
{
  public function applyFilter(Image $image)
  {
    $this->image = new \App\Models\ProjectImage;
    $img = $this->image->where('name', '=', $image->basename)->get()->first();
    return $image->crop(floor($img->coords_w), floor($img->coords_h), floor($img->coords_x), floor($img->coords_y));
  }
}