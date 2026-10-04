<?php

namespace Tests\Feature\Admin;

use Illuminate\Support\Facades\Route;

/**
 * Sanctum's cookie-based SPA auth: every api/* route except login needs a
 * session, and answers with JSON either way.
 */
class AuthTest extends AdminTestCase
{
  public function test_every_api_route_needs_a_login(): void
  {
    $routes = collect(Route::getRoutes())
      ->filter(fn ($route) => in_array('auth:sanctum', $route->gatherMiddleware()));

    $this->assertGreaterThan(100, $routes->count());

    foreach ($routes as $route) {
      $uri = preg_replace('/\{\w+\??\}/', '1', $route->uri());
      $method = collect($route->methods())->reject(fn ($m) => $m === 'HEAD')->first();

      $this->json($method, '/' . $uri)
        ->assertUnauthorized()
        ->assertJson(['message' => 'Unauthenticated.']);
    }
  }

  public function test_login_starts_a_session(): void
  {
    $this->spa()->postJson('/api/auth/login', ['email' => $this->user->email, 'password' => 'password'])
      ->assertOk()
      ->assertJson(['id' => $this->user->id, 'email' => $this->user->email])
      ->assertJsonMissingPath('password');

    $this->assertAuthenticatedAs($this->user);
    $this->spa()->postJson('/api/auth/me')->assertOk()->assertJson(['id' => $this->user->id]);
  }

  public function test_login_with_a_wrong_password_is_refused(): void
  {
    $this->spa()->postJson('/api/auth/login', ['email' => $this->user->email, 'password' => 'wrong'])
      ->assertUnauthorized()
      ->assertJson(['error' => 'Unauthorized']);

    $this->assertGuest();
  }

  public function test_login_validates_its_input(): void
  {
    $this->spa()->postJson('/api/auth/login', ['email' => 'not-an-email'])
      ->assertUnprocessable()
      ->assertJsonValidationErrors(['email', 'password']);
  }

  public function test_logout_ends_the_session(): void
  {
    $this->admin()->spa()->postJson('/api/auth/logout')->assertOk();

    $this->assertGuest('web');
  }

  public function test_unknown_api_routes_answer_with_json(): void
  {
    $this->getJson('/api/nope')->assertNotFound()->assertJsonStructure(['message']);
  }

  /**
   * Sanctum only starts a session for requests from a stateful domain
   */
  protected function spa(): static
  {
    return $this->withHeader('Referer', config('app.url'));
  }
}
