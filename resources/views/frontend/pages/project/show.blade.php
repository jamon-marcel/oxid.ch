@extends('frontend.layout.pages')
@section('seo_title', $project->title . ' - Projekte')
@section('seo_description', '')
@section('content')
<section class="content">
  @if ($project_grid->isNotEmpty())
    <div class="project-grids js-project-grids">
      <a href="javascript:;" class="btn-scroll is-prev js-btn-scroll-prev"></a>
      <a href="javascript:;" class="btn-scroll is-next js-btn-scroll-next"></a>
      @foreach($project_grid as $grid)
        @if ($grid->layout->key == '1' || $grid->layout->key == '1-1')
          @include('frontend.pages.project.partials.grid-1')
        @endif
        @if ($grid->layout->key == '1-2' && count($grid->elements) == 3)
          @include('frontend.pages.project.partials.grid-1-2')
        @endif
        @if ($grid->layout->key == '2-1' && count($grid->elements) == 3)
          @include('frontend.pages.project.partials.grid-2-1')
        @endif
        @if ($grid->layout->key == '2-2' && count($grid->elements) == 4)
          @include('frontend.pages.project.partials.grid-2-2')
        @endif
      @endforeach
    </div>
  @endif
  <div class="project-teaser">
    Nächstes Projekt
    <a href="{{ route('page.project', ['slug' => AppHelper::slug($navBrowse['next']->title_short), 'project' => $navBrowse['next']->id]) }}" title="{{$navBrowse['next']->title_short}}, {{$navBrowse['next']->location}}">
      {{$navBrowse['next']->title_short}}, {{$navBrowse['next']->location}}
      @include('frontend.partials.icons.arrow-external-blue')
    </a>
    @if ($navBrowse['next']->teaserImage)
      <figure>
        <a href="{{ route('page.project', ['slug' => AppHelper::slug($navBrowse['next']->title_short), 'project' => $navBrowse['next']->id]) }}" title="{{$navBrowse['next']->title_short}}, {{$navBrowse['next']->location}}">
          <img src="/image/small/{{$navBrowse['next']->teaserImage->name}}" height="400" width="800">
        </a>
      </figure>
    @endif
  </div>
</section>
<div class="overlay-info overlay-info js-info">
  <a href="javascript:;" class="btn-close js-btn-info"></a>
  <div class="project">
    <h1>{{$project->title}}</h1>
    {!! $project->description !!}
    <div class="project__info">
      {!!$project->info !!}
    </div>
  </div>
</div>
@endsection