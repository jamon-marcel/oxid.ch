<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TeamController extends Controller
{
  /**
   * Constructor
   * 
   */

  public function __construct()
  {

  }

  /**
   * Show the homepage
   *
   * @return \Illuminate\Http\Response
   */

  public function index()
  {
    return 
      view('frontend.pages.office.team', 
        [

        ]
    );
  }
}
