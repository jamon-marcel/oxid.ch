<?php
namespace App\Http\Controllers\Api;
use App\Models\HomeImage;
use App\Http\Resources\DataCollection;
use App\Http\Requests\HomeImageStoreRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeImageController extends Controller
{
  protected $image;
  
  /**
   * Constructor
   * 
   * @param HomeImage $image
   */

  public function __construct(HomeImage $image)
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
  
  public function store(HomeImageStoreRequest $request)
  {   
    $image = new HomeImage($request->all());
    $image->save();
    return response()->json(['imageId' => $image->id]);
  }

  /**
   * Edit a specified resource.
   *
   * @param HomeImage $image
   * @return \Illuminate\Http\Response
   */
  public function edit(HomeImage $image)
  {
    return response()->json($image);
  }

  /**
   * Update the status of the specified resource.
   *
   * @param HomeImage $image
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function update(HomeImage $image, HomeImageStoreRequest $request)
  {
    $image->update($request->all());
    return response()->json('successfully updated');
  }

  /**
   * Update the status of the specified resource.
   *
   * @param  HomeImage $image
   * @return \Illuminate\Http\Response
   */
  public function status(HomeImage $image)
  {
    $image->publish = $image->publish == 0 ? 1 : 0;
    $image->save();
    return response()->json($image->publish);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  HomeImage $image
   * @return \Illuminate\Http\Response
   */
  public function destroy(HomeImage $image)
  {
    $image->delete();
    return response()->json('successfully deleted');
  }
}
