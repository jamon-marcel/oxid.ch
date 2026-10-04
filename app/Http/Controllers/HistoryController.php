<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HistoryController extends Controller
{
  public function index(): View
  {
    return view('frontend.pages.history.index', ['pageFooter' => 'history']);
  }
}
