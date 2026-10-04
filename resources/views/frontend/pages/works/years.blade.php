@extends('frontend.layout.pages')
@section('seo_title', 'Werkliste nach Jahr')
@section('seo_description', 'Forsanose, Hochhaus Weberstrasse, Wannenholz, Murgareal, Giesshübel, Sunnige Hof')
@section('content')
<section class="content content--works">
  @if ($projects)
    <div class="works">
      @foreach($projects as $key => $group)
        <div class="collapsible is-expanded" data-collapsible="root">
          <div class="works__heading">
            <h1>
              <a href="javascript:;" class="btn-collapsible" data-collapsible="btn">{{$key}}</a>
            </h1>
          </div>
          <div class="works__grid collapsible__content" data-collapsible="body">
            <div class="works__items" data-filter="group">
              @foreach($group as $p)
                @include('frontend.pages.works.partials.item')
              @endforeach
            </div>
          </div>
        </div>

      @endforeach
   </div>
  @endif
</section>
@endsection