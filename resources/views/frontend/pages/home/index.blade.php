@extends('frontend.layout.home')
@section('seo_title', 'Home')
@section('seo_description', '')
@section('content')
<section class="content-home grid-home">
  @if ($image)
    <figure class="span visual-fit is-home">
      <img src="/image/home/{{$image->name}}" height="400" width="800">
    </figure>
  @endif
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