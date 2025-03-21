<?php
namespace App\Http\Controllers\Api;
use App\Models\ProfileImage;
use App\Http\Resources\DataCollection;
use App\Http\Requests\ProfileImageStoreRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use MarceliTo\ImageCache\Facades\ImageCache;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProfileImageController extends Controller
{
  protected $image;
  
  /**
   * Constructor
   * 
   * @param ProfileImage $image
   */

  public function __construct(ProfileImage $image)
  {
    $this->image = $image;
  }

  /**
   * Get all records
   *
   * @return \Illuminate\Http\Response
   */

  public function get()
  {
    return new DataCollection($this->image->orderBy('order')->get());
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  
  public function store(ProfileImageStoreRequest $request)
  {   
    $image = new ProfileImage($request->all());
    $image->save();
    return response()->json(['imageId' => $image->id]);
  }

  /**
   * Edit a specified resource.
   *
   * @param ProfileImage $image
   * @return \Illuminate\Http\Response
   */

  public function edit(ProfileImage $image)
  {
    return response()->json($image);
  }

  /**
   * Update the status of the specified resource.
   *
   * @param ProfileImage $image
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function update(ProfileImage $image, ProfileImageStoreRequest $request)
  {
    $image->update($request->all());
    return response()->json('successfully updated');
  }

  /**
   * Update the status of the specified resource.
   *
   * @param  ProfileImage $image
   * @return \Illuminate\Http\Response
   */

  public function status(ProfileImage $image)
  {
    $image->publish = $image->publish == 0 ? 1 : 0;
    $image->save();
    return response()->json($image->publish);
  }

  /**
   * Update the cropping coords of the specified resource.
   *
   * @param ProfileImage $profileImage
   * @param  \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */
  public function coords(ProfileImage $profileImage, Request $request)
  {
    $image = $this->image->findOrFail($profileImage->id);
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
    $record = $this->image->where('name', '=', $image)->first();
    
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
      $i = $this->image->find($image['id']);
      $i->order = $image['order'];
      $i->save(); 
    }
    return response()->json('successfully updated');
  }

  /**
   * Remove cached version of the image
   *
   * @param ProfileImage $image
   * @return void
   */
  private function removeCachedImage(ProfileImage $image)
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
