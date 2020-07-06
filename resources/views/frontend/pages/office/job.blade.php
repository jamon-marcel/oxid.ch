@extends('frontend.layout.pages')
@section('seo_title', 'Jobs')
@section('seo_description', 'Team, Profil, Jobs - Yves Schihin, Urs Rinklef')
@section('content')
<section class="content">
  @if (isset($images))
    <a href="javascript:;" class="btn-scroll is-prev js-btn-scroll-prev" style="display:none"></a>
    <a href="javascript:;" class="btn-scroll is-next js-btn-scroll-next"></a>
    <div class="visual-list">
      @foreach($images as $image)
        <figure class="visual-fit js-scroll-item">
          {!! ImageHelper::largeImage($image, $image->title) !!}
        </figure>
      @endforeach
    </div>
  @endif
</section>
<div class="overlay-info js-info" data-visible-onload="1">
  <a href="javascript:;" class="btn-close js-btn-info"></a>
  <div>
    @if (count($jobs) > 0)
      @foreach($jobs as $j)
        <article class="job">
          <h1>{{$j->title}}</h1>
          <div class="job__description">{!! $j->description !!}</div>
          <div class="job__info">{!! $j->info !!}</div>
        </article>
      @endforeach
    @else
      <p>Zur Zeit sind alle unsere Stellen besetzt.</p>
    @endif
  </div>
</div>
@endsection