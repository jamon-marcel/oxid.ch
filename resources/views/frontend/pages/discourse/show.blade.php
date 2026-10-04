@extends('frontend.layout.pages')
@section('seo_title', $discourse->title . ' - Diskurs')
@section('seo_description', substr(strip_tags($discourse->description_short),0,255))
@if ($discourse_og)
  @section('og_image', url('/') . $discourse_og->url(1600))
@endif
@section('content')
<section class="content content--discourse">
  <a href="javascript:window.history.back();" class="btn-close" data-swiper="themed"></a>
  <div class="swiper-container">
    <div class="swiper-wrapper">
      @if ($discourse->publishedImages)
        @foreach($discourse->publishedImages as $img)
          <div class="swiper-slide" data-theme="{{$img->theme}}">
            <figure class="visual-fit">
              <x-image :image="$img" preset="large" :alt="$img->title" />
            </figure>
          </div>
        @endforeach
      @endif
    </div>
    @if ($discourse->publishedImages->count() > 1)
      <div class="swiper-btn-prev" data-swiper="themed"></div>
      <div class="swiper-btn-next" data-swiper="themed"></div>
    @endif
  </div>
</section>
<div class="overlay-info is-discourse" data-overlay="root">
  <a href="javascript:;" class="btn-close" data-overlay="btn"></a>
  <div class="discourse-detail">
    <h1>{{$discourse->title}}</h1>
    {!! $discourse->description !!}
    <div class="discourse-detail__info">
      {!!$discourse->info !!}
    </div>
  </div>
</div>
<div class="menu-bar is-pages is-discourse">
  <div class="menu-bar__open" data-menu="bar">
    <div class="menu-footer menu-footer--discourse-show">
      <a href="javascript:window.history.back();" class="btn-close is-sm" data-swiper="themed"></a>
      <h2 class="menu-footer__heading">{{$discourse->heading}}</h2>
      <h1>{{$discourse->title}}</h1>
      <div class="menu-footer__info">
        @if ($discourse->description || $discourse->info)
          <a href="javascript:;" class="anchor-ul" data-overlay="btn">Info</a>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection