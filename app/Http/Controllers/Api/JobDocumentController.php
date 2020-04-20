<?php
namespace App\Http\Controllers\Api;
use App\Models\JobDocument;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JobDocumentController extends Controller
{
  protected $jobDocument;
  
  /**
   * Constructor
   * 
   * @param JobDocument $jobDocument
   */

  public function __construct(JobDocument $jobDocument)
  {
    $this->jobDocument = $jobDocument;
  }

  /**
   * Update the status of the specified resource.
   *
   * @param  int $id
   * @return \Illuminate\Http\Response
   */
  public function status($id)
  {
    $jobDocument = $this->jobDocument->findOrFail($id);
    $jobDocument->publish = $jobDocument->publish == 0 ? 1 : 0;
    $jobDocument->save();
    return response()->json($jobDocument->publish);
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
    $jobDocument = $this->jobDocument->where('name', '=', $file)->first();
    
    if ($jobDocument)
    {
      $jobDocument->delete();
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
