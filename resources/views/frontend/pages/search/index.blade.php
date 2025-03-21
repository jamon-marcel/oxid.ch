@extends('frontend.layout.home')
@section('seo_title', 'Suche')
@section('seo_description', '')
@section('content')
<section class="content-search grid-home">
  @if ($image)
    <figure class="span span-visual visual-fit is-home">
      <img src="/img/home/{{$image->name}}" height="400" width="800">
    </figure>
  @endif
  <div class="span search">
    <form method="GET" action="{{ route('page.search.index') }}">
      <input type="text" name="keyword" placeholder="Suchbegriff" value="{{ $keyword ?? ''}}">
    </form>
    @if ($results)
      <div class="search__results">
        @if ($results['projects'])
          @foreach($results['projects'] as $p)
            <article class="search__result">
              @if ($p->has_detail)
                <span>Projekt</span>
                <a href="{{ route('page.project', ['slug' => AppHelper::slug($p->title_short), 'project' => $p->id]) }}">
                  {{$p->title_short}}
                </a>
                <div class="abstract">
                  {!! \Str::words($p->description, 10, ' ...') !!}
                </div>
              @else
                <span>Projekt - Werkliste</span>
                <a href="{{ route('page.works.authors', ['search' => 1]) }}">
                  {{$p->title_short}}
                </a>
                <div class="abstract">
                  {!! \Str::words($p->description, 10, ' ...') !!}
                </div>
              @endif
            </article>
          @endforeach
        @endif
        @if ($results['discourse'])
          @foreach($results['discourse'] as $d)
            <article class="search__result">
              <span>Diskurs</span>
              <a href="{{ route('page.discourse.detail', ['slug' => AppHelper::slug($d->title), 'discourse' => $d->id]) }}">
                {{$d->title}}
              </a>
              <div class="abstract">
                {!! \Str::words($d->description_short, 8, '...') !!}
              </div>
            </article>
          @endforeach
        @endif
      </div>
    @endif
  </div>
</section>
@endsection