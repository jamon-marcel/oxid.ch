<?php
namespace App\Http\Controllers\Api;
use App\Services\ImageService;
use App\Models\ProjectImage;
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
   * Update the status of the specified resource.
   *
   * @param  ProjectImage $projectImage
   * @return \Illuminate\Http\Response
   */
  public function status(ProjectImage $projectImage)
  {
    $projectImage->publish = $projectImage->publish == 0 ? 1 : 0;
    $projectImage->save();
    return response()->json($projectImage->publish);
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
