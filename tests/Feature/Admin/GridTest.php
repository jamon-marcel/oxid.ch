<?php

namespace Tests\Feature\Admin;

use App\Models\Grid;
use App\Models\GridElement;
use App\Models\GridLayout;
use App\Models\Project;
use App\Models\ProjectImage;

/**
 * The project grid builder: rows (grids) in a layout, filled with the
 * project's images. An image placed in a grid is flagged is_grid so the
 * project form protects it from deletion.
 */
class GridTest extends AdminTestCase
{
  protected Project $project;

  protected GridLayout $layout;

  protected function setUp(): void
  {
    parent::setUp();

    $this->project = Project::factory()->create();
    $this->layout = GridLayout::create(['key' => '1-2']);
  }

  protected function grid(array $attributes = []): Grid
  {
    return Grid::create(array_merge(['project_id' => $this->project->id, 'layout_id' => $this->layout->id], $attributes));
  }

  protected function image(): ProjectImage
  {
    return ProjectImage::create(['project_id' => $this->project->id, 'name' => uniqid() . '.jpg']);
  }

  protected function place(Grid $grid, ProjectImage $image, int $position = 1)
  {
    return $this->admin()->postJson('/api/project/grid/image/store', [
      'position' => $position,
      'grid_id' => $grid->id,
      'project_image_id' => $image->id,
      'project_id' => $this->project->id,
    ]);
  }

  public function test_layouts(): void
  {
    $this->admin()->getJson('/api/project/grid/layouts')->assertOk()->assertJsonPath('data.0.key', '1-2');
  }

  public function test_store_appends_a_row_and_returns_the_projects_grids(): void
  {
    $this->grid(['order' => 1]);
    $other = Project::factory()->create();
    Grid::create(['project_id' => $other->id, 'layout_id' => $this->layout->id]);

    $this->admin()->getJson("/api/project/grid/store/{$this->project->id}/{$this->layout->id}")
      ->assertOk()
      ->assertJsonCount(2, 'data')
      ->assertJsonPath('data.1.order', 99)
      ->assertJsonPath('data.1.layout.key', '1-2');
  }

  public function test_listing_includes_elements_with_their_images(): void
  {
    $grid = $this->grid();
    $image = $this->image();
    $this->place($grid, $image, 2)->assertOk();

    $this->admin()->getJson("/api/project/grids/{$this->project->id}")
      ->assertOk()
      ->assertJsonPath('data.0.elements.0.position', 2)
      ->assertJsonPath('data.0.elements.0.image.name', $image->name);

    $this->admin()->getJson("/api/project/grid/images/{$grid->id}")
      ->assertOk()
      ->assertJsonPath('data.0.image.project.id', $this->project->id);
  }

  public function test_placing_an_image_flags_it_as_used(): void
  {
    $image = $this->image();

    $this->place($this->grid(), $image)->assertOk();

    $this->assertEquals(1, $image->fresh()->is_grid);
    $this->assertSame(1, GridElement::count());
  }

  public function test_removing_an_element_unflags_the_image_unless_it_is_used_elsewhere(): void
  {
    $image = $this->image();
    $this->place($this->grid(), $image, 1);
    $this->place($this->grid(), $image, 2);
    [$first, $second] = GridElement::orderBy('id')->get();

    $this->admin()->deleteJson("/api/project/grid/image/delete/{$first->id}")->assertOk();
    $this->assertEquals(1, $image->fresh()->is_grid, 'still in the second grid');

    $this->admin()->deleteJson("/api/project/grid/image/delete/{$second->id}")->assertOk();
    $this->assertEquals(0, $image->fresh()->is_grid);

    $this->admin()->deleteJson("/api/project/grid/image/delete/{$second->id}")->assertNotFound();
  }

  public function test_deleting_a_row_removes_its_elements_and_unflags_their_images(): void
  {
    $grid = $this->grid();
    $keep = $this->grid();
    [$a, $b] = [$this->image(), $this->image()];
    $this->place($grid, $a, 1);
    $this->place($grid, $b, 2);
    $this->place($keep, $this->image());

    $this->admin()->deleteJson("/api/project/grid/delete/{$grid->id}")->assertOk();

    $this->assertModelMissing($grid);
    $this->assertSame([$keep->id], GridElement::pluck('grid_id')->all());
    $this->assertEquals([0, 0], [$a->fresh()->is_grid, $b->fresh()->is_grid]);
  }

  public function test_order(): void
  {
    $a = $this->grid(['order' => 1]);
    $b = $this->grid(['order' => 2]);

    $this->admin()->postJson('/api/project/grids/order', ['grids' => [
      ['id' => $b->id, 'order' => 1],
      ['id' => $a->id, 'order' => 2],
    ]])->assertOk();

    $this->admin()->getJson("/api/project/grids/{$this->project->id}")
      ->assertOk()
      ->assertJsonPath('data.*.id', [$b->id, $a->id]);
  }
}
