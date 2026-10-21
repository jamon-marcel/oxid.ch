<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserTest extends AdminTestCase
{
  protected function payload(array $overrides = []): array
  {
    return array_replace([
      'name' => 'Anna Muster',
      'email' => 'anna@example.com',
      'password' => 'geheim1234',
      'password_confirmation' => 'geheim1234',
    ], $overrides);
  }

  public function test_list_is_sorted_by_name_and_hides_passwords(): void
  {
    User::factory()->create(['name' => 'Aaron']);

    $this->admin()->getJson('/api/users/get')
      ->assertOk()
      ->assertJsonPath('data.0.name', 'Aaron')
      ->assertJsonMissingPath('data.0.password');
  }

  public function test_store_creates_a_user_who_can_log_in(): void
  {
    $response = $this->admin()->postJson('/api/user/create', $this->payload())->assertOk();

    $user = User::findOrFail($response->json('userId'));
    $this->assertSame('anna@example.com', $user->email);
    $this->assertTrue(Hash::check('geheim1234', $user->password));
  }

  public function test_store_validates(): void
  {
    $this->admin()->postJson('/api/user/create', $this->payload([
      'name' => '',
      'email' => $this->user->email,
      'password' => 'kurz',
      'password_confirmation' => 'anders',
    ]))
      ->assertUnprocessable()
      ->assertJsonValidationErrors(['name' => 'Pflichtfeld', 'email' => 'bereits vergeben', 'password']);
  }

  public function test_update_keeps_the_password_when_it_is_empty(): void
  {
    $user = User::factory()->create(['email' => 'old@example.com']);
    $hash = $user->password;

    // The own address is not "already taken"
    $this->admin()->postJson("/api/user/update/{$user->id}", $this->payload([
      'email' => 'old@example.com',
      'password' => null,
      'password_confirmation' => null,
    ]))->assertOk();

    $user->refresh();
    $this->assertSame('Anna Muster', $user->name);
    $this->assertSame($hash, $user->password);

    $this->admin()->postJson("/api/user/update/{$user->id}", $this->payload(['email' => 'old@example.com']))->assertOk();
    $this->assertTrue(Hash::check('geheim1234', $user->fresh()->password));
  }

  public function test_changing_the_own_password_keeps_the_session(): void
  {
    $this->spa()->postJson('/api/auth/login', ['email' => $this->user->email, 'password' => 'password'])->assertOk();

    $this->spa()->postJson("/api/user/update/{$this->user->id}", $this->payload(['email' => $this->user->email]))->assertOk();

    // A new request in production starts with fresh guards; here they
    // would keep the user (and old password) from the last request
    $this->app['auth']->forgetGuards();
    $this->spa()->postJson('/api/auth/me')->assertOk()->assertJson(['id' => $this->user->id]);
  }

  public function test_destroy_deletes_others_but_not_the_own_account(): void
  {
    $other = User::factory()->create();

    $this->admin()->deleteJson("/api/user/destroy/{$other->id}")->assertOk();
    $this->assertModelMissing($other);

    $this->admin()->deleteJson("/api/user/destroy/{$this->user->id}")
      ->assertForbidden()
      ->assertJson(['message' => 'Das eigene Konto kann nicht gelöscht werden.']);
    $this->assertModelExists($this->user);
  }

  /**
   * Sanctum only starts a session for requests from a stateful domain
   */
  protected function spa(): static
  {
    return $this->withHeader('Referer', config('app.url'));
  }
}
