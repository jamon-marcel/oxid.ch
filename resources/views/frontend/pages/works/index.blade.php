@extends('frontend.layout.pages')
@section('seo_title', 'Werkliste')
@section('seo_description', 'Forsanose, Hochhaus Weberstrasse, Wannenholz, Murgareal, Giesshübel, Sunnige Hof')
@section('content')
<section class="content content--works">
  @if ($projects)
    @foreach($projects as $group)
      <div>

      </div>
    @endforeach
  @endif
</section>
@endsection