<?php
namespace App\Filters\Image\Template;
use Intervention\Image\Image;
use Intervention\Image\Filters\FilterInterface;

class CropLandscapeMedium implements FilterInterface
{
  /**
   * Maximum width for large landscape images
   */    
  protected $width = 1000;    

  /**
   * Maximum height for large portrait images
   */    
  protected $height = 680;

  public function applyFilter(Image $image)
  {
    return $image->crop($this->width, $this->height);
  }
}