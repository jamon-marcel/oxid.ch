@extends('frontend.layout.pages')
@section('seo_title', 'Werkliste')
@section('seo_description', '')
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