<?php
namespace App\Http\Controllers\Api;
use App\Models\JobImage;
use App\Http\Resources\DataCollection;
use App\Http\Requests\JobImageStoreRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JobImageController extends Controller
{
  protected $image;
  
  /**
   * Constructor
   * 
   * @param JobImage $image
   */

  public function __construct(JobImage $image)
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
    return new DataCollection($this->image->get());
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  
  public function store(JobImageStoreRequest $request)
  {   
    $image = new JobImage($request->all());
    $image->save();
    return response()->json(['imageId' => $image->id]);
  }

  /**
   * Edit a specified resource.
   *
   * @param JobImage $image
   * @return \Illuminate\Http\Response
   */
  public function edit(JobImage $image)
  {
    return response()->json($image);
  }

  /**
   * Update the status of the specified resource.
   *
   * @param JobImage $image
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function update(JobImage $image, JobImageStoreRequest $request)
  {
    $image->update($request->all());
    return response()->json('successfully updated');
  }

  /**
   * Update the status of the specified resource.
   *
   * @param  JobImage $image
   * @return \Illuminate\Http\Response
   */
  public function status(JobImage $image)
  {
    $image->publish = $image->publish == 0 ? 1 : 0;
    $image->save();
    return response()->json($image->publish);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  JobImage $image
   * @return \Illuminate\Http\Response
   */
  public function destroy(JobImage $image)
  {
    $image->delete();
    $directories = Storage::allDirectories('public');
    foreach($directories as $d)
    {
      Storage::delete($d . '/'. $image->name);
    }
    return response()->json('successfully deleted');
  }
}
