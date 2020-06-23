<?php
namespace App\Http\Controllers;
use App\Http\Controllers\BaseController;
use App\Models\Project;
use App\Models\Discourse;
use Illuminate\Http\Request;

class SearchController extends BaseController
{
  protected $viewPath = 'frontend.pages.search.index';
  
  // Models
  protected $project;
  protected $discourse;

  /**
   * Constructor
   * 
   */

  public function __construct(
    Project $project,
    Discourse $discourse)
  {
    parent::__construct();
    $this->project   = $project;
    $this->discourse = $discourse;
  }

  /**
   * Show the homepage
   *
   * @param  \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */

  public function index(Request $request)
  {
    $results = [];

    if ($request->input('keyword'))
    {
      $results['projects']  = $this->project->search($request->input('keyword'))->where('publish', '1')->get();
      $results['discourse'] = $this->discourse->search($request->input('keyword'))->where('publish', '1')->get();
    }
   
    return 
      view($this->viewPath, 
        [
          'pageFooter' => '',
          'results' => $results,
        ]
    );
  }
}
