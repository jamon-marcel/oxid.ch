@extends('frontend.layout.pages')
@section('seo_title', 'Jobs')
@section('seo_description', '')
@section('content')
<section class="content">
  @if (isset($images))
    <div class="visual-list">
      @foreach($images as $image)
        <figure class="visual-fit">
          <img src="/image/large/{{$image->name}}" height="400" width="800">
        </figure>
      @endforeach
    </div>
  @else
    <div class="visual-list">
      <figure class="visual-fit">
        <img src="/assets/media/dummy.jpg" height="400" width="800">
      </figure>
    </div>
  @endif
</section>
<div class="overlay-info js-info">
  <a href="javascript:;" class="btn-info js-btn-info"></a>
  <div>
    @foreach($jobs as $j)
      <article class="job">
        <h1>{{$j->title}}</h1>
        <div class="job__description">{!! $j->description !!}</div>
        <div class="job__info">{!! $j->info !!}</div>
      </article>
    @endforeach
  </div>
</div>
@endsection