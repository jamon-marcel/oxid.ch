@extends('frontend.layout.pages')
@section('seo_title', $discourse->title . ' - Diskurs')
@section('seo_description', '')
@section('content')
<section class="content content--discourse">
  <a href="javascript:;" onclick="history.go(-1)" class="btn-close"></a>
  <div class="swiper-container">
    <div class="swiper-wrapper">
      @if ($discourse->publishedImages)
        @foreach($discourse->publishedImages as $img)
          <div class="swiper-slide">
            <figure class="visual-fit">
              <img src="/image/large/{{$img->name}}" height="400" width="800">
            </figure>
          </div>
        @endforeach
      @endif
    </div>
    @if ($discourse->publishedImages->count() > 1)
      <div class="swiper-btn-prev"></div>
      <div class="swiper-btn-next"></div>
    @endif
  </div>
</section>
<div class="overlay-info overlay-info is-discourse js-info">
  <a href="javascript:;" class="btn-close js-btn-info"></a>
  <div class="discourse-detail">
    <h1>{{$discourse->title}}</h1>
    {!! $discourse->description !!}
    <div class="discourse-detail__info">
      {!!$discourse->info !!}
    </div>
  </div>
</div>
<div class="menu-bar is-pages is-discourse">
  <div class="menu-bar__open js-menu-bar">
    <div class="menu-footer menu-footer--discourse-show">
      <a href="javascript:;" onclick="history.go(-1)" class="btn-close is-sm"></a>
      <h2 class="menu-footer__heading">{{$discourse->heading}}</h2>
      <h1>{{$discourse->title}}</h1>
      <div class="menu-footer__info">
        <a href="javascript:;" class="anchor-ul js-btn-info">Info</a>
      </div>
    </div>
  </div>
</div>
@endsection