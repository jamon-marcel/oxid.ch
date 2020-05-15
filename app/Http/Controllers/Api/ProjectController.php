<?php
namespace App\Http\Controllers\Api;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\ProjectDocument;
use App\Http\Resources\DataCollection;
use App\Http\Requests\ProjectStoreRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
  protected $project;
  protected $projectImage;
  protected $projectDocument;
  
  /**
   * Constructor
   * 
   * @param Project $project
   */

  public function __construct(Project $project, ProjectImage $projectImage, ProjectDocument $projectDocument)
  {
    $this->project = $project;
    $this->projectImage = $projectImage;
    $this->projectDocument = $projectDocument;
  }

  /**
   * Get all records
   *
   * @return \Illuminate\Http\Response
   */

  public function get()
  {
    return new DataCollection($this->project->with('images')->with('documents')->orderBy('is_highlight', 'DESC')->get());
  }

  /**
   * Fetch one record
   *
   * @param Project $project
   * @return \Illuminate\Http\Response
   */

  public function getOne(Project $project)
  {
    return response()->json($project);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  
  public function store(Request $request)
  {   
    $project = new Project([
      'title' => [
        'de' => $request->input('title.de'),
        'en' => $request->input('title.en'),
      ],
      'title_short' => [
        'de' => $request->input('title_short.de'),
        'en' => $request->input('title_short.en'),
      ],
      'location' => [
        'de' => $request->input('location.de'),
        'en' => $request->input('location.en'),
      ],
      'description' => [
        'de' => $request->input('description.de'),
        'en' => $request->input('description.en'),
      ],
      'info' => [
        'de' => $request->input('info.de'),
        'en' => $request->input('info.en'),
      ],
      'year'            => $request->input('year'),
      'year_works'      => $request->input('year_works'),
      'client_works'    => $request->input('client_works'),
      'principal_works' => $request->input('principal_works'),
      'program'         => $request->input('program'),
      'state'           => $request->input('state'),
      'author'          => $request->input('author'),
      'is_filter_wood'  => $request->input('is_filter_wood'),
      'is_filter_reuse' => $request->input('is_filter_reuse'),
      'is_filter_area'  => $request->input('is_filter_area'),
      'has_detail'      => $request->input('has_detail'),
      'is_highlight'    => $request->input('is_highlight'),
      'publish'         => $request->input('publish'),
    ]);
    $project->save();
    
    // Images
    if (!empty($request->images))
    {
      foreach($request->images as $i)
      {
        $image = new ProjectImage([
          'project_id'  => $project->id,
          'name'        => $i['name'],
          'caption' => [
            'de' => $i['caption']['de'],
            'en' => $i['caption']['en'],    
          ],
          'is_preview_navigation' => $i['is_preview_navigation'],
          'is_preview_works'      => $i['is_preview_works'],
          'is_plan'               => $i['is_plan'],
          'coords_w'              => $i['coords_w'],
          'coords_h'              => $i['coords_h'],
          'coords_x'              => $i['coords_x'],
          'coords_y'              => $i['coords_y'],
          'orientation'           => $i['orientation'],
          'publish'               => $i['publish'],
        ]);
        $image->save();
      }
    }

    // Documents
    if (!empty($request->documents))
    {
      foreach($request->documents as $i)
      {
        $document = new ProjectDocument([
          'project_id'  => $project->id,
          'name'        => $i['name'],
          'caption' => [
            'de' => $i['caption']['de'],
            'en' => $i['caption']['en'],    
          ],
          'publish' => $i['publish'],
        ]);
        $document->save();
      }
    }

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
    $project = $this->project->with('images')->with('documents')->findOrFail($project->id);
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
    $project = $this->project->findOrFail($project->id);
    
    // German
    $project->setTranslation('title', 'de', $request->input('title.de'));
    $project->setTranslation('title_short', 'de', $request->input('title_short.de'));
    $project->setTranslation('location', 'de', $request->input('location.de'));
    $project->setTranslation('description', 'de', $request->input('description.de'));
    $project->setTranslation('info', 'de', $request->input('info.de'));

    // English
    $project->setTranslation('title', 'en', $request->input('title.en'));
    $project->setTranslation('title_short', 'en', $request->input('title_short.en'));
    $project->setTranslation('location', 'en', $request->input('location.en'));
    $project->setTranslation('description', 'en', $request->input('description.en'));
    $project->setTranslation('info', 'en', $request->input('info.en'));

    $project->year              = $request->input('year');
    $project->program           = $request->input('program');
    $project->state             = $request->input('state');
    $project->author            = $request->input('author');
    $project->year_works        = $request->input('year_works');
    $project->client_works      = $request->input('client_works');
    $project->principal_works   = $request->input('principal_works');
    $project->is_filter_wood    = $request->input('is_filter_wood');
    $project->is_filter_reuse   = $request->input('is_filter_reuse');
    $project->is_filter_area    = $request->input('is_filter_area');
    $project->has_detail        = $request->input('has_detail');
    $project->is_highlight      = $request->input('is_highlight');
    $project->publish           = $request->input('publish');
    $project->save();

    // Images
    if (!empty($request->images))
    {
      foreach($request->images as $i)
      { 
        $image = ProjectImage::updateOrCreate(
          ['id' => $i['id']], 
          [
            'project_id' => $project->id,
            'name' => $i['name'],
            'caption' => [
              'de' => $i['caption']['de'],
              'en' => $i['caption']['en']
            ],
            'publish'                 => $i['publish'] ? $i['publish'] : 0,
            'is_preview_navigation'   => $i['is_preview_navigation'] ? $i['is_preview_navigation'] : 0,
            'is_preview_works'        => $i['is_preview_works'] ? $i['is_preview_works'] : 0,
            'is_plan'                 => $i['is_plan'] ? $i['is_plan'] : 0,
            'coords_w'                => $i['coords_w'] ? $i['coords_w'] : NULL,
            'coords_h'                => $i['coords_h'] ? $i['coords_h'] : NULL,
            'coords_x'                => $i['coords_x'] ? $i['coords_x'] : NULL,
            'coords_y'                => $i['coords_y'] ? $i['coords_y'] : NULL,
            'orientation'             => $i['orientation'] ? $i['orientation'] : NULL,
          ]
        );
      }
    }
    
    // Documents
    if (!empty($request->documents))
    {
      foreach($request->documents as $i)
      {
        $document = ProjectDocument::updateOrCreate(
          ['id' => $i['id']], 
          [
            'project_id' => $project->id,
            'name' => $i['name'],
            'caption' => [
              'de' => $i['caption']['de'],
              'en' => $i['caption']['en']
            ],
            'publish' => $i['publish'] ? $i['publish'] : 0,
          ]
        );
      }
    }

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
   * \Observers\ProjectObserver observes and deletes child elements (images, documents, grids).
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
