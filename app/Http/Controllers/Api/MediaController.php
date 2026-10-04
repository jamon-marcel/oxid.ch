<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Support\ImageSupport;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;

class MediaController extends Controller
{
  /**
   * Largest upload in KB. The admin's uploaders show and check the same
   * limit (resources/js/backend/composables/useImages.js, useFiles.js).
   */
  public const MAX_KB = 8 * 1024;

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
    // Was checked only in the browser; the API took any file of any size.
    // Content (mimes) and name (extensions): a JPEG named .php passes
    // mimes alone and would be stored as .php in the public uploads.
    $request->validate([
      'file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'extensions:jpg,jpeg,png,pdf', 'max:' . self::MAX_KB],
    ], [
      'file.required' => 'Keine Datei erhalten.',
      'file.file' => 'Keine Datei erhalten.',
      'file.uploaded' => 'Die Datei konnte nicht hochgeladen werden (zu gross für den Server?).',
      'file.mimes' => 'Dateityp nicht erlaubt (erlaubt: jpg, png, pdf).',
      'file.extensions' => 'Dateityp nicht erlaubt (erlaubt: jpg, png, pdf).',
      'file.max' => 'Datei ist zu gross (max. ' . (self::MAX_KB / 1024) . ' MB).',
    ]);

    $file = $request->file('file');
    $name = $this->sanitize(trim($file->getClientOriginalName()));
    $name = uniqid()  . '_' . $name;
    $file->move($this->upload_path, $name);
    $filetype = \File::extension($this->upload_path . $name);
    
    $image_types = ['jpg', 'jpeg', 'png'];
    $orientation = '';
    if (in_array($filetype, $image_types))
    {
      // From the file header (EXIF rotation included), not a full decode:
      // GD needs ~4 bytes per pixel, 269 MB for a 65 MP floor plan
      [$width, $height] = ImageSupport::dimensions($this->upload_path . '/' . $name) ?? [1, 0];
      $orientation = $width >= $height ? 'l' : 'p';
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
