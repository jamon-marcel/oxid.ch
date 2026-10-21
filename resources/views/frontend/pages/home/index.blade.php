@extends('frontend.layout.home')
@push('head')
<script type="application/ld+json">{!! \App\Support\Seo::organization() !!}</script>
@endpush
@section('content')
<section class="content-home grid-home">
  @if ($image)
    <figure class="span visual-fit is-home">
      <x-image :image="$image" preset="home" />
    </figure>
    
  @endif
  <div class="span news-listing">
    @if ($news)
      @foreach($news as $n)
        <article class="news">
          <header>
            <h2>{{$n->title}}</h2>
          </header>
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