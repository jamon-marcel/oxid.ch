<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class TeamStoreRequest extends FormRequest
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
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'firstname' => 'required|string',
      'name' => 'required|string',
    ];
  }

  /**
   * Custom message for validation
   *
   * @return array
   */
  public function messages()
  {
    return [
      'firstname.required' => 'Firstname is required!',
      'name.required' => 'Name is required!',
    ];
  }
}
