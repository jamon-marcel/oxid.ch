<?php
namespace App\Services;
use App\Models\Project;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

class Menu
{
  // Menu item active class
  protected $active = 'is-active';

  // Models
  protected $project;

  public function __construct()
  {
    $project = new \App\Models\Project;
    $this->project = $project;
  }

  public function boot()
  {
    $data = [
      'project' => $this->project->highlight()->get()
    ];
    View::share('menuItems', $data);
  }

}