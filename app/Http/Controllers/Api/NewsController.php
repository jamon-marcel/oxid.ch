<?php
namespace App\Http\Controllers\Api;
use App\Models\News;
use App\Http\Resources\DataCollection;
use App\Http\Requests\NewsStoreRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NewsController extends Controller
{
  protected $news;
  
  /**
   * Constructor
   * 
   * @param News $news
   */

  public function __construct(News $news)
  {
    $this->news = $news;
  }

  /**
   * Get all records
   *
   * @return \Illuminate\Http\Response
   */

  public function get()
  {
    return new DataCollection($this->news->orderBy('order', 'ASC')->get());
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  
  public function store(NewsStoreRequest $request)
  {   
    $news = new News($request->all());
    $news->save();
    return response()->json(['newsId' => $news->id]);
  }

  /**
   * Edit a specified resource.
   *
   * @param News $news
   * @return \Illuminate\Http\Response
   */
  public function edit(News $news)
  {
    return response()->json($news);
  }

  /**
   * Update the status of the specified resource.
   *
   * @param News $news
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function update(News $news, NewsStoreRequest $request)
  {
    $news->update($request->all());
    return response()->json('successfully updated');
  }

  /**
   * Update the status of the specified resource.
   *
   * @param  News $news
   * @return \Illuminate\Http\Response
   */
  public function status(News $news)
  {
    $news->publish = $news->publish == 0 ? 1 : 0;
    $news->save();
    return response()->json($news->publish);
  }

  /**
   * Update the order of the resources.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */

  public function order(Request $request)
  {
    $news = $request->get('news');
    foreach($news as $n)
    {
      $d = $this->news->find($n['id']);
      $d->order = $n['order'];
      $d->save(); 
    }
    return response()->json('successfully updated');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  News $news
   * @return \Illuminate\Http\Response
   */
  public function destroy(News $news)
  {
    $news->delete();
    return response()->json('successfully deleted');
  }
}
