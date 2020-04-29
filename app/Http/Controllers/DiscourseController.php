<?php
namespace App\Http\Controllers;
use App\Http\Controllers\BaseController;
use App\Models\Discourse;
use Illuminate\Http\Request;

class DiscourseController extends BaseController
{
  protected $pageFooter = 'discourse';
  protected $viewPath   = 'frontend.pages.discourse.';

  // Models
  protected $discourse;

  /**
   * Constructor
   * 
   */

  public function __construct(Discourse $discourse)
  {
    parent::__construct();
    $this->discourse = $discourse;
  }

  /**
   * Show all entries
   *
   * @return \Illuminate\Http\Response
   */

  public function index()
  {
    $discourse = $this->discourse->with('publishedImages')->published()->orderBy('category')->orderBy('order')->get();
    return 
      view($this->viewPath . 'index', 
        [
          'pageFooter' => $this->pageFooter,
          'pageTitle'  => '',
          'discourse'  => $discourse
        ]
  );
  }

  /**
   * Show entries by categorie 'research'
   *
   * @return \Illuminate\Http\Response
   */

  public function research()
  {
    $discourse = $this->discourse->with('publishedImages')->research()->orderBy('order')->get();
    return 
      view($this->viewPath . 'index', 
        [
          'pageFooter' => $this->pageFooter,
          'pageTitle'  => 'Recherche',
          'discourse'  => $discourse
        ]
    );
  }

  /**
   * Show entries by categorie 'events'
   *
   * @return \Illuminate\Http\Response
   */

  public function events()
  {
    $discourse = $this->discourse->with('publishedImages')->events()->orderBy('order')->get();
    return 
      view($this->viewPath . 'index', 
        [
          'pageFooter' => $this->pageFooter,
          'pageTitle'  => 'Veranstaltungen',
          'discourse'  => $discourse
        ]
    );
  }

  /**
   * Show entries by categorie 'publications'
   *
   * @return \Illuminate\Http\Response
   */

  public function publications()
  {
    $discourse = $this->discourse->with('publishedImages')->publications()->orderBy('order')->get();
    return 
      view($this->viewPath . 'index', 
        [
          'pageFooter' => $this->pageFooter,
          'pageTitle'  => 'Publikationen',
          'discourse'  => $discourse
        ]
    );
  }

}
