<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobImage;
use Illuminate\View\View;

class JobController extends Controller
{
  public function index(): View
  {
    return view('frontend.pages.office.job', [
      'pageFooter' => 'office',
      'images' => JobImage::published()->orderBy('order')->get(),
      'jobs' => Job::with('documents')->published()->orderBy('order')->get(),
    ]);
  }
}
