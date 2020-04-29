<div class="menu-footer menu-footer--projects">
  <div>
    <h2 class="menu-footer__heading">Projekte</h2>
    <nav class="menu-footer__browse">
      <a href="{{ route('page.project', ['slug' => AppHelper::slug($navBrowse['prev']), 'project' => $navBrowse['prev']->id]) }}" class="icon-browse-prev"></a>
      <a href="{{ route('page.project', ['slug' => AppHelper::slug($navBrowse['next']), 'project' => $navBrowse['next']->id]) }}" class="icon-browse-next"></a>
    </nav>
  </div>
  <div class="menu-footer__project-info">
    <h1>{{$project->title_short}}, {{$project->location}}</h1>&nbsp;
    <span><em class="js-project-idx">1</em>/{{count($project_grid)}}</span>
  </div>
  <div class="menu-footer__info">
    @if ($project->publishedDocuments && isset($project->publishedDocuments[0]))
      <a href="{{asset('storage/uploads/' . $project->publishedDocuments[0]->name)}}" class="icon-document hide-below-md" target="_blank" title="Download Projektdokumentation">
        Pdf
      </a>
    @endif
    <a href="javascript:;" class="anchor-ul js-btn-info">Info</a>
  </div>
</div>