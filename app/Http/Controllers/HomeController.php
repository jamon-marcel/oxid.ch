<?php
namespace App\Http\Controllers;
use App\Models\HomeImage;
use App\Models\News;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
  protected $image;
  protected $news;

  /**
   * Constructor
   * 
   */

  public function __construct(HomeImage $image, News $news)
  {
    $this->image = $image;
    $this->news = $news;
  }

  /**
   * Show the homepage
   *
   * @return \Illuminate\Http\Response
   */

  public function index()
  {
    $images = $this->image->where('publish', 1)->get();
    $news   = $this->news->where('publish', 1)->orderBy('sticky', 'DESC')->get();
    return 
      view('frontend.pages.home.index', 
        [
          'image' => $images[mt_rand(1, count($images)-1)],
          'news' => $news,
        ]
    );
  }
}
