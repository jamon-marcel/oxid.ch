@extends('frontend.layout.pages')
@section('seo_title', 'Diskurs ' . $pageTitle)
@section('seo_description', 'Veranstaltungen, Publikationen, Recherche - Open House Stadthalle 2020, Swiss Live Perfomance 2020, Modul17')
@section('content')
<section class="content content--discourse-listing">
  <div class="discourse">
    @include('frontend.pages.discourse.partials.listing')
  </div>
</section>
@endsection