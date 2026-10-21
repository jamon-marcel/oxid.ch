@extends('frontend.layout.pages')
@section('seo_title', 'Diskurs ' . $pageTitle)
@section('seo_description', 'Diskurs bei Oxid Architektur: Veranstaltungen, Vorträge, Publikationen und Recherche zu Holzbau, Umnutzung und Stadt.')
@section('content')
<section class="content content--discourse-listing">
  <div class="discourse">
    @include('frontend.pages.discourse.partials.listing')
  </div>
</section>
@endsection