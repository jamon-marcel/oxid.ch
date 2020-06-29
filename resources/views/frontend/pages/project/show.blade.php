@extends('frontend.layout.pages')
@section('seo_title', $project->title . ' - Projekte')
@section('seo_description', '')
@section('content')
<section class="content">
  @if ($project_grid->isNotEmpty())
    <div class="project-grids js-project-grids">
      <a href="javascript:;" class="btn-scroll is-prev js-btn-scroll-up"></a>
      <a href="javascript:;" class="btn-scroll is-next js-btn-scroll-down"></a>
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
  @if ($navBrowse['next'])
    <div class="project-teaser">
      Nächstes Projekt
      <div>
        <a href="{{ route('page.project', ['slug' => AppHelper::slug($navBrowse['next']->title_short), 'project' => $navBrowse['next']->id]) }}" title="{{$navBrowse['next']->title_short}}, {{$navBrowse['next']->location}}">
          {{$navBrowse['next']->title_short}}, {{$navBrowse['next']->location}}
        </a>
      </div>
      @if ($navBrowse['next']->teaserImage)
        <figure>
          <a href="{{ route('page.project', ['slug' => AppHelper::slug($navBrowse['next']->title_short), 'project' => $navBrowse['next']->id]) }}" title="{{$navBrowse['next']->title_short}}, {{$navBrowse['next']->location}}">
            {!! ImageHelper::previewImage($navBrowse['next']->teaserImage, $navBrowse['next']->title_short) !!}
          </a>
        </figure>
      @endif
    </div>
  @endif
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

@if ($project_teasers)
  @include('frontend.pages.project.partials.teasers', ['teasers' => $project_teasers])
@endif

@endsection