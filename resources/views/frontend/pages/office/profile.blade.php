@extends('frontend.layout.pages')
@section('seo_title', 'Profil')
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
    <h1>Büroprofil</h1>
    <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>
    <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>
  </div>
</div>
@endsection