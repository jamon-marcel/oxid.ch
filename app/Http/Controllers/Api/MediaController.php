<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Image;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;

class MediaController extends Controller
{
  protected $upload_path;

  protected $prefix = 'oxid';
  
  /**
   * Constructor
   */
  public function __construct()
  {
    $this->upload_path = storage_path('app/public/uploads');

    if (!File::isDirectory($this->upload_path))
    {
      File::makeDirectory($this->upload_path, 0775, true, true);
    }
  }

  /**
   * File upload
   * 
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function upload(Request $request)
  {
    $file = $request->file('file');
    $name = $this->sanitize(trim($file->getClientOriginalName()));
    $name = $this->prefix . '-' . uniqid()  . '_' . $name;
    $file->move($this->upload_path, $name);
    $filetype = \File::extension($this->upload_path . $name);
    
    $image_types = ['jpg', 'jpeg', 'png'];
    $orientation = '';
    if (in_array($filetype, $image_types))
    {
      $img = \Image::make(storage_path('app/public/uploads/') . $name);
      $orientation = $img->width() >= $img->height() ? 'l' : 'p';
    }
    return response()->json(['name' => $name, 'filetype' => $filetype, 'orientation' => $orientation], 200);
  }

  /**
   * Sanitize a filename
   *
   * @param str $filename
   * @param boolean  $force_lowercase - Force the string to lowercase?
   * @param boolean  $anal - If set to *true*, will remove all non-alphanumeric characters.
   */

  protected function sanitize($filename, $force_lowercase = true, $anal = true)
  {
    $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "=", "+", "[", "{", "]", "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;", "â€”", "â€“", ",", "<", ">", "/", "?");
    $clean = trim(str_replace($strip, "", strip_tags($filename)));
    $clean = preg_replace('/\s+/', "-", $clean);
    $clean = ($anal) ? preg_replace("/[^a-zA-Z0-9._\-]/", "", $clean) : $clean ;
    return ($force_lowercase) ? (function_exists('mb_strtolower')) ? mb_strtolower($clean, 'UTF-8') : strtolower($clean) : $clean;
  }
}
