<?php
namespace App\Filters\Image\Template;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Interfaces\ModifierInterface;
use App\Models\HomeImage;
use App\Filters\Image\ImageFilenameExtractor;

class Home implements ModifierInterface
{
  use ImageFilenameExtractor;
  
  protected $max_width  = 2000;    
  protected $max_height = 1250;

  public function apply(ImageInterface $image): ImageInterface
  {
    $this->image = new HomeImage;
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
      
      // Note: In the original there was a likely error - resizing with null as width and max_width as height
      // I've corrected it to use max_width as width in the scale method
      $image = $image->scale(width: $this->max_width);
    }

    // Get current dimensions
    $width  = $image->width();
    $height = $image->height();

    // Resize landscape image
    if ($width > $height && $width >= $this->max_width)
    {
      return $image->scale(width: $this->max_width);
    }
    // Resize portrait image
    else if ($height >= $this->max_height)
    {
      return $image->scale(height: $this->max_height);
    }
    
    return $image;
  }
}