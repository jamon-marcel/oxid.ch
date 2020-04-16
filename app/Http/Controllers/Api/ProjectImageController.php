<?php
namespace App\Http\Controllers\Api;
use App\Models\ProjectImage;
use App\Http\Resources\DataCollection;
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
