@extends('frontend.layout.home')
@section('seo_title', 'Home')
@section('seo_description', '')
@section('content')
<section class="content-home grid-2x1">
  <figure class="span visual">
    <img src="/image/large/{{$image->name}}" class="object-fit-cover" height="800" width="400">
  </figure>
  <div class="span news-listing">
    @if ($news)
      @foreach($news as $n)
        <article class="news">
          <h2>{{$n->title}}</h2>
          @if ($n->subtitle)
            <h3>{{$n->subtitle}}</h3>
          @endif
          {!! $n->text !!}
        </article>
      @endforeach
    @endif
  </div>
</section>
@endsection