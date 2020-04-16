<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class DiscourseStoreRequest extends FormRequest
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
      'heading.de' => 'required|string',
      'date.de' => 'required|string',
      'description_short.de' => 'required|string',
      'description.de' => 'required|string',
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
      'heading.de.required' => 'Heading is required!',
      'date.de.required' => 'Title short is required!',
      'description_short.de.required' => 'Description short is required!',
      'description.de.required' => 'Description is required!',
    ];
  }
}
