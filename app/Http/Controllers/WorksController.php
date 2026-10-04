<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorksController extends Controller
{
  /**
   * There is no overview page; the menu links to the authors list.
   */
  public function index(): RedirectResponse
  {
    return redirect()->route('page.works.authors', status: 301);
  }

  /**
   * Grouped by author. Coming from the search (?search=1), every group is expanded.
   */
  public function authors(Request $request): View
  {
    return $this->render('authors', 'author', [
      'search' => $request->input('search'),
    ]);
  }

  public function year(): View
  {
    return $this->render('years', 'year');
  }

  public function program(): View
  {
    return $this->render('program', 'program');
  }

  public function state(): View
  {
    return $this->render('state', 'state');
  }

  /**
   * Published projects grouped by $column, newest work first within a group.
   * Authors and years are listed descending, programs and states ascending.
   */
  private function render(string $view, string $column, array $data = []): View
  {
    $direction = in_array($column, ['author', 'year']) ? 'desc' : 'asc';

    $projects = Project::with('workImage')
      ->published()
      ->orderBy($column, $direction)
      ->orderBy('year_works', 'desc')
      ->get();

    return view("frontend.pages.works.{$view}", [
      'pageFooter' => 'works',
      'projects' => $projects->groupBy($column),
      ...$data,
    ]);
  }
}
