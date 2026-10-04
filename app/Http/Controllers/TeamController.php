<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TeamImage;
use Illuminate\View\View;

class TeamController extends Controller
{
  /**
   * The team page; alumni are grouped by initial and split into two columns.
   */
  public function index(): View
  {
    $members = fn (string $category) => Team::with('documents')->published()->{$category}()->get();

    $alumni = $members('alumni')->groupBy(fn ($member) => substr(strtoupper($member->name), 0, 1));

    return view('frontend.pages.office.team', [
      'pageFooter' => 'office',
      'images' => TeamImage::published()->orderBy('order')->get(),
      'team' => [
        'partner' => $members('partner'),
        'associate' => $members('associate'),
        'seniorStaff' => $members('seniorStaff'),
        'employee' => $members('employee'),
        'alumni' => $alumni->chunk(ceil($alumni->count() / 2)),
      ],
    ]);
  }
}
