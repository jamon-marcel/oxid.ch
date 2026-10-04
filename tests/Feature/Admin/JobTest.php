<?php

namespace Tests\Feature\Admin;

use App\Models\Job;
use App\Models\JobDocument;

class JobTest extends AdminTestCase
{
  protected function payload(array $overrides = []): array
  {
    return array_replace_recursive([
      'title' => ['de' => 'Architekt:in 80–100%', 'en' => 'Architect 80–100%'],
      'description' => ['de' => '<p>Wir suchen</p>', 'en' => '<p>We are looking</p>'],
      'info' => ['de' => 'Ab sofort', 'en' => 'Immediately'],
      'publish' => 1,
    ], $overrides);
  }

  protected function job(array $attributes = []): Job
  {
    return Job::create(array_merge($this->payload(), $attributes));
  }

  public function test_store_saves_the_job_and_documents(): void
  {
    $response = $this->admin()->postJson('/api/job/create', $this->payload([
      'documents' => [['name' => 'inserat.pdf', 'caption' => ['de' => 'Inserat', 'en' => 'Ad'], 'publish' => 1]],
    ]))->assertOk();

    $job = Job::findOrFail($response->json('jobId'));
    $this->assertSame('Architect 80–100%', $job->getTranslation('title', 'en'));
    $this->assertSame('Inserat', $job->documents()->sole()->getTranslation('caption', 'de'));
  }

  public function test_store_validates(): void
  {
    $this->admin()->postJson('/api/job/create', ['title' => ['de' => 'Titel']])
      ->assertUnprocessable()
      ->assertJsonValidationErrors(['description.de']);
  }

  public function test_update_changes_fields_and_upserts_documents(): void
  {
    $job = $this->job();
    $document = JobDocument::create(['job_id' => $job->id, 'name' => 'old.pdf', 'publish' => 1]);

    $this->admin()->postJson("/api/job/update/{$job->id}", $this->payload([
      'title' => ['de' => 'Praktikum'],
      'publish' => 0,
      'documents' => [
        ['id' => $document->id, 'name' => 'old.pdf', 'caption' => ['de' => 'Alt', 'en' => ''], 'publish' => 1],
        ['id' => null, 'name' => 'new.pdf', 'caption' => ['de' => 'Neu', 'en' => ''], 'publish' => 1],
      ],
    ]))->assertOk();

    $job->refresh();
    $this->assertSame('Praktikum', $job->getTranslation('title', 'de'));
    $this->assertEquals(0, $job->publish);
    $this->assertSame('Alt', $document->fresh()->getTranslation('caption', 'de'));
    $this->assertSame(2, $job->documents()->count());
  }

  public function test_status_order_and_destroy(): void
  {
    $a = $this->job(['order' => 1]);
    $b = $this->job(['order' => 2]);
    JobDocument::create(['job_id' => $a->id, 'name' => 'a.pdf']);

    $this->admin()->getJson("/api/job/status/{$a->id}")->assertOk()->assertExactJson([0]);

    $this->admin()->postJson('/api/job/order', ['jobs' => [
      ['id' => $b->id, 'order' => 1],
      ['id' => $a->id, 'order' => 2],
    ]])->assertOk();
    $this->admin()->getJson('/api/jobs/get')->assertOk()->assertJsonPath('data.*.id', [$b->id, $a->id]);

    $this->admin()->deleteJson("/api/job/destroy/{$a->id}")->assertOk();
    $this->assertModelMissing($a);
    $this->assertSame(0, JobDocument::count());
  }
}
