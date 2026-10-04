<?php

namespace App\Http\Controllers;

use App\Models\HomeImage;
use App\Models\News;
use Illuminate\View\View;

class HomeController extends Controller
{
  /**
   * The homepage: a random published image and the news.
   */
  public function index(): View
  {
    return view('frontend.pages.home.index', [
      'image' => HomeImage::published()->inRandomOrder()->first(),
      'news' => News::published()->orderBy('order')->get(),
    ]);
  }
}
