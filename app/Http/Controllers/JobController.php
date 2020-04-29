<?php
namespace App\Http\Controllers;
use App\Http\Controllers\BaseController;
use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends BaseController
{
  protected $pageFooter = 'office';
  protected $viewPath   = 'frontend.pages.office.job';

  // Models
  protected $images;
  protected $job;

  /**
   * Constructor
   * 
   */

  public function __construct(Job $job)
  {
    parent::__construct();
    $this->job = $job;
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
          'jobs' => $this->job->with('documents')->published()->get(),
          'pageFooter' => $this->pageFooter,
        ]
    );
  }
}
