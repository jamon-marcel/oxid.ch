<?php

namespace App\Http\Controllers;

use App\Models\HomeImage;
use App\Services\Search\SearchService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
  /**
   * The search page, with results for ?keyword= or /suche/{keyword}.
   */
  public function index(Request $request, SearchService $search, ?string $keyword = null): View
  {
    $keyword = $request->input('keyword', $keyword);

    return view('frontend.pages.search.index', [
      'pageFooter' => '',
      'image' => HomeImage::published()->inRandomOrder()->first(),
      'results' => $keyword ? $search->search($keyword) : [],
      'keyword' => $keyword,
    ]);
  }
}
