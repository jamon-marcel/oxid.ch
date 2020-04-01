<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class ProjectStoreRequest extends FormRequest
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
      'title_short.de' => 'required|string',
      'location.de' => 'required|string',
      'year.de' => 'required|string',
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
      'title_short.de.required' => 'Title short is required!',
      'location.de.required' => 'Location is required!',
      'year.de.required' => 'Year is required!',
    ];
  }
}
