<?php

namespace Tests\Feature\Admin;

use App\Models\Discourse;
use App\Models\DiscourseDocument;
use App\Models\DiscourseImage;
use App\Models\Job;
use App\Models\JobDocument;
use App\Models\Project;
use App\Models\ProjectDocument;
use App\Models\ProjectImage;
use App\Models\Team;
use App\Models\TeamDocument;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Images and documents that belong to a project, discourse entry, team
 * member or job. They are saved with their parent's form; these are the
 * endpoints the form calls per file.
 */
class AttachmentTest extends AdminTestCase
{
  public static function images(): array
  {
    return [
      'project' => ['project', ProjectImage::class, 'project_id'],
      'discourse' => ['discourse', DiscourseImage::class, 'discourse_id'],
    ];
  }

  public static function documents(): array
  {
    return [
      'project' => ['project', ProjectDocument::class, 'project_id'],
      'discourse' => ['discourse', DiscourseDocument::class, 'discourse_id'],
      'team' => ['team', TeamDocument::class, 'team_id'],
      'job' => ['job', JobDocument::class, 'job_id'],
    ];
  }

  protected function parentId(string $endpoint): int
  {
    return match ($endpoint) {
      'project' => Project::factory()->create()->id,
      'discourse' => Discourse::factory()->create()->id,
      'team' => Team::factory()->create()->id,
      'job' => Job::factory()->create()->id,
    };
  }

  #[DataProvider('images')]
  public function test_image_listing_has_only_published_images_of_the_parent(string $endpoint, string $model, string $key): void
  {
    $id = $this->parentId($endpoint);
    $published = $model::create([$key => $id, 'name' => 'a.jpg', 'publish' => 1]);
    $model::create([$key => $id, 'name' => 'b.jpg', 'publish' => 0]);
    $model::create([$key => $this->parentId($endpoint), 'name' => 'c.jpg', 'publish' => 1]);

    $this->admin()->getJson("/api/{$endpoint}/image/get/{$id}")
      ->assertOk()
      ->assertJsonPath('data.*.id', [$published->id]);
  }

  #[DataProvider('images')]
  public function test_image_status_and_coords(string $endpoint, string $model, string $key): void
  {
    $image = $model::create([$key => $this->parentId($endpoint), 'name' => 'a.jpg', 'publish' => 1]);

    $this->admin()->getJson("/api/{$endpoint}/image/status/{$image->id}")->assertOk()->assertExactJson([0]);
    $this->admin()->getJson('/api/' . $endpoint . '/image/status/999')->assertNotFound();

    $this->admin()->postJson("/api/{$endpoint}/image/coords/{$image->id}", [
      'coords_w' => 640, 'coords_h' => 480, 'coords_x' => 12.5, 'coords_y' => 0,
    ])->assertOk();
    $this->assertSame([640, 480, 12, 0], $image->fresh()->crop());
  }

  #[DataProvider('images')]
  public function test_image_destroy_removes_the_record_and_the_file(string $endpoint, string $model, string $key): void
  {
    $image = $model::create([$key => $this->parentId($endpoint), 'name' => 'a.jpg']);
    Storage::put('public/uploads/a.jpg', 'x');

    $this->admin()->deleteJson("/api/{$endpoint}/image/destroy/a.jpg")->assertOk();

    $this->assertModelMissing($image);
    Storage::assertMissing('public/uploads/a.jpg');
  }

  public function test_discourse_image_order(): void
  {
    $id = Discourse::factory()->create()->id;
    $a = DiscourseImage::create(['discourse_id' => $id, 'name' => 'a.jpg', 'order' => 1]);
    $b = DiscourseImage::create(['discourse_id' => $id, 'name' => 'b.jpg', 'order' => 2]);

    $this->admin()->postJson('/api/discourse/image/order', ['images' => [
      ['id' => $b->id, 'order' => 1],
      ['id' => $a->id, 'order' => 2],
    ]])->assertOk();

    $this->assertEquals([2, 1], [$a->fresh()->order, $b->fresh()->order]);
  }

  #[DataProvider('documents')]
  public function test_document_status_and_destroy(string $endpoint, string $model, string $key): void
  {
    $document = $model::create([$key => $this->parentId($endpoint), 'name' => 'plan.pdf', 'publish' => 1]);
    Storage::put('public/uploads/plan.pdf', 'x');

    $this->admin()->getJson("/api/{$endpoint}/document/status/{$document->id}")->assertOk()->assertExactJson([0]);
    $this->admin()->getJson("/api/{$endpoint}/document/status/{$document->id}")->assertOk()->assertExactJson([1]);

    $this->admin()->deleteJson("/api/{$endpoint}/document/destroy/plan.pdf")->assertOk();

    $this->assertModelMissing($document);
    Storage::assertMissing('public/uploads/plan.pdf');
  }
}
