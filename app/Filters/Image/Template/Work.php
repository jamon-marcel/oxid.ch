<?php
namespace App\Filters\Image\Template;
use Intervention\Image\Image;
use Intervention\Image\Filters\FilterInterface;
use App\Models\ProjectImage;

class Work implements FilterInterface
{
  protected $max_width  = 2000;    
  protected $max_height = 1250;

  public function applyFilter(Image $image)
  {
    $this->image = new \App\Models\ProjectImage;
    $img = $this->image->where('name', '=', $image->basename)->get()->first();
    
    // Crop the image if coords are set
    if ($img && $img->coords_w && $img->coords_h)
    {
      $image->crop(floor($img->coords_w), floor($img->coords_h), floor($img->coords_x), floor($img->coords_y))
            ->resize($this->max_width, null, function ($constraint) {
        return $constraint->aspectRatio();
      });
    }

    // Otherwise just resize the image
    $width  = $image->getWidth();
    $height = $image->getHeight();

    // Resize landscape image
    if ($width > $height)
    {
      if ($width >= $this->max_width)
      {
        return $image->crop($this->max_width, $this->max_height);
      }
      else
      {
        $ratio_height = ($width/16) * 10;
        return $image->crop($width, $ratio_height);
      }
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