@extends('frontend.layout.pages')
@section('seo_title', $project->title . ' - Projekte')
@section('seo_description', $project->description)
@section('canonical', route('page.project', ['project' => $project->id, 'slug' => AppHelper::slug($project->title_short)]))
@if ($project_og)
  @section('og_image', $project_og->ogUrl())
@endif
@section('content')
<section class="content">
  @if ($project_grid->isNotEmpty())
    @php($compositions = $project_grid->map->composition())
    <div class="project-grids" style="--w: {{ \App\Models\Grid::sharedWidth($compositions) }}">
      <a href="javascript:;" class="btn-scroll is-prev" data-project="prev"></a>
      <a href="javascript:;" class="btn-scroll is-next" data-project="next"></a>
      @foreach($project_grid as $i => $grid)
        @include('frontend.pages.project.partials.grid', ['composition' => $compositions[$i]])
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
            <x-image :image="$navBrowse['next']->teaserImage" preset="preview" :alt="$navBrowse['next']->title_short" />
          </a>
        </figure>
      @endif
    </div>
  @endif
</section>
<div class="overlay-info" data-overlay="root">
  <a href="javascript:;" class="btn-close" data-overlay="btn"></a>
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