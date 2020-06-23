<?php
namespace App\Http\Controllers;
use App\Models\Team;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

class DataController extends BaseController
{

  /**
   * Constructor
   * 
   */

  public function __construct(Team $team)
  {
    $this->team = $team;
  }

  /**
   * Import team data
   *
   * @return \Illuminate\Http\Response
   */

  public function index()
  {

  }
}
