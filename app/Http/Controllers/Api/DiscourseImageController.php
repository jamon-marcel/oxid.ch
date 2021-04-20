<?php
namespace App\Http\Controllers\Api;
use App\Models\DiscourseImage;
use App\Http\Resources\DataCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
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
   * Update the cropping coords of the specified resource.
   *
   * @param DiscourseImage $discourseImage
   * @param  \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */
  public function coords(DiscourseImage $discourseImage, Request $request)
  {
    $image = $this->discourseImage->findOrFail($discourseImage->id);
    $image->coords_w = round($request->input('coords_w'), 12);
    $image->coords_h = round($request->input('coords_h'), 12);
    $image->coords_x = round($request->input('coords_x'), 12);
    $image->coords_y = round($request->input('coords_y'), 12);
    $image->save();

    $this->removeCachedImage($image);

    return response()->json('successfully updated');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  string $image
   * @return \Illuminate\Http\Response
   */
  
  public function destroy($image)
  {
    // Delete image from database
    $record = $this->discourseImage->where('name', '=', $image)->first();
    
    if ($record)
    {
      $record->delete();
    }

    // Delete file from storage
    $directories = Storage::allDirectories('public');
    foreach($directories as $d)
    {
      Storage::delete($d . '/'. $image);
    }
    
    return response()->json('successfully deleted');
  }

  /**
   * Update the order of the resources.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */

  public function order(Request $request)
  {
    $images = $request->get('images');
    foreach($images as $image)
    {
      $i = $this->discourseImage->find($image['id']);
      $i->order = $image['order'];
      $i->save(); 
    }
    return response()->json('successfully updated');
  }


  /**
   * Remove cached version of the image
   *
   * @param DiscourseImage $discourseImage
   * @param  \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */
  private function removeCachedImage(DiscourseImage $image)
  {
    // Get an instance of the ImageCache class
    $imageCache = new \Intervention\Image\ImageCache();

    // Get a cached image from it and apply all of your templates / methods
    $image = $imageCache->make(storage_path('app/public/uploads/') . $image->name)->filter(new \App\Filters\Image\Template\Cache);

    // Remove the image from the cache by using its internal checksum
    Cache::forget($image->checksum());
  }
}
