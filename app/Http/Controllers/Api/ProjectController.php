<?php
namespace App\Http\Controllers\Api;
use App\Models\Project;
use App\Http\Resources\DataCollection;
use App\Http\Requests\ProjectStoreRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
  protected $project;
  
  /**
   * Constructor
   * 
   * @param Project $project
   */

  public function __construct(Project $project)
  {
    $this->project = $project;
  }

  /**
   * Get all records
   *
   * @return \Illuminate\Http\Response
   */

  public function get()
  {
    return new DataCollection($this->project->get());
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  
  public function store(ProjectStoreRequest $request)
  {   
    $project = new Project($request->all());
    $project->save();
    return response()->json(['projectId' => $project->id]);
  }

  /**
   * Edit a specified resource.
   *
   * @param Project $project
   * @return \Illuminate\Http\Response
   */
  public function edit(Project $project)
  {
    return response()->json($project);
  }

  /**
   * Update the status of the specified resource.
   *
   * @param Project $project
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function update(Project $project, ProjectStoreRequest $request)
  {
    $project->update($request->all());
    return response()->json('successfully updated');
  }

  /**
   * Update the status of the specified resource.
   *
   * @param  Project $project
   * @return \Illuminate\Http\Response
   */
  public function status(Project $project)
  {
    $project->publish = $project->publish == 0 ? 1 : 0;
    $project->save();
    return response()->json($project->publish);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  Project $project
   * @return \Illuminate\Http\Response
   */
  public function destroy(Project $project)
  {
    $project->delete();
    return response()->json('successfully deleted');
  }
}
