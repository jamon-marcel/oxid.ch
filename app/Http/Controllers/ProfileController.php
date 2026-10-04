<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\ProfileImage;
use Illuminate\View\View;

class ProfileController extends Controller
{
  public function index(): View
  {
    return view('frontend.pages.office.profile', [
      'pageFooter' => 'office',
      'images' => ProfileImage::published()->orderBy('order')->get(),
      'profile' => Profile::published()->first(),
    ]);
  }
}
