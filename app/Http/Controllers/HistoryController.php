<?php
namespace App\Http\Controllers;
use App\Http\Controllers\BaseController;
use App\Models\HomeImage;
use App\Models\News;
use Illuminate\Http\Request;

class HistoryController extends BaseController
{
  protected $viewPath   = 'frontend.pages.history.index';
  protected $pageFooter = 'history';

  /**
   * Show the history page
   *
   * @return \Illuminate\Http\Response
   */

  public function index()
  {
    return 
      view(
        $this->viewPath,
        [
          'pageFooter' => $this->pageFooter
        ]
    );
  }
}
