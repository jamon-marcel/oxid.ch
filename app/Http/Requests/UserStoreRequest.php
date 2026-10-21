<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserStoreRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request. On update an empty
   * password keeps the current one.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'name' => 'required|string|max:255',
      'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->route('user'))],
      'password' => [$this->route('user') ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
    ];
  }

  /**
   * Custom message for validation. The admin shows them after the field's
   * label ("E-Mail: bereits vergeben").
   *
   * @return array
   */
  public function messages()
  {
    return [
      'required' => 'Pflichtfeld',
      'email.email' => 'ungültige Adresse',
      'email.unique' => 'bereits vergeben',
      'password.min' => 'mindestens :min Zeichen',
      'password.confirmed' => 'Bestätigung stimmt nicht überein',
    ];
  }
}
