<?php

namespace Tests\Feature\Admin;

use App\Models\HomeImage;
use App\Models\JobImage;
use App\Models\ProfileImage;
use App\Models\TeamImage;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The standalone image libraries (home, team, jobs, profile), all managed
 * by the admin's images/Index.vue through the same endpoint shape.
 */
class ImageLibraryTest extends AdminTestCase
{
  public static function libraries(): array
  {
    return [
      'home' => ['home', 'home/images/get', HomeImage::class],
      'team' => ['team', 'team/images/get', TeamImage::class],
      'job' => ['job', 'job/images/get', JobImage::class],
      'profile' => ['profile', 'profile/images/get', ProfileImage::class],
    ];
  }

  public static function orderedLibraries(): array
  {
    return array_diff_key(self::libraries(), ['home' => true]);
  }

  #[DataProvider('libraries')]
  public function test_create_edit_update_and_status(string $endpoint, string $list, string $model): void
  {
    $id = $this->admin()->postJson("/api/{$endpoint}/image/create", [
      'name' => 'bild.jpg',
      'caption' => ['de' => 'Bild', 'en' => 'Image'],
      'publish' => 1,
    ])->assertOk()->json('imageId');

    $this->admin()->getJson("/api/{$endpoint}/image/edit/{$id}")
      ->assertOk()
      ->assertJsonPath('name', 'bild.jpg')
      ->assertJsonPath('caption.en', 'Image');

    $this->admin()->postJson("/api/{$endpoint}/image/update/{$id}", ['name' => 'bild.jpg', 'caption' => ['de' => 'Neu', 'en' => '']])->assertOk();
    $this->assertSame('Neu', $model::findOrFail($id)->getTranslation('caption', 'de'));

    $this->admin()->getJson("/api/{$endpoint}/image/status/{$id}")->assertOk()->assertExactJson([0]);
    $this->admin()->getJson("/api/{$list}")->assertOk()->assertJsonPath('data.0.publish', 0);
  }

  #[DataProvider('libraries')]
  public function test_create_needs_a_name(string $endpoint, string $list, string $model): void
  {
    $this->admin()->postJson("/api/{$endpoint}/image/create", ['caption' => ['de' => 'Bild']])
      ->assertUnprocessable()
      ->assertJsonValidationErrors('name');

    $this->assertSame(0, $model::count());
  }

  #[DataProvider('libraries')]
  public function test_coords_store_the_crop(string $endpoint, string $list, string $model): void
  {
    $image = $model::create(['name' => 'bild.jpg']);

    $this->admin()->postJson("/api/{$endpoint}/image/coords/{$image->id}", [
      'coords_w' => 1200.5, 'coords_h' => 800, 'coords_x' => 0, 'coords_y' => 33.25,
    ])->assertOk();

    $image->refresh();
    $this->assertEquals([1200.5, 800, 0, 33.25], [$image->coords_w, $image->coords_h, $image->coords_x, $image->coords_y]);
    $this->assertSame([1200, 800, 0, 33], $image->crop());
  }

  #[DataProvider('orderedLibraries')]
  public function test_order(string $endpoint, string $list, string $model): void
  {
    $a = $model::create(['name' => 'a.jpg', 'order' => 1]);
    $b = $model::create(['name' => 'b.jpg', 'order' => 2]);

    $this->admin()->postJson("/api/{$endpoint}/image/order", ['images' => [
      ['id' => $b->id, 'order' => 1],
      ['id' => $a->id, 'order' => 2],
    ]])->assertOk();

    $this->admin()->getJson("/api/{$list}")->assertOk()->assertJsonPath('data.*.id', [$b->id, $a->id]);
  }

  #[DataProvider('libraries')]
  public function test_destroy_removes_the_record_and_the_file(string $endpoint, string $list, string $model): void
  {
    $image = $model::create(['name' => 'bild.jpg']);
    $keep = $model::create(['name' => 'other.jpg']);
    Storage::put('public/uploads/bild.jpg', 'x');
    Storage::put('public/uploads/other.jpg', 'x');

    $this->admin()->deleteJson("/api/{$endpoint}/image/destroy/bild.jpg")->assertOk();

    $this->assertModelMissing($image);
    $this->assertModelExists($keep);
    Storage::assertMissing('public/uploads/bild.jpg');
    Storage::assertExists('public/uploads/other.jpg');
  }

  /**
   * An upload removed before it was saved, or a record another tab deleted
   */
  #[DataProvider('libraries')]
  public function test_destroy_without_a_record_still_removes_the_file(string $endpoint, string $list, string $model): void
  {
    Storage::put('public/uploads/unsaved.jpg', 'x');

    $this->admin()->deleteJson("/api/{$endpoint}/image/destroy/unsaved.jpg")->assertOk();

    Storage::assertMissing('public/uploads/unsaved.jpg');
  }
}
