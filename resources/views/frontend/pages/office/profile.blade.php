@extends('frontend.layout.pages')
@section('seo_title', 'Profil')
@section('seo_description', '')
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
    @if ($profile)
      @if ($profile->title)
        <h1>{{$profile->title}}</h1>
      @endif
      @if ($profile->description)
        {!! $profile->description !!}
      @endif
    @endif
  </div>
</div>
@endsection