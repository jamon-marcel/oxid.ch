<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * The admin API, tested on a throwaway in-memory SQLite database: the
 * tests create, reorder and delete freely without touching the local copy
 * of production (people edit it while tests run). The local disk is faked
 * too, because the image and document controllers delete files through it.
 */
abstract class AdminTestCase extends TestCase
{
  use RefreshDatabase;

  protected User $user;

  protected function beforeRefreshingDatabase(): void
  {
    config([
      'database.default' => 'sqlite',
      'database.connections.sqlite.database' => ':memory:',
    ]);
  }

  protected function setUp(): void
  {
    parent::setUp();

    // A guard, not a test: never write to the MySQL database
    $this->assertSame('sqlite', DB::connection()->getDriverName());

    Storage::fake('local');
    $this->user = User::factory()->create();
  }

  protected function admin(): static
  {
    return $this->actingAs($this->user);
  }
}
