<?php

namespace Tests\Feature\Admin;

use App\Models\Team;
use App\Models\TeamDocument;

class TeamTest extends AdminTestCase
{
  protected function payload(array $overrides = []): array
  {
    return array_replace_recursive([
      'firstname' => 'Anna',
      'name' => 'Muster',
      'email' => 'anna@example.com',
      'phone' => '+41 44 000 00 00',
      'role' => ['de' => 'Architektin', 'en' => 'Architect'],
      'position' => ['de' => 'Partnerin', 'en' => 'Partner'],
      'category' => 1,
      'publish' => 1,
    ], $overrides);
  }

  protected function member(array $attributes = []): Team
  {
    return Team::create(array_merge($this->payload(), $attributes));
  }

  public function test_store_saves_the_member_and_documents(): void
  {
    $response = $this->admin()->postJson('/api/team/create', $this->payload([
      'documents' => [['name' => 'cv.pdf', 'language' => 'en', 'caption' => ['de' => 'CV'], 'publish' => 1]],
    ]))->assertOk();

    $member = Team::findOrFail($response->json('teamId'));
    $this->assertSame(['Anna', 'Muster', 'anna@example.com'], [$member->firstname, $member->name, $member->email]);
    $this->assertSame('Architect', $member->getTranslation('role', 'en'));
    $this->assertSame(['cv.pdf', 'en'], [$member->documents()->sole()->name, $member->documents()->sole()->language]);
  }

  public function test_store_validates(): void
  {
    $this->admin()->postJson('/api/team/create', ['email' => 'x@example.com'])
      ->assertUnprocessable()
      ->assertJsonValidationErrors(['firstname', 'name']);
  }

  public function test_update(): void
  {
    $member = $this->member();

    $this->admin()->postJson("/api/team/update/{$member->id}", $this->payload([
      'name' => 'Beispiel',
      'position' => ['de' => 'Alumna'],
      'category' => 3,
    ]))->assertOk();

    $member->refresh();
    $this->assertSame('Beispiel', $member->name);
    $this->assertSame('Alumna', $member->getTranslation('position', 'de'));
    $this->assertEquals(3, $member->category);
  }

  public function test_listing_is_grouped_by_category_and_ordered(): void
  {
    $b = $this->member(['category' => 1, 'order' => 2]);
    $a = $this->member(['category' => 1, 'order' => 1]);
    $c = $this->member(['category' => 2, 'order' => 1]);

    $this->admin()->getJson('/api/teams/get')
      ->assertOk()
      ->assertJsonPath('1.*.id', [$a->id, $b->id])
      ->assertJsonPath('2.*.id', [$c->id]);
  }

  public function test_status_order_and_destroy(): void
  {
    $a = $this->member(['order' => 1]);
    $b = $this->member(['order' => 2]);
    TeamDocument::create(['team_id' => $a->id, 'name' => 'cv.pdf']);

    $this->admin()->getJson("/api/team/status/{$a->id}")->assertOk()->assertExactJson([0]);

    $this->admin()->postJson('/api/team/order', ['teams' => [
      ['id' => $a->id, 'order' => 2],
      ['id' => $b->id, 'order' => 1],
    ]])->assertOk();
    $this->assertEquals([2, 1], [$a->fresh()->order, $b->fresh()->order]);

    $this->admin()->deleteJson("/api/team/destroy/{$a->id}")->assertOk();
    $this->assertModelMissing($a);
    $this->assertSame(0, TeamDocument::count());
  }
}
