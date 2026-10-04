<?php

namespace App\Http\Controllers;

use App\Models\Discourse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class DiscourseController extends Controller
{
  public function index(): View
  {
    return $this->listing(Discourse::with('previewImage')->published(), '');
  }

  public function research(): View
  {
    return $this->listing(Discourse::with('publishedImages')->research(), 'Recherche');
  }

  public function events(): View
  {
    return $this->listing(Discourse::with('publishedImages')->events(), 'Veranstaltungen');
  }

  public function publications(): View
  {
    return $this->listing(Discourse::with('publishedImages')->publications(), 'Publikationen');
  }

  /**
   * A discourse entry. Unpublished ones are visible to admins only.
   */
  public function show(Discourse $discourse, ?string $slug = null): View
  {
    abort_unless($discourse->publish || auth()->check(), 404);

    $discourse->load('publishedImages');

    return view('frontend.pages.discourse.show', [
      'pageFooter' => false,
      'discourse' => $discourse,
      'discourse_og' => $discourse->publishedImages->first(),
    ]);
  }

  private function listing(Builder $query, string $title): View
  {
    return view('frontend.pages.discourse.index', [
      'pageFooter' => 'discourse',
      'pageTitle' => $title,
      'discourse' => $query->orderBy('order')->get(),
    ]);
  }
}
