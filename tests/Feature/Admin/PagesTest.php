<?php

namespace Tests\Feature\Admin;

use App\Models\Contact;
use App\Models\News;
use App\Models\Profile;

/**
 * The smaller resources: news, the office profile, the contact page and
 * the settings lists the forms' selects are built from.
 */
class PagesTest extends AdminTestCase
{
  public function test_news_crud(): void
  {
    $id = $this->admin()->postJson('/api/news/create', [
      'title' => ['de' => 'Wettbewerb gewonnen', 'en' => 'Competition won'],
      'subtitle' => ['de' => 'Schulhaus', 'en' => 'School'],
      'text' => ['de' => '<p>Text</p>', 'en' => ''],
      'date_end' => '2026-12-31',
      'publish' => 1,
    ])->assertOk()->json('newsId');

    $this->admin()->getJson("/api/news/edit/{$id}")
      ->assertOk()
      ->assertJsonPath('title.en', 'Competition won')
      ->assertJsonPath('date_end', '2026.12.31');

    $this->admin()->postJson("/api/news/update/{$id}", ['title' => ['de' => 'Neu', 'en' => ''], 'date_end' => null])->assertOk();
    $news = News::findOrFail($id);
    $this->assertSame('Neu', $news->getTranslation('title', 'de'));
    $this->assertNull($news->date_end);

    $this->admin()->getJson("/api/news/status/{$id}")->assertOk()->assertExactJson([0]);

    $this->admin()->deleteJson("/api/news/destroy/{$id}")->assertOk();
    $this->assertModelMissing($news);
  }

  public function test_news_validates(): void
  {
    $this->admin()->postJson('/api/news/create', ['title' => ['en' => 'English only']])
      ->assertUnprocessable()
      ->assertJsonValidationErrors('title.de');
  }

  public function test_news_order(): void
  {
    $a = News::create(['title' => ['de' => 'A'], 'order' => 1]);
    $b = News::create(['title' => ['de' => 'B'], 'order' => 2]);

    $this->admin()->postJson('/api/news/order', ['news' => [
      ['id' => $b->id, 'order' => 1],
      ['id' => $a->id, 'order' => 2],
    ]])->assertOk();

    $this->admin()->getJson('/api/news/get')->assertOk()->assertJsonPath('data.*.id', [$b->id, $a->id]);
  }

  public function test_profile_crud(): void
  {
    $id = $this->admin()->postJson('/api/profile/create', [
      'title' => ['de' => 'Profil', 'en' => 'Profile'],
      'description' => ['de' => '<p>Über uns</p>', 'en' => '<p>About us</p>'],
      'publish' => 1,
    ])->assertOk()->json('profileId');

    $this->admin()->getJson('/api/profile/get')->assertOk()->assertJsonPath('0.title.de', 'Profil');

    $this->admin()->postJson("/api/profile/update/{$id}", [
      'title' => ['de' => 'Büro', 'en' => 'Office'],
      'description' => ['de' => 'Neu', 'en' => 'New'],
      'publish' => 1,
    ])->assertOk();
    $this->assertSame(['de' => 'Büro', 'en' => 'Office'], Profile::findOrFail($id)->getTranslations('title'));

    $this->admin()->postJson("/api/profile/update/{$id}", ['title' => ['de' => '']])
      ->assertUnprocessable()
      ->assertJsonValidationErrors('title.de');

    $this->admin()->getJson("/api/profile/status/{$id}")->assertOk()->assertExactJson([0]);
  }

  public function test_contact_crud(): void
  {
    $id = $this->admin()->postJson('/api/contact/create', [
      'address' => ['de' => 'Musterstrasse 1', 'en' => 'Musterstrasse 1'],
      'google_maps_url' => 'https://maps.example.com/oxid',
      'contacts' => ['de' => 'info@example.com', 'en' => ''],
      'info' => ['de' => 'Info', 'en' => ''],
      'imprint' => ['de' => 'Impressum', 'en' => 'Imprint'],
    ])->assertOk()->json('contactId');

    $this->admin()->getJson("/api/contact/edit/{$id}")
      ->assertOk()
      ->assertJsonPath('google_maps_url', 'https://maps.example.com/oxid')
      ->assertJsonPath('imprint.en', 'Imprint');

    $this->admin()->postJson("/api/contact/update/{$id}", [
      'address' => ['de' => 'Neue Strasse 2', 'en' => ''],
      'google_maps_url' => null,
      'contacts' => ['de' => '', 'en' => ''],
      'info' => ['de' => '', 'en' => ''],
      'imprint' => ['de' => 'Impressum', 'en' => ''],
    ])->assertOk();

    $contact = Contact::findOrFail($id);
    $this->assertSame('Neue Strasse 2', $contact->getTranslation('address', 'de'));
    $this->assertNull($contact->google_maps_url);
    $this->admin()->getJson('/api/contact/get')->assertOk()->assertJsonCount(1);
  }

  public function test_settings_lists_are_german_labels_keyed_by_id(): void
  {
    $this->admin()->getJson('/api/settings/authors')->assertOk()->assertExactJson(config('settings.authors'));

    foreach (['program', 'state', 'discourseCategories', 'teamCategories'] as $type) {
      $list = $this->admin()->getJson("/api/settings/{$type}")->assertOk()->json();

      $this->assertSame(array_keys(config("settings.{$type}")), array_map('intval', array_keys($list)), $type);
      foreach ($list as $label) {
        $this->assertStringNotContainsString('settings.', $label, "{$type}: untranslated label");
      }
    }
  }
}
