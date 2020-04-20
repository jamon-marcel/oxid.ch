<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class JobStoreRequest extends FormRequest
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
      'title.de' => 'required|string',
      'description.de' => 'required|string',
      'info.de' => 'required|string',
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
      'title.de.required' => 'Title is required!',
      'description.de.required' => 'Description is required!',
      'info.de.required' => 'Info is required!',
    ];
  }
}
