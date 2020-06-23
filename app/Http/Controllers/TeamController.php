<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamImage;
use Illuminate\Http\Request;

class TeamController extends BaseController
{
  protected $pageFooter = 'office';
  protected $viewPath   = 'frontend.pages.office.team';

  // Models
  protected $teamImages;
  protected $team;

  /**
   * Constructor
   * 
   */

  public function __construct(Team $team, TeamImage $teamImages)
  {
    parent::__construct();
    $this->teamImages  = $teamImages;
    $this->team        = $team;
  }

  /**
   * Show the team page
   *
   * @return \Illuminate\Http\Response
   */

  public function index()
  {
    $alumni = [];
    $alumnis = $this->team->with('documents')->published()->alumni()->get();

    foreach($alumnis as $a)
    {
      $alumni[substr(strtoupper($a->name), 0, 1)][] = $a;
    }

    $chunks = collect($alumni)->chunk(ceil(count($alumni)/2));

    return 
      view($this->viewPath, 
        [
          'images'  => $this->teamImages->published()->orderBy('order')->get(),
          'team'    => [
            'partner'  => $this->team->with('documents')->published()->partner()->get(),
            'employee' => $this->team->with('documents')->published()->employee()->get(),
            'alumni'   => $chunks,
          ],
          'pageFooter' => $this->pageFooter,
          'showInfo' => TRUE,
        ]
    );
  }
}
