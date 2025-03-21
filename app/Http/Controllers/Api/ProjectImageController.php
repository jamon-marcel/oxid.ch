<?php
namespace App\Http\Controllers\Api;
use App\Models\ProjectImage;
use App\Http\Resources\DataCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use MarceliTo\ImageCache\Facades\ImageCache;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProjectImageController extends Controller
{
  protected $projectImage;
  
  /**
   * Constructor
   * 
   * @param ProjectImage $projectImage
   */

  public function __construct(ProjectImage $projectImage)
  {
    $this->projectImage = $projectImage;
  }

    /**
     * Get all images by project
     *
     * @param int $projectId
     * @return \Illuminate\Http\Response
     */

    public function get($projectId = NULL)
    {
      $projectImages = $this->projectImage
                            ->where('project_id', '=', $projectId)
                            ->where('publish', '=', 1)
                            ->get();
      return new DataCollection($projectImages);
    }

  /**
   * Update the status of the specified resource.
   *
   * @param  int $id
   * @return \Illuminate\Http\Response
   */
  public function status($id)
  {
    $projectImage = $this->projectImage->findOrFail($id);
    $projectImage->publish = $projectImage->publish == 0 ? 1 : 0;
    $projectImage->save();
    return response()->json($projectImage->publish);
  }

  /**
   * Update the cropping coords of the specified resource.
   *
   * @param ProjectImage $projectImage
   * @param  \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */
  public function coords(ProjectImage $projectImage, Request $request)
  {
    $image = $this->projectImage->findOrFail($projectImage->id);
    $image->coords_w = round($request->input('coords_w'), 12);
    $image->coords_h = round($request->input('coords_h'), 12);
    $image->coords_x = round($request->input('coords_x'), 12);
    $image->coords_y = round($request->input('coords_y'), 12);
    $image->save();
    $this->removeCachedImage($image);
    return response()->json('successfully updated');
  }

  /**
   * Remove a specified resource.
   *
   * @param  string $image
   * @return \Illuminate\Http\Response
   */
  public function destroy($image)
  {
    // Delete image from database
    $projectImage = $this->projectImage->where('name', '=', $image)->first();
    
    if ($projectImage)
    {
      $projectImage->delete();
    }

    // Delete image from storage
    $directories = Storage::allDirectories('public');
    foreach($directories as $d)
    {
      Storage::delete($d . '/'. $image);
    }
    
    return response()->json('successfully deleted');
  }

  /**
   * Remove cached version of the image
   *
   * @param ProjectImage $image
   * @return void
   */
  private function removeCachedImage(ProjectImage $image)
  {
    // Try the package method first
    ImageCache::clearImageCache($image->name);
    
    // Also manually clear cache from all template directories
    $cachePath = storage_path('app/public/cache');
    $templateDirs = File::directories($cachePath);
    
    foreach ($templateDirs as $dir) {
      $files = File::glob($dir . '/*' . $image->name . '*');
      foreach ($files as $file) {
        File::delete($file);
      }
    }
  }
}
