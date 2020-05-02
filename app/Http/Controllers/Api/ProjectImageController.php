<?php
namespace App\Http\Controllers\Api;
use App\Models\ProjectImage;
use App\Http\Resources\DataCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
   * Update the status of the specified resource.
   *
   * @param ProjectImage $projectImage
   * @param  \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */
  public function coords(ProjectImage $projectImage, Request $request)
  {
    $image = $this->projectImage->findOrFail($projectImage->id);
    $image->coords_w = $request->input('coords_w');
    $image->coords_h = $request->input('coords_h');
    $image->coords_x = $request->input('coords_x');
    $image->coords_y = $request->input('coords_y');
    $image->save();

    // get an instance of the ImageCache class
    $imageCache = new \Intervention\Image\ImageCache();

    // get a cached image from it and apply all of your templates / methods
    $image = $imageCache->make(storage_path('app/public/uploads/') . $image->name)->filter(new \App\Filters\Image\Template\Crop);

    // remove the image from the cache by using its internal checksum
    Cache::forget($image->checksum());
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
}
