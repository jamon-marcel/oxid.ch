<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\GridLayout;
use App\Http\Resources\DataCollection;

use Illuminate\Http\Request;

class GridLayoutController extends Controller
{
  protected $gridLayout;

  public function __construct(GridLayout $gridLayout)
  {
    $this->gridLayout = $gridLayout;
  }

  /**
   * Get all layouts
   *
   * @return \Illuminate\Http\Response
   */
  public function get()
  {
    return new DataCollection($this->gridLayout->get());
  }
}
