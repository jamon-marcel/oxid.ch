<?php
namespace App\Http\Controllers;
use App\Http\Controllers\BaseController;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\ProjectDocument;
use Illuminate\Http\Request;

class ProjectController extends BaseController
{
  protected $pageFooter = 'projects';
  protected $viewPath   = 'frontend.pages.project.';

  // Models
  protected $project;

  /**
   * Constructor
   * 
   */

  public function __construct(Project $project)
  {
    parent::__construct();
    $this->project = $project;
  }

  /**
   * Show the project listing
   *
   * @return \Illuminate\Http\Response
   */

  public function index()
  {
    
    //$search = $this->project->search('Frauenfeld')->get();
    //dd($search);
    
    
    // get first project
    $project      = $this->project->with('publishedDocuments')->with('grids.layout')->with('grids.elements.image')->first();
    $project_grid = $project->with('grids.layout')->with('grids.elements.image')->findOrFail($project->id);

    // get teaser images
    $projectTeasers = $this->project->published()->with('teaserImage')->get();

    return 
      view($this->viewPath . 'show',
      [
        'pageFooter'      => $this->pageFooter,
        'project'         => $project,
        'project_grid'    => $project_grid->grids->sortBy('order'),
        'project_teasers' => $projectTeasers,
        'navBrowse'       => $this->getBrowse($project->id),
      ]
    );
  }

  /**
   * Show a project
   *
   * @param String $slug
   * @param Project $project
   * @return \Illuminate\Http\Response
   */

  public function show(Project $project, $slug = NULL)
  {
    $project      = $project->with('publishedDocuments')->findOrFail($project->id);
    $project_grid = $project->with('grids.layout')->with('grids.elements.image')->findOrFail($project->id);
    
    // get teaser images
    $projectTeasers = $this->project->published()->with('teaserImage')->get();

    return 
      view($this->viewPath . 'show',
      [
        'pageFooter'   => $this->pageFooter,
        'project'      => $project,
        'project_grid' => $project_grid->grids->sortBy('order'),
        'project_teasers' => $projectTeasers,
        'navBrowse'    => $this->getBrowse($project->id),
      ]
    );
  }

  protected function getBrowse($id = NULL)
  {
    // Build project nav
    $projects = $this->project->hasDetail()->get();
    $keys     = [];
    $items    = [];

    foreach($projects as $p)
    {
      $keys[] = (int) $p->id;
    }

    // Get current key
    $key = array_search($id, $keys);

    if ($key == 0)
    {
      $prevId = end($keys);
      $nextId = isset($keys[$key+1]) ? $keys[$key+1] : NULL;
    }
    else if ($key == count($keys) - 1)
    {
      $prevId = $keys[$key-1];
      $nextId = $keys[0];
    }
    else
    {
      $prevId = $keys[$key-1];
      $nextId = $keys[$key+1];
    }

    $items = [
      'prev' => $this->project->find($prevId),
      'next' => $this->project->find($nextId),
    ];

    return $items;
  }
}
