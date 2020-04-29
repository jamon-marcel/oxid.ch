<?php
namespace App\Http\Controllers;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

class ProfileController extends BaseController
{
  protected $pageFooter = 'office';
  protected $viewPath   = 'frontend.pages.office.profile';
  
  /**
   * Constructor
   * 
   */

  public function __construct()
  {
    parent::__construct();
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
        'pageFooter' => $this->pageFooter,
        'showInfo' => TRUE,
      ]
    );
  }
}
