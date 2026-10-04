<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
  /**
   * Log in with a session cookie. The SPA fetches /sanctum/csrf-cookie first.
   */
  public function login(Request $request): JsonResponse
  {
    $credentials = $request->validate([
      'email' => 'required|email',
      'password' => 'required',
    ]);

    if (! Auth::guard('web')->attempt($credentials)) {
      return response()->json(['error' => 'Unauthorized'], 401);
    }

    $request->session()->regenerate();

    return response()->json($request->user());
  }

  /**
   * Get the authenticated user.
   */
  public function me(Request $request): JsonResponse
  {
    return response()->json($request->user());
  }

  /**
   * Log out and invalidate the session.
   */
  public function logout(Request $request): JsonResponse
  {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->json(['message' => 'Successfully logged out']);
  }
}
