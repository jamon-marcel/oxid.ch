<?php
namespace App\Http\Controllers\Api;
use App\Http\Resources\DataCollection;
use App\Http\Controllers\Controller;
use Config;
use Lang;
use App;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
  protected $settings;
  
  public function __construct()
  {
    $this->settings = Config::get('settings');
  }

  /**
   * Get settings->program
   * @return \Illuminate\Http\Response
   */

  public function program()
  {
    return response()->json($this->translate('program'));
  }

  /**
   * Get settings->state
   * @return \Illuminate\Http\Response
   */

  public function state()
  {
    return response()->json($this->translate('state'));
  }

  /**
   * Get settings->authors
   * @return \Illuminate\Http\Response
   */

  public function authors()
  {
    return response()->json($this->settings['authors']);
  }


  protected function translate($type)
  {
    // Set to german for use in backend
    App::setLocale('de');
    
    // Get settings by type
    $settings = $this->settings[$type];

    // Translate
    $translated = [];
    foreach($settings as $key => $val)
    {
      $translated[$key] = Lang::get('settings.' . $val);
    }
    return $translated;
  }
}
