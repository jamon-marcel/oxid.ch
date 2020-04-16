<?php
namespace App\Http\Controllers\Api;
use App\Models\DiscourseDocument;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DiscourseDocumentController extends Controller
{
  protected $discourseDocument;
  
  /**
   * Constructor
   * 
   * @param DiscourseDocument $discourseDocument
   */

  public function __construct(DiscourseDocument $discourseDocument)
  {
    $this->discourseDocument = $discourseDocument;
  }

  /**
   * Update the status of the specified resource.
   *
   * @param  int $id
   * @return \Illuminate\Http\Response
   */
  public function status($id)
  {
    $discourseDocument = $this->discourseDocument->findOrFail($id);
    $discourseDocument->publish = $discourseDocument->publish == 0 ? 1 : 0;
    $discourseDocument->save();
    return response()->json($discourseDocument->publish);
  }

  /**
   * Remove a specified resource.
   *
   * @param  string $file
   * @return \Illuminate\Http\Response
   */
  public function destroy($file)
  {
    // Delete file from database
    $discourseDocument = $this->discourseDocument->where('name', '=', $file)->first();
    
    if ($discourseDocument)
    {
      $discourseDocument->delete();
    }

    // Delete file from storage
    $directories = Storage::allDirectories('public');
    foreach($directories as $d)
    {
      Storage::delete($d . '/'. $file);
    }
    
    return response()->json('successfully deleted');
  }
}
