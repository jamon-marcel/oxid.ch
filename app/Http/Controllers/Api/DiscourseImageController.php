<?php
namespace App\Http\Controllers\Api;
use App\Models\DiscourseImage;
use App\Http\Resources\DataCollection;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DiscourseImageController extends Controller
{
  protected $discourseImage;
  
  /**
   * Constructor
   * 
   * @param DiscourseImage $discourseImage
   */

  public function __construct(DiscourseImage $discourseImage)
  {
    $this->discourseImage = $discourseImage;
  }

    /**
     * Get all images by discourse
     *
     * @param int $discourseId
     * @return \Illuminate\Http\Response
     */

    public function get($discourseId = NULL)
    {
      $discourseImages = $this->discourseImage
                            ->where('discourse_id', '=', $discourseId)
                            ->where('publish', '=', 1)
                            ->get();
      return new DataCollection($discourseImages);
    }

  /**
   * Update the status of the specified resource.
   *
   * @param  int $id
   * @return \Illuminate\Http\Response
   */
  public function status($id)
  {
    $discourseImage = $this->discourseImage->findOrFail($id);
    $discourseImage->publish = $discourseImage->publish == 0 ? 1 : 0;
    $discourseImage->save();
    return response()->json($discourseImage->publish);
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
    $discourseImage = $this->discourseImage->where('name', '=', $image)->first();
    
    if ($discourseImage)
    {
      $discourseImage->delete();
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
