<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\View\View;

class ProjectController extends Controller
{
  /**
   * The project listing opens on the first published project.
   */
  public function index(): View
  {
    return $this->render(Project::published()->orderBy('order')->firstOrFail());
  }

  /**
   * A project. Unpublished ones are visible to admins only (the grid builder's preview).
   */
  public function show(Project $project, ?string $slug = null): View
  {
    abort_unless($project->publish || auth()->check(), 404);

    return $this->render($project);
  }

  private function render(Project $project): View
  {
    $project->load([
      'publishedDocuments',
      'grids' => fn ($query) => $query->orderBy('order'),
      'grids.layout',
      'grids.elements.image',
    ]);

    return view('frontend.pages.project.show', [
      'pageFooter' => 'projects',
      'project' => $project,
      'project_grid' => $project->grids,
      'project_og' => $project->grids->first()?->elements->first()?->image,
      'project_teasers' => Project::published()->with('teaserImage')->get(),
      'navBrowse' => $this->neighbours($project),
    ]);
  }

  /**
   * The previous and next project with a detail page, wrapping around.
   */
  private function neighbours(Project $project): array
  {
    $projects = Project::hasDetail()->orderBy('order')->get()->values();
    $count = $projects->count();
    $key = (int) $projects->search(fn ($p) => $p->is($project));

    return [
      'prev' => $projects->get(($key - 1 + $count) % max($count, 1)),
      'next' => $projects->get(($key + 1) % max($count, 1)),
    ];
  }
}
