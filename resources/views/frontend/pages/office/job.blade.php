@extends('frontend.layout.pages')
@section('seo_title', 'Jobs')
@section('seo_description', '')
@section('content')
<section class="content">
  @if (isset($images))
    <div class="visual-list">
      @foreach($images->shuffle() as $image)
        <figure class="visual-fit">
          <img src="/image/jobs/{{$image->name}}" height="1000" width="1600" alt="{{$image->caption}}">
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