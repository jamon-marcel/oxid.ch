<?php
namespace App\Filters\Image\Template;
use Intervention\Image\Image;
use Intervention\Image\Filters\FilterInterface;
use App\Models\HomeImage;

class Home implements FilterInterface
{
  protected $max_width  = 2000;    
  protected $max_height = 1250;

  public function applyFilter(Image $image)
  {
    $this->image = new \App\Models\HomeImage;
    $img = $this->image->where('name', '=', $image->basename)->get()->first();
    
    // Crop the image if coords are set
    if ($img && $img->coords_w && $img->coords_h)
    {
      $image->crop(floor($img->coords_w), floor($img->coords_h), floor($img->coords_x), floor($img->coords_y))
            ->resize(null, $this->max_width, function ($constraint) {
        return $constraint->aspectRatio();
      });
    }

    // Otherwise just resize the image
    $width  = $image->getWidth();
    $height = $image->getHeight();

    // Resize landscape image
    if ($width > $height && $width >= $this->max_width)
    {
      $image->resize($this->max_width, null, function ($constraint) {
        return $constraint->aspectRatio();
      });
    }
    else if ($height >= $this->max_height)
    {
      $image->resize(null, $this->max_height, function ($constraint) {
        return $constraint->aspectRatio();
      });
    }
    return $image;

  }
}