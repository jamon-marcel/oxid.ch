<?php
namespace App\Http\Controllers\Api;
use App\Models\Profile;
use App\Models\ProfileImage;
use App\Http\Resources\DataCollection;
use App\Http\Requests\ProfileStoreRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
  protected $profile;
  protected $profileImage;
  
  /**
   * Constructor
   * 
   * @param Profile $profile
   */

  public function __construct(Profile $profile, ProfileImage $profileImage)
  {
    $this->profile = $profile;
    $this->profileImage = $profileImage;
  }

  /**
   * Get all records
   *
   * @return \Illuminate\Http\Response
   */

  public function get()
  {
    return response()->json($this->profile->get());
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  
  public function store(ProfileStoreRequest $request)
  {   
    $profile = new Profile([
      'title' => [
        'de' => $request->input('title.de'),
        'en' => $request->input('title.en'),
      ],
      'description' => [
        'de' => $request->input('description.de'),
        'en' => $request->input('description.en'),
      ],
      'publish'  => $request->input('publish'),
    ]);
    $profile->save();

    // Images
    if (!empty($request->images))
    {
      foreach($request->images as $i)
      {
        $document = new ProfileImage([
          'profile_id' => $profile->id,
          'name' => $i['name'],
          'caption' => [
            'de' => isset($i['caption']['de']) ? $i['caption']['de'] : null,
            'en' => isset($i['caption']['en']) ? $i['caption']['en'] : null,
          ],
          'publish' => $i['publish'],
        ]);
        $document->save();
      }
    }

    return response()->json(['profileId' => $profile->id]);
  }

  /**
   * Edit a specified resource.
   *
   * @param Profile $profile
   * @return \Illuminate\Http\Response
   */

  public function edit(Profile $profile)
  {
    $profile = $this->profile->findOrFail($profile->id);
    return response()->json($profile);
  }

  /**
   * Update the status of the specified resource.
   *
   * @param Profile $profile
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */

  public function update(Profile $profile, ProfileStoreRequest $request)
  {
    $profile = $this->profile->findOrFail($profile->id);

    // Translations
    $profile->setTranslation('title', 'de', $request->input('title.de'));
    $profile->setTranslation('title', 'en', $request->input('title.en'));
    $profile->setTranslation('description', 'de', $request->input('description.de'));
    $profile->setTranslation('description', 'en', $request->input('description.en'));
    $profile->publish  = $request->input('publish');
    $profile->save();
    
    // Images
    if (!empty($request->images))
    {
      foreach($request->images as $i)
      {
        $document = ProfileImage::updateOrCreate(
          ['id' => $i['id']], 
          [
            'profile_id' => $profile->id,
            'name' => $i['name'],
            'caption' => [
              'de' => isset($i['caption']['de']) ? $i['caption']['de'] : null,
              'en' => isset($i['caption']['en']) ? $i['caption']['en'] : null,
            ],
            'publish' => $i['publish'] ? $i['publish'] : 0,
          ]
        );
      }
    }

    return response()->json('successfully updated');
  }

  /**
   * Update the status of the specified resource.
   *
   * @param  Profile $profile
   * @return \Illuminate\Http\Response
   */
  
  public function status(Profile $profile)
  {
    $profile->publish = $profile->publish == 0 ? 1 : 0;
    $profile->save();
    return response()->json($profile->publish);
  }

}
