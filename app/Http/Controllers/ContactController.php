<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\View\View;

class ContactController extends Controller
{
  public function index(): View
  {
    return view('frontend.pages.contact.index', [
      'pageFooter' => 'contact',
      'contact' => Contact::first(),
    ]);
  }
}
