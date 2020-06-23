<?php
namespace App\Http\Controllers;
use App\Http\Controllers\BaseController;
use App\Models\Profile;
use App\Models\ProfileImage;
use Illuminate\Http\Request;

class ProfileController extends BaseController
{
  protected $pageFooter = 'office';
  protected $viewPath   = 'frontend.pages.office.profile';
  
  // Models
  protected $profile;
  protected $profileImage;

  /**
   * Constructor
   * 
   */

  public function __construct(Profile $profile, ProfileImage $profileImage)
  {
    parent::__construct();
    $this->profile       = $profile;
    $this->profileImage  = $profileImage;
  }

  /**
   * Show the team page
   *
   * @return \Illuminate\Http\Response
   */

  public function index()
  {
    return 
      view($this->viewPath,
      [
        'images'  => $this->profileImage->published()->orderBy('order')->get(),
        'profile' => $this->profile->published()->get()->first(),
        'pageFooter' => $this->pageFooter,
        'showInfo' => TRUE,
      ]
    );
  }
}
