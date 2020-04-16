<?php
namespace App\Http\Controllers\Api;
use App\Models\TeamDocument;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TeamDocumentController extends Controller
{
  protected $teamDocument;
  
  /**
   * Constructor
   * 
   * @param TeamDocument $teamDocument
   */

  public function __construct(TeamDocument $teamDocument)
  {
    $this->teamDocument = $teamDocument;
  }

  /**
   * Update the status of the specified resource.
   *
   * @param  int $id
   * @return \Illuminate\Http\Response
   */
  public function status($id)
  {
    $teamDocument = $this->teamDocument->findOrFail($id);
    $teamDocument->publish = $teamDocument->publish == 0 ? 1 : 0;
    $teamDocument->save();
    return response()->json($teamDocument->publish);
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
    $teamDocument = $this->teamDocument->where('name', '=', $file)->first();
    
    if ($teamDocument)
    {
      $teamDocument->delete();
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
