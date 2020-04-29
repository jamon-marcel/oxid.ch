@extends('frontend.layout.pages')
@section('seo_title', 'Diskurs ' . $pageTitle)
@section('seo_description', '')
@section('content')
<section class="content content--discourse">
  <div class="discourse">
    @include('frontend.pages.discourse.partials.listing')
  </div>
</section>
@endsection