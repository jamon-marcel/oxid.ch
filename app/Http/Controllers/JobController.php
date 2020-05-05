<?php
namespace App\Http\Controllers;
use App\Http\Controllers\BaseController;
use App\Models\Job;
use App\Models\JobImage;
use Illuminate\Http\Request;

class JobController extends BaseController
{
  protected $pageFooter = 'office';
  protected $viewPath   = 'frontend.pages.office.job';

  // Models
  protected $job;
  protected $jobImage;

  /**
   * Constructor
   * 
   */

  public function __construct(Job $job, JobImage $jobImage)
  {
    parent::__construct();
    $this->job = $job;
    $this->jobImage = $jobImage;
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
          'images'      => $this->jobImage->published()->get(),
          'jobs'        => $this->job->orderBy('order', 'ASC')->with('documents')->published()->get(),
          'pageFooter'  => $this->pageFooter,
        ]
    );
  }
}
