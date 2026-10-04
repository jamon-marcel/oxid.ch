<?php
namespace App\Http\Controllers;
use App\Http\Controllers\BaseController;
use App\Models\HomeImage;
use App\Services\Search\SearchService;
use Illuminate\Http\Request;

class SearchController extends BaseController
{
  protected $viewPath = 'frontend.pages.search.index';
  
  protected $image;
  protected $search;

  /**
   * Constructor
   * 
   */

  public function __construct(SearchService $search, HomeImage $image)
  {
    parent::__construct();
    $this->search = $search;
    $this->image  = $image;
  }

  /**
   * Show the search page, with results for ?keyword= or /suche/{keyword}
   *
   * @param  \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */

  public function index(Request $request, ?string $keyword = null)
  {
    $results = [];
    $keyword = $request->input('keyword', $keyword);

    if ($keyword)
    {
      $results = $this->search->search($keyword);
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
