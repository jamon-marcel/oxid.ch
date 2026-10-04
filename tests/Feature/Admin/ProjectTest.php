<?php

namespace Tests\Feature\Admin;

use App\Models\Grid;
use App\Models\GridLayout;
use App\Models\Project;
use App\Models\ProjectDocument;
use App\Models\ProjectImage;
use Illuminate\Support\Facades\Cache;

class ProjectTest extends AdminTestCase
{
  protected function payload(array $overrides = []): array
  {
    return array_replace_recursive([
      'title' => ['de' => 'Holzhaus am See', 'en' => 'Wooden house by the lake'],
      'title_short' => ['de' => 'Holzhaus', 'en' => 'Wooden house'],
      'location' => ['de' => 'Zürich', 'en' => 'Zurich'],
      'description' => ['de' => '<p>Beschreibung</p>', 'en' => '<p>Description</p>'],
      'info' => ['de' => 'Info', 'en' => null],
      'year' => '2024',
      'year_works' => '2022–2024',
      'client_works' => 'Stadt Zürich',
      'principal_works' => 'Oxid',
      'author_works' => 'Oxid',
      'program' => 1,
      'state' => 2,
      'author' => 1,
      'is_filter_wood' => 1,
      'is_filter_reuse' => 0,
      'is_filter_area' => 0,
      'has_detail' => 1,
      'is_highlight' => 0,
      'publish' => 1,
    ], $overrides);
  }

  protected function image(array $overrides = []): array
  {
    return array_merge([
      'id' => null,
      'name' => 'plan.jpg',
      'caption' => ['de' => 'Grundriss', 'en' => 'Floor plan'],
      'is_preview_navigation' => 1,
      'is_preview_works' => 0,
      'is_plan' => 1,
      'coords_w' => 800,
      'coords_h' => 600,
      'coords_x' => 10,
      'coords_y' => 20,
      'orientation' => 'l',
      'publish' => 1,
    ], $overrides);
  }

  protected function project(array $attributes = []): Project
  {
    return Project::create(array_merge($this->payload(), $attributes));
  }

  public function test_store_saves_translations_images_and_documents(): void
  {
    $response = $this->admin()->postJson('/api/project/create', $this->payload([
      'images' => [$this->image()],
      'documents' => [['name' => 'plan.pdf', 'caption' => ['de' => 'Plan'], 'publish' => 1]],
    ]))->assertOk();

    $project = Project::findOrFail($response->json('projectId'));
    $this->assertSame(['de' => 'Holzhaus am See', 'en' => 'Wooden house by the lake'], $project->getTranslations('title'));
    $this->assertSame('2022–2024', $project->year_works);
    $this->assertEquals(1, $project->is_filter_wood);

    $image = $project->images()->sole();
    $this->assertSame('plan.jpg', $image->name);
    $this->assertSame('Floor plan', $image->getTranslation('caption', 'en'));
    $this->assertEquals([800, 600, 10, 20], [$image->coords_w, $image->coords_h, $image->coords_x, $image->coords_y]);

    $document = $project->documents()->sole();
    $this->assertSame(['name' => 'plan.pdf', 'caption' => 'Plan'], ['name' => $document->name, 'caption' => $document->getTranslation('caption', 'de')]);
  }

  public function test_store_validates_the_required_german_fields(): void
  {
    $this->admin()->postJson('/api/project/create', ['title' => ['en' => 'Only English']])
      ->assertUnprocessable()
      ->assertJsonValidationErrors(['title.de', 'title_short.de', 'location.de', 'year']);

    $this->assertSame(0, Project::count());
  }

  public function test_edit_returns_the_project_with_images_and_documents(): void
  {
    $project = $this->project();
    ProjectImage::create(['project_id' => $project->id, 'name' => 'a.jpg']);
    ProjectDocument::create(['project_id' => $project->id, 'name' => 'a.pdf']);

    $this->admin()->getJson("/api/project/edit/{$project->id}")
      ->assertOk()
      ->assertJsonPath('title.de', 'Holzhaus am See')
      ->assertJsonPath('images.0.name', 'a.jpg')
      ->assertJsonPath('documents.0.name', 'a.pdf');
  }

