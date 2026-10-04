<?php

namespace Tests\Feature\Admin;

use App\Models\Discourse;
use App\Models\DiscourseDocument;
use App\Models\DiscourseImage;
use Illuminate\Support\Facades\Cache;

class DiscourseTest extends AdminTestCase
{
  protected function payload(array $overrides = []): array
  {
    return array_replace_recursive([
      'heading' => ['de' => 'Vortrag', 'en' => 'Lecture'],
      'date' => ['de' => '13. Juni 2024', 'en' => 'June 13, 2024'],
      'title' => ['de' => 'Stadt aus Holz', 'en' => 'City of wood'],
      'description_short' => ['de' => 'Kurz', 'en' => 'Short'],
      'description' => ['de' => '<p>Lang</p>', 'en' => '<p>Long</p>'],
      'info' => ['de' => null, 'en' => null],
      'category' => 2,
      'publish' => 1,
    ], $overrides);
  }

  protected function discourse(array $attributes = []): Discourse
  {
    return Discourse::create(array_merge($this->payload(), $attributes));
  }

  public function test_store_saves_translations_images_and_documents(): void
  {
    $response = $this->admin()->postJson('/api/discourse/create', $this->payload([
      'images' => [['name' => 'talk.jpg', 'caption' => ['de' => 'Bild', 'en' => 'Image'], 'is_preview' => 1, 'publish' => 1, 'theme' => 0]],
      'documents' => [['name' => 'talk.pdf', 'caption' => ['de' => 'Folien', 'en' => 'Slides'], 'publish' => 1]],
    ]))->assertOk();

    $discourse = Discourse::findOrFail($response->json('discourseId'));
    $this->assertSame(['de' => 'Stadt aus Holz', 'en' => 'City of wood'], $discourse->getTranslations('title'));
    $this->assertEquals(2, $discourse->category);
    $this->assertSame('Image', $discourse->images()->sole()->getTranslation('caption', 'en'));
    $this->assertSame('talk.pdf', $discourse->documents()->sole()->name);
  }

  public function test_store_validates(): void
  {
    $this->admin()->postJson('/api/discourse/create', ['title' => ['de' => 'Nur Titel']])
      ->assertUnprocessable()
      ->assertJsonValidationErrors(['heading.de', 'date.de', 'description_short.de']);
  }

  public function test_update_changes_fields_and_upserts_documents(): void
  {
    $discourse = $this->discourse();
    $document = DiscourseDocument::create(['discourse_id' => $discourse->id, 'name' => 'old.pdf', 'publish' => 1]);

    $this->admin()->postJson("/api/discourse/update/{$discourse->id}", $this->payload([
      'heading' => ['de' => 'Ausstellung'],
      'category' => 3,
      'documents' => [
        ['id' => $document->id, 'name' => 'old.pdf', 'caption' => ['de' => 'Alt', 'en' => 'Old'], 'publish' => 0],
        ['id' => null, 'name' => 'new.pdf', 'caption' => ['de' => 'Neu', 'en' => 'New'], 'publish' => 1],
      ],
    ]))->assertOk();

    $discourse->refresh();
    $this->assertSame('Ausstellung', $discourse->getTranslation('heading', 'de'));
    $this->assertEquals(3, $discourse->category);
    $this->assertEquals(0, $document->fresh()->publish);
    $this->assertSame(['old.pdf', 'new.pdf'], $discourse->documents()->orderBy('id')->pluck('name')->all());
  }

  public function test_edit_returns_images_in_their_order(): void
  {
    $discourse = $this->discourse();
    DiscourseImage::create(['discourse_id' => $discourse->id, 'name' => 'b.jpg', 'order' => 2]);
    DiscourseImage::create(['discourse_id' => $discourse->id, 'name' => 'a.jpg', 'order' => 1]);

    $this->admin()->getJson("/api/discourse/edit/{$discourse->id}")
      ->assertOk()
      ->assertJsonPath('images.*.name', ['a.jpg', 'b.jpg']);
  }

  public function test_status_order_and_listing(): void
  {
    $a = $this->discourse(['order' => 1, 'publish' => 1]);
    $b = $this->discourse(['order' => 2]);

    $this->admin()->getJson("/api/discourse/status/{$a->id}")->assertOk()->assertExactJson([0]);
    $this->admin()->postJson('/api/discourse/order', ['discourses' => [
      ['id' => $b->id, 'order' => 1],
      ['id' => $a->id, 'order' => 2],
    ]])->assertOk();

    $this->admin()->getJson('/api/discourses/get')->assertOk()->assertJsonPath('*.id', [$b->id, $a->id]);
  }

  public function test_destroy_removes_images_and_documents(): void
  {
    $discourse = $this->discourse();
    DiscourseImage::create(['discourse_id' => $discourse->id, 'name' => 'a.jpg']);
    DiscourseDocument::create(['discourse_id' => $discourse->id, 'name' => 'a.pdf']);

    Cache::put('search.index', 'stale');
    $this->admin()->deleteJson("/api/discourse/destroy/{$discourse->id}")->assertOk();

    $this->assertModelMissing($discourse);
    $this->assertSame(0, DiscourseImage::count() + DiscourseDocument::count());
    $this->assertFalse(Cache::has('search.index'));
  }
}
