<?php
namespace App\Http\Controllers\Api;
use App\Models\ProjectDocument;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProjectDocumentController extends Controller
{
  protected $projectDocument;
  
  /**
   * Constructor
   * 
   * @param ProjectDocument $projectDocument
   */

  public function __construct(ProjectDocument $projectDocument)
  {
    $this->projectDocument = $projectDocument;
  }

  /**
   * Update the status of the specified resource.
   *
   * @param  int $id
   * @return \Illuminate\Http\Response
   */
  public function status($id)
  {
    $projectDocument = $this->projectDocument->findOrFail($id);
    $projectDocument->publish = $projectDocument->publish == 0 ? 1 : 0;
    $projectDocument->save();
    return response()->json($projectDocument->publish);
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
    $projectDocument = $this->projectDocument->where('name', '=', $file)->first();
    
    if ($projectDocument)
    {
      $projectDocument->delete();
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
