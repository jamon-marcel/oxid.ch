<?php
namespace App\Http\Controllers\Api;
use App\Models\Job;
use App\Models\JobDocument;
use App\Http\Resources\DataCollection;
use App\Http\Requests\JobStoreRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JobController extends Controller
{
  protected $job;
  protected $jobDocument;
  
  /**
   * Constructor
   * 
   * @param Job $job
   */

  public function __construct(Job $job, JobDocument $jobDocument)
  {
    $this->job = $job;
    $this->jobDocument = $jobDocument;
  }

  /**
   * Get all records
   *
   * @return \Illuminate\Http\Response
   */

  public function get()
  {
    return new DataCollection($this->job->orderBy('order', 'ASC')->get());
  }

  /**
   * Fetch one record
   *
   * @param Job $job
   * @return \Illuminate\Http\Response
   */

  public function getOne(Job $job)
  {
    return response()->json($job);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  
  public function store(Request $request)
  {   
    $job = new Job([
      'title' => [
        'de' => $request->input('title.de'),
        'en' => $request->input('title.en'),
      ],
      'description' => [
        'de' => $request->input('description.de'),
        'en' => $request->input('description.en'),
      ],
      'info' => [
        'de' => $request->input('info.de'),
        'en' => $request->input('info.en'),
      ],
      'publish'  => $request->input('publish'),
    ]);
    $job->save();
    
    // Documents
    if (!empty($request->documents))
    {
      foreach($request->documents as $i)
      {
        $document = new JobDocument([
          'job_id' => $job->id,
          'name' => $i['name'],
          'caption' => [
            'de' => isset($i['caption']['de']) ? $i['caption']['de'] : null,
            'en' => $i['caption']['en'],    
          ],
          'publish' => $i['publish'],
        ]);
        $document->save();
      }
    }

    return response()->json(['jobId' => $job->id]);
  }

  /**
   * Edit a specified resource.
   *
   * @param Job $job
   * @return \Illuminate\Http\Response
   */
  public function edit(Job $job)
  {
    $job = $this->job->with('documents')->findOrFail($job->id);
    return response()->json($job);
  }

  /**
   * Update the status of the specified resource.
   *
   * @param Job $job
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function update(Job $job, JobStoreRequest $request)
  {
    $job = $this->job->findOrFail($job->id);
    
    // German
    $job->setTranslation('title', 'de', $request->input('title.de'));
    $job->setTranslation('description', 'de', $request->input('description.de'));
    $job->setTranslation('info', 'de', $request->input('info.de'));

    // English
    $job->setTranslation('title', 'en', $request->input('title.en'));
    $job->setTranslation('description', 'en', $request->input('description.en'));
    $job->setTranslation('info', 'en', $request->input('info.en'));

    $job->publish  = $request->input('publish');
    $job->save();
   
    // Documents
    if (!empty($request->documents))
    {
      foreach($request->documents as $i)
      {
        $document = JobDocument::updateOrCreate(
          ['id' => $i['id']], 
          [
            'job_id' => $job->id,
            'name' => $i['name'],
            'caption' => [
              'de' => $i['caption']['de'],
              'en' => $i['caption']['en']
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
   * @param  Job $job
   * @return \Illuminate\Http\Response
   */
  public function status(Job $job)
  {
    $job->publish = $job->publish == 0 ? 1 : 0;
    $job->save();
    return response()->json($job->publish);
  }

  /**
   * Update the order of the resources.
   *
   * @param  \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */

  public function order(Request $request)
  {
    $jobs = $request->get('jobs');
    foreach($jobs as $job)
    {
      $j = $this->job->find($job['id']);
      $j->order = $job['order'];
      $j->save(); 
    }
    return response()->json('successfully updated');
  }

  /**
   * Remove the specified resource from storage.
   *
   * \Observers\JobObserver observes and deletes child elements (images, documents).
   *
   * @param  Job $job
   * @return \Illuminate\Http\Response
   */
  public function destroy(Job $job)
  {
    $job->delete();
    return response()->json('successfully deleted');
  }
}
