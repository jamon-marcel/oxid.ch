<?php
namespace App\Http\Controllers\Api;
use App\Models\Team;
use App\Models\TeamDocument;
use App\Http\Resources\DataCollection;
use App\Http\Requests\TeamStoreRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TeamController extends Controller
{
  protected $team;
  protected $teamDocument;
  
  /**
   * Constructor
   * 
   * @param Team $team
   */

  public function __construct(Team $team, TeamDocument $teamDocument)
  {
    $this->team = $team;
    $this->teamDocument = $teamDocument;
  }

  /**
   * Get all records
   *
   * @return \Illuminate\Http\Response
   */

  public function get()
  {
    $team = $this->team->orderBy('order', 'ASC')->get();
    return response()->json($team->groupBy('category'));
  }

  /**
   * Fetch one record
   *
   * @param Team $team
   * @return \Illuminate\Http\Response
   */

  public function getOne(Team $team)
  {
    return response()->json($team);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  
  public function store(Request $request)
  {   
    $team = new Team([
      'firstname' => $request->input('firstname'),
      'name' => $request->input('name'),
      'email' => $request->input('email'),
      'phone' => $request->input('phone'),
      'role' => [
        'de' => $request->input('role.de'),
        'en' => $request->input('role.en'),
      ],
      'position' => [
        'de' => $request->input('position.de'),
        'en' => $request->input('position.en'),
      ],
      'category' => $request->input('category'),
      'publish'  => $request->input('publish'),
    ]);
    $team->save();

    // Documents
    if (!empty($request->documents))
    {
      foreach($request->documents as $i)
      {
        $document = new TeamDocument([
          'team_id' => $team->id,
          'name' => $i['name'],
          'language' => $i['language'],
          'caption' => [
            'de' => isset($i['caption']['de']) ? $i['caption']['de'] : null,
            'en' => isset($i['caption']['en']) ? $i['caption']['en'] : null,
          ],
          'publish' => $i['publish'],
        ]);
        $document->save();
      }
    }

    return response()->json(['teamId' => $team->id]);
  }

  /**
   * Edit a specified resource.
   *
   * @param Team $team
   * @return \Illuminate\Http\Response
   */
  public function edit(Team $team)
  {
    $team = $this->team->with('documents')->findOrFail($team->id);
    return response()->json($team);
  }

  /**
   * Update the status of the specified resource.
   *
   * @param Team $team
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function update(Team $team, TeamStoreRequest $request)
  {
    $team = $this->team->findOrFail($team->id);
    $team->firstname = $request->input('firstname');
    $team->name = $request->input('name');
    $team->email = $request->input('email');
    $team->phone = $request->input('phone');

    // Translations
    $team->setTranslation('role', 'de', $request->input('role.de'));
    $team->setTranslation('role', 'en', $request->input('role.en'));
    $team->setTranslation('position', 'de', $request->input('position.de'));
    $team->setTranslation('position', 'en', $request->input('position.en'));

    $team->category = $request->input('category');
    $team->publish  = $request->input('publish');
    $team->save();
    
    // Documents
    if (!empty($request->documents))
    {
      foreach($request->documents as $i)
      {
        $document = TeamDocument::updateOrCreate(
          ['id' => $i['id']], 
          [
            'team_id' => $team->id,
            'name' => $i['name'],
            'language' => $i['language'],
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
   * @param  Team $team
   * @return \Illuminate\Http\Response
   */
  public function status(Team $team)
  {
    $team->publish = $team->publish == 0 ? 1 : 0;
    $team->save();
    return response()->json($team->publish);
  }

  /**
   * Update the order of the resources.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */

  public function order(Request $request)
  {
    $teams = $request->get('teams');
    foreach($teams as $team)
    {
      $t = $this->team->find($team['id']);
      $t->order = $team['order'];
      $t->save(); 
    }
    return response()->json('successfully updated');
  }

  /**
   * Remove the specified resource from storage.
   *
   * \Observers\TeamObserver observes and deletes child elements (images, documents).
   *
   * @param  Team $team
   * @return \Illuminate\Http\Response
   */
  public function destroy(Team $team)
  {
    $team->delete();
    return response()->json('successfully deleted');
  }
}
