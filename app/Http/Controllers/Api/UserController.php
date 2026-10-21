<?php
namespace App\Http\Controllers\Api;
use App\Models\User;
use App\Http\Resources\DataCollection;
use App\Http\Requests\UserStoreRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
  /**
   * Get all records
   *
   * @return \Illuminate\Http\Response
   */
  public function get()
  {
    return new DataCollection(User::orderBy('name')->get());
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  UserStoreRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(UserStoreRequest $request)
  {
    $user = User::create($request->only('name', 'email', 'password'));
    return response()->json(['userId' => $user->id]);
  }

  /**
   * Edit a specified resource.
   *
   * @param User $user
   * @return \Illuminate\Http\Response
   */
  public function edit(User $user)
  {
    return response()->json($user);
  }

  /**
   * Update the specified resource; an empty password keeps the current one.
   *
   * A new password ends the user's other sessions (Sanctum's
   * AuthenticateSession compares the password hash). For one's own account
   * the guard gets the updated user, so the current session stores the new
   * hash and stays logged in.
   *
   * @param User $user
   * @param  UserStoreRequest $request
   * @return \Illuminate\Http\Response
   */
  public function update(User $user, UserStoreRequest $request)
  {
    $user->fill($request->only('name', 'email'));
    if ($request->filled('password')) {
      $user->password = $request->input('password');
    }
    $user->save();

    if ($request->user()->is($user)) {
      Auth::guard('web')->setUser($user);
    }

    return response()->json('successfully updated');
  }

  /**
   * Remove the specified resource from storage. One's own account can't be
   * deleted, so there is always a user left.
   *
   * @param  User $user
   * @param  \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */
  public function destroy(User $user, Request $request)
  {
    abort_if($request->user()->is($user), 403, 'Das eigene Konto kann nicht gelöscht werden.');
    $user->delete();
    return response()->json('successfully deleted');
  }
}
