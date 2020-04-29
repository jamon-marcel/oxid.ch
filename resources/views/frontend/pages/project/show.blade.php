@extends('frontend.layout.pages')
@section('seo_title', 'Projekte')
@section('seo_description', '')
@section('content')
<section class="content">
  <div class="project-grids js-project-grids">
    <a href="javascript:;" class="btn-scroll js-btn-scroll"></a>
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
</section>
<div class="overlay-info overlay-info js-info">
  <a href="javascript:;" class="btn-info js-btn-info"></a>
  <div class="project">
    <h1>{{$project->title}}</h1>
    {!! $project->description !!}
    <div class="project__info">
      {!!$project->info !!}
    </div>
  </div>
</div>
@endsection