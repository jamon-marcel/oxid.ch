<?php
namespace App\Http\Controllers;
use App\Http\Controllers\BaseController;
use App\Models\HomeImage;
use App\Models\News;
use Illuminate\Http\Request;

class HomeController extends BaseController
{
  protected $viewPath = 'frontend.pages.home.index';
  
  // Models
  protected $image;
  protected $news;

  /**
   * Constructor
   * 
   */

  public function __construct(
    HomeImage $image,
    News $news)
  {
    parent::__construct();
    $this->image = $image;
    $this->news  = $news;
  }

  /**
   * Show the homepage
   *
   * @return \Illuminate\Http\Response
   */

  public function index()
  {
    $images = $this->image->published()->get();
    $news   = $this->news->published()->orderBy('sticky', 'DESC')->get();
    
    $image = null;
    if (count($images) > 0)
    {
      $random = count($images) > 1 ? mt_rand(0, count($images)-1) : 0;
      $image  = $images[$random];
    }

    return 
      view($this->viewPath, 
        [
          'image'     => $image,
          'news'      => $news
        ]
    );
  }
}