  public function test_update_changes_fields_and_upserts_images(): void
  {
    $project = $this->project();
    $existing = ProjectImage::create(['project_id' => $project->id, 'name' => 'old.jpg', 'publish' => 1]);

    $this->admin()->postJson("/api/project/update/{$project->id}", $this->payload([
      'title' => ['de' => 'Neuer Titel', 'en' => ''],
      'publish' => 0,
      'images' => [
        $this->image(['id' => $existing->id, 'name' => 'old.jpg', 'caption' => ['de' => 'Neu'], 'publish' => 0, 'coords_w' => null]),
        $this->image(['name' => 'new.jpg']),
      ],
    ]))->assertOk();

    $project->refresh();
    $this->assertSame('Neuer Titel', $project->getTranslation('title', 'de'));
    $this->assertEquals(0, $project->publish);
    $this->assertSame(['old.jpg', 'new.jpg'], $project->images()->orderBy('id')->pluck('name')->all());

    $existing->refresh();
    $this->assertSame('Neu', $existing->getTranslation('caption', 'de'));
    $this->assertEquals(0, $existing->publish);
    $this->assertNull($existing->coords_w);
  }

  public function test_update_validates(): void
  {
    $project = $this->project();

    $this->admin()->postJson("/api/project/update/{$project->id}", $this->payload(['title' => ['de' => '']]))
      ->assertUnprocessable()
      ->assertJsonValidationErrors('title.de');

    $this->assertSame('Holzhaus am See', $project->fresh()->getTranslation('title', 'de'));
  }

  public function test_status_toggles_publish(): void
  {
    $project = $this->project(['publish' => 1]);

    $this->admin()->getJson("/api/project/status/{$project->id}")->assertOk()->assertExactJson([0]);
    $this->admin()->getJson("/api/project/status/{$project->id}")->assertOk()->assertExactJson([1]);
  }

  public function test_order_writes_the_given_order(): void
  {
    [$a, $b, $c] = [$this->project(['order' => 1]), $this->project(['order' => 2]), $this->project(['order' => 3])];

    $this->admin()->postJson('/api/project/order', ['projects' => [
      ['id' => $c->id, 'order' => 1],
      ['id' => $a->id, 'order' => 2],
      ['id' => $b->id, 'order' => 3],
    ]])->assertOk();

    $this->admin()->getJson('/api/projects/get')
      ->assertOk()
      ->assertJsonPath('data.*.id', [$c->id, $a->id, $b->id]);
  }

  public function test_destroy_removes_images_documents_and_grids(): void
  {
    $project = $this->project();
    ProjectImage::create(['project_id' => $project->id, 'name' => 'a.jpg']);
    ProjectDocument::create(['project_id' => $project->id, 'name' => 'a.pdf']);
    Grid::create(['project_id' => $project->id, 'layout_id' => GridLayout::create(['key' => '1'])->id]);
    $other = $this->project();

    $this->admin()->deleteJson("/api/project/destroy/{$project->id}")->assertOk();

    $this->assertModelMissing($project);
    $this->assertSame(0, ProjectImage::count());
    $this->assertSame(0, ProjectDocument::count());
    $this->assertSame(0, Grid::count());
    $this->assertModelExists($other);
  }

  public function test_saving_and_deleting_flush_the_search_index(): void
  {
    Cache::put('search.index', 'stale');
    $this->admin()->postJson('/api/project/create', $this->payload())->assertOk();
    $this->assertFalse(Cache::has('search.index'));

    Cache::put('search.index', 'stale');
    $this->admin()->deleteJson('/api/project/destroy/' . Project::value('id'))->assertOk();
    $this->assertFalse(Cache::has('search.index'));
  }
}
