<?php
namespace App\Http\Controllers;
use App\Http\Controllers\BaseController;
use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Http\Request;

class WorksController extends BaseController
{
  protected $pageFooter = 'works';
  protected $viewPath   = 'frontend.pages.works.';

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
   * Show all entries
   *
   * @return \Illuminate\Http\Response
   */

  public function index()
  {
  }

  /**
   * Show entries by authors
   *
   * @param Boolean search
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */

  public function authors(Request $request, $search = false)
  {
    $projects = $this->project->with('workImage')->published()->orderBy('author', 'DESC')->orderBy('year_works', 'DESC')->get();
    return 
      view($this->viewPath . 'authors', 
        [
          'pageFooter' => $this->pageFooter,
          'projects'   => $projects->groupBy('author'),
          'search'   => $request->input('search')
        ]
    );
  }

  /**
   * Show entries by year
   *
   * @return \Illuminate\Http\Response
   */

  public function year()
  {
    $projects = $this->project->with('workImage')->published()->orderBy('year', 'DESC')->orderBy('year_works', 'DESC')->get();
    $projects = $projects->groupBy('year');
    $data = [];
    
    // Filter out projects without preview image
    foreach($projects as $key => $project)
    {
      foreach($project as $p)
      {
        if ($p->workImage)
        {
          $data[$key][] = $p;
        }
      }
    }

    return 
      view($this->viewPath . 'years', 
        [
          'pageFooter' => $this->pageFooter,
          'projects'   => $projects,
        ]
    );
  }

  /**
   * Show entries by program
   *
   * @return \Illuminate\Http\Response
   */

  public function program()
  {
    $projects = $this->project->with('workImage')->published()->orderBy('program')->orderBy('year_works', 'DESC')->get();
    return 
      view($this->viewPath . 'program', 
        [
          'pageFooter' => $this->pageFooter,
          'projects'   => $projects->groupBy('program')
        ]
    );
  }

  /**
   * Show entries by state
   *
   * @return \Illuminate\Http\Response
   */

  public function state()
  {
    $projects = $this->project->with('workImage')->published()->orderBy('state')->orderBy('year_works', 'DESC')->get();
    return 
      view($this->viewPath . 'state', 
        [
          'pageFooter' => $this->pageFooter,
          'projects'   => $projects->groupBy('state')
        ]
    ); 
  }
}
