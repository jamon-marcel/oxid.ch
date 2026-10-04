<?php

namespace Tests\Feature;

use App\Models\Discourse;
use App\Models\Project;
use App\Models\User;
use Tests\TestCase;

/**
 * The public pages. Unpublished projects and discourse entries are hidden
 * from visitors but stay reachable for a logged-in admin (the grid
 * builder's "Vorschau"). Reads existing rows from the local database;
 * writes nothing.
 */
class PublicPagesTest extends TestCase
{
  public function test_every_page_renders(): void
  {
    $pages = [
      '/', '/projekte', '/werkliste/autorenschaft', '/werkliste/autorenschaft?search=1',
      '/werkliste/jahr', '/werkliste/programm', '/werkliste/status',
      '/diskurs', '/diskurs/recherche', '/diskurs/veranstaltungen', '/diskurs/publikationen',
      '/buero/team', '/buero/profil', '/buero/jobs', '/kontakt', '/geschichte',
      '/suche', '/suche/haus', '/suche?keyword=holz',
    ];

    foreach ($pages as $page) {
      $this->get($page)->assertOk();
    }
  }

  public function test_works_overview_redirects_to_the_authors_list(): void
  {
    $this->get('/werkliste')->assertRedirect('/werkliste/autorenschaft')->assertStatus(301);
  }

  public function test_project_browse_wraps_around(): void
  {
    $projects = Project::hasDetail()->orderBy('order')->get();

    $this->get('/projekt/' . $projects->first()->id)
      ->assertViewHas('navBrowse', fn ($nav) => $nav['prev']->is($projects->last()) && $nav['next']->is($projects->get(1)));

    $this->get('/projekt/' . $projects->last()->id)
      ->assertViewHas('navBrowse', fn ($nav) => $nav['prev']->is($projects->get($projects->count() - 2)) && $nav['next']->is($projects->first()));
  }

  public function test_unpublished_project_is_404_for_visitors_and_visible_to_admins(): void
  {
    $id = Project::where('publish', 0)->value('id') ?? $this->markTestSkipped('No unpublished project');

    $this->get("/projekt/{$id}")->assertNotFound();
    $this->actingAs(User::firstOrFail())->get("/projekt/{$id}")->assertOk();
  }

  public function test_unpublished_discourse_is_404_for_visitors_and_visible_to_admins(): void
  {
    $id = Discourse::where('publish', 0)->value('id') ?? $this->markTestSkipped('No unpublished discourse entry');

    $this->get("/diskurs/{$id}")->assertNotFound();
    $this->actingAs(User::firstOrFail())->get("/diskurs/{$id}")->assertOk();
  }

  public function test_published_pages_stay_public(): void
  {
    $this->get('/projekt/' . Project::published()->value('id'))->assertOk();
    $this->get('/diskurs/' . Discourse::published()->value('id'))->assertOk();
    $this->get('/projekte')->assertOk();
  }

  public function test_projects_index_shows_the_first_published_project(): void
  {
    $first = Project::published()->orderBy('order')->firstOrFail();

    $this->get('/projekte')->assertViewHas('project', fn ($project) => $project->is($first));
  }
}
