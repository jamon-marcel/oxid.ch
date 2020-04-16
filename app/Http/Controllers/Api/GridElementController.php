<?php
namespace App\Http\Controllers\Api;
use App\Models\GridElement;
use App\Models\ProjectImage;
use App\Http\Resources\DataCollection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GridElementController extends Controller
{
  protected $gridElement;
  protected $projectImage;

  public function __construct(GridElement $gridElement, ProjectImage $projectImage)
  {
    $this->gridElement = $gridElement;
    $this->projectImage = $projectImage;
  }

  /**
   * Get all records
   *
   * @param int $gridId
   * @return \Illuminate\Http\Response
   */

  public function get($gridId)
  {
    $gridElements = $this->gridElement
                          ->with('image.project')
                          ->byGrid($gridId)
                          ->get();
    return new DataCollection($gridElements);
  }

  /**
   * Store a newly created image.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $item = new GridElement([
        'grid_id'           => $request->get('grid_id'),
        'project_id'        => $request->get('project_id'),
        'project_image_id'  => $request->get('project_image_id'),
        'position'          => $request->get('position')
    ]);
    $item->save();

    // Mark an image as used
    $image = $this->projectImage->find($request->get('project_image_id'));
    $image->is_grid = 1;
    $image->save();

    return response()->json('success');
  }

  /**
   * Delete a grid image
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $element = $this->gridElement->find($id);
    $imageId = $element->project_image_id;
    if ($element->delete())
    {
      $image = $this->projectImage->find($imageId);
      $image->is_grid = 0;
      $image->save();
    }
    return response()->json('successfully deleted');
  }
}
