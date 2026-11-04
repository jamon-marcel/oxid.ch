@extends('frontend.layout.pages')
@section('seo_title', 'Profil')
@section('seo_description', 'Profil von Oxid Architektur: Haltung, Themen und Arbeitsweise des Zürcher Büros von Yves Schihin und Urs Rinklef.')
@section('content')
<section class="content">
  @if (isset($images))
    <a href="javascript:;" class="btn-scroll is-prev" data-imagescroll="prev" aria-label="Vorheriges Bild" hidden></a>
    <a href="javascript:;" class="btn-scroll is-next" data-imagescroll="next" aria-label="Nächstes Bild"></a>
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
  <a href="javascript:;" class="btn-close" data-overlay="btn" aria-label="Schliessen"></a>
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