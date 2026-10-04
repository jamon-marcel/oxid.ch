@extends('frontend.layout.pages')
@section('seo_title', 'Profil')
@section('seo_description', 'Team, Profil, Jobs - Yves Schihin, Urs Rinklef')
@section('content')
<section class="content">
  @if (isset($images))
    <a href="javascript:;" class="btn-scroll is-prev" data-imagescroll="prev" hidden></a>
    <a href="javascript:;" class="btn-scroll is-next" data-imagescroll="next"></a>
    <div class="visual-list">
      @foreach($images as $image)
        <figure class="visual-fit" data-imagescroll="item">
          <x-image :image="$image" preset="large" :alt="$image->title" />
        </figure>
      @endforeach
    </div>
  @endif
</section>
<div class="overlay-info" data-overlay="root" data-visible-onload="1">
  <a href="javascript:;" class="btn-close" data-overlay="btn"></a>
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