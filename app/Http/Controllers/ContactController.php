<?php
namespace App\Http\Controllers;
use App\Http\Controllers\BaseController;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends BaseController
{
  protected $pageFooter = 'contact';
  protected $viewPath   = 'frontend.pages.contact.index';
  
  // Models
  protected $contact;

  /**
   * Constructor
   * 
   */

  public function __construct(Contact $contact)
  {
    parent::__construct();
    $this->contact = $contact;
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
        'contact' => $this->contact->get()->first(),
        'pageFooter' => $this->pageFooter,
        'showInfo' => FALSE,
      ]
    );
  }
}
