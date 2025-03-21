<?php

namespace App\Filters\Image;

use Intervention\Image\Interfaces\ImageInterface;

/**
 * Trait to extract filenames from Intervention Image v3 objects
 * 
 * This trait provides a consistent way to extract filenames from image objects
 * since the basePath() method was removed in Intervention Image v3.
 */
trait ImageFilenameExtractor
{
    /**
     * Extract the filename from an image object
     *
     * @param ImageInterface $image
     * @return string
     */
    protected function getFilenameFromImage(ImageInterface $image): string
    {
        // Try to get it from the core object if available
        if (method_exists($image, 'core') && method_exists($image->core(), 'basename')) {
            return $image->core()->basename();
        }
        
        // If the image has a filename property or method
        if (method_exists($image, 'filename')) {
            return $image->filename();
        }
        
        // Try to get it from the request parameters
        if (request()->has('filename')) {
            return request()->get('filename');
        }
        
        // Last resort: try to extract it from the request path
        $path = request()->path();
        $pathParts = explode('/', $path);
        
        // The filename should be the last part of the path
        if (!empty($pathParts)) {
            return end($pathParts);
        }
        
        // If all else fails, return a placeholder
        return 'unknown';
    }
}
