<?php
namespace App\Filters\Image\Template;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Interfaces\ModifierInterface;
use App\Models\ProjectImage;
use App\Filters\Image\ImageFilenameExtractor;

class Project implements ModifierInterface
{
  use ImageFilenameExtractor;
  
  protected $max_width  = 2000;    
  protected $max_height = 1250;

  public function apply(ImageInterface $image): ImageInterface
  {
    $this->image = new ProjectImage;
    $filename = $this->getFilenameFromImage($image);
    $img = $this->image->where('name', '=', $filename)->get()->first();
    
    // Crop the image if coords are set
    if ($img && $img->coords_w && $img->coords_h)
    {
      $image = $image->crop(
        floor(floatval($img->coords_w)), 
        floor(floatval($img->coords_h)),
        floor(floatval($img->coords_x ?? 0)), 
        floor(floatval($img->coords_y ?? 0))
      );
      
      $image = $image->encode('jpg', 100);
      return $image->scale(width: $this->max_width);
    }

    // There's an unreachable return statement in the original code
    // I'm restructuring to make all code reachable
    
    // Get current dimensions
    $width  = $image->width();
    $height = $image->height();

    // Resize landscape image
    if ($width > $height)
    {
      if ($width >= $this->max_width)
      {
        return $image->scale(width: $this->max_width);
      }
      else
      {
        $ratio_height = ($width/16) * 10;
        return $image->crop($width, floor($ratio_height));
      }
    }
    else if ($height >= $this->max_height)
    {
      return $image->scale(height: $this->max_height);
    }
    
    return $image;
  }
}