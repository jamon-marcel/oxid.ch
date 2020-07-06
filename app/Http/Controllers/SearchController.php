<?php
namespace App\Http\Controllers;
use App\Http\Controllers\BaseController;
use App\Models\Project;
use App\Models\Discourse;
use App\Models\HomeImage;
use Illuminate\Http\Request;

class SearchController extends BaseController
{
  protected $viewPath = 'frontend.pages.search.index';
  
  // Models
  protected $image;
  protected $project;
  protected $discourse;

  /**
   * Constructor
   * 
   */

  public function __construct(
    Project $project,
    Discourse $discourse,
    HomeImage $image)
  {
    parent::__construct();
    $this->project   = $project;
    $this->discourse = $discourse;
    $this->image     = $image;
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
    $keyword = null;

    if ($request->input('keyword'))
    {
      $keyword = $request->input('keyword');
      $results['projects']  = $this->project->search($request->input('keyword'))->where('publish', '1')->get();
      $results['discourse'] = $this->discourse->search($request->input('keyword'))->where('publish', '1')->get();
    }
   
    $images = $this->image->published()->get();
    $image = null;
    if (count($images) > 0)
    {
      $random = count($images) > 1 ? mt_rand(0, count($images)-1) : 0;
      $image  = $images[$random];
    }

    return 
      view($this->viewPath, 
        [
          'pageFooter' => '',
          'image'      => $image,
          'results'    => $results,
          'keyword'    => $keyword
        ]
    );
  }
}
